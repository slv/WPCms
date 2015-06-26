<?php

Class WPCmsSEOReplacePostTypeWithTaxonomy {

  function __construct ($post_type, $taxonomy, $keep_post_type_prefix = false) {
    $this->post_type = $post_type;
    $this->taxonomy = $taxonomy;
    $this->keep_post_type_prefix = $keep_post_type_prefix;

    add_filter('post_link', array($this, 'filter_post_permalink'), 20, 3);
    add_filter('post_type_link', array($this, 'filter_post_permalink'), 20, 3);
    add_filter('term_link', array($this, 'filter_term_permalink'), 20, 3);

    add_action('init', array($this, 'add_rewrite_rules'));
  }

  function get_term_hierarchy ($terms, $parent, $hierarchy = '') {
    foreach($terms as $term) {
      if (intval($term->parent) === intval($parent)) {
        $hierarchy .= '/' . $term->slug;
        return $this->get_term_hierarchy($terms, $term->term_id, $hierarchy);
      }
    }

    return $hierarchy;
  }

  function filter_post_permalink ($permalink, $post, $leavename) {
    $terms = wp_get_post_terms($post->ID, $this->taxonomy);
    if (!is_wp_error($terms) && !empty($terms) && is_object($terms[0])) {
      $terms_string = $this->get_term_hierarchy($terms, 0);
      if (empty($terms_string)) {
        $term = $terms[0];
        $terms_string = $this->get_term_hierarchy($terms, $term->term_id, '/' . $this->post_type);
      }

      return str_replace(
        '/' . $this->post_type . '/',
        ($this->keep_post_type_prefix ? '/' . $this->post_type : '') . $terms_string . '/',
        $permalink
      );
    }

    return $permalink;
  }

  function filter_term_permalink ($permalink, $term, $taxonomy) {
    if (intval($term->parent) === 0) {
      return str_replace(
        '/' . $this->taxonomy . '/',
        '/',
        $permalink
      );
    }

    return $permalink;
  }

  function add_rewrite_rules ()
  {
    add_rewrite_rule('^' . $this->post_type . '/(.*)/([^\/]+)/?$', 'index.php?post_type=' . $this->post_type . '&' . $this->post_type . '=$matches[2]', 'top');
    $terms = get_terms(array($this->taxonomy), array('parent' => 0));
    foreach ($terms as $term) {
      add_rewrite_rule('^' . $term->slug . '/?$', 'index.php?taxonomy=' . $this->taxonomy . '&term=' . $term->slug, 'top');
      add_rewrite_rule('^' . ($this->keep_post_type_prefix ? $this->post_type . '/' : '') . $term->slug . '/([^\/]+)/?$', 'index.php?post_type=' . $this->post_type . '&' . $this->post_type . '=$matches[1]', 'top');
      add_rewrite_rule('^' . ($this->keep_post_type_prefix ? $this->post_type . '/' : '') . $term->slug . '/(.*)/([^\/]+)/?$', 'index.php?post_type=' . $this->post_type . '&' . $this->post_type . '=$matches[2]', 'top');
    }
  }
}
