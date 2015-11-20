<?php

Class WPCmsRelationField Extends WPCmsField {

  function __construct ($config)
  {
    $this->id = WPCmsStatus::getStatus()->getData('pre') . $this->normalize($config['id']);
    $this->inverse = isset($config['inverse']) ? $this->normalize($config['inverse']) : '';
    $this->name = isset($config['name']) ? $config['name'] : '';
    $this->description = isset($config['description']) ? $config['description'] : '';
    $this->default = isset($config['default']) ? $config['default'] : '';
    $this->postTypeOfRelated = isset($config['related']) ? $config['related'] : '';

    return $this;
  }

  var $input_class = 'col-sm-10';

  public function addActionAdminEnqueueScripts ($hook)
  {
    wp_enqueue_script('wpcms-multiselect', WPCMS_STYLESHEET_URI . '/assets/multi.select.js', array('jquery-ui-core', 'jquery', 'jquery-ui-sortable', 'jquery-ui-autocomplete'));
    wp_enqueue_style('wpcms-multiselect', WPCMS_STYLESHEET_URI . '/assets/multi.select.css');
  }

  public function addActionRegister ($slug)
  {
    $this->payload = $slug . '_' . preg_replace("/^wpcms_(.*)/", "$1", $this->id);
    add_action('wp_ajax_' . $this->payload, array($this, 'ajaxCallback'));
  }

  public function ajaxCallback ()
  {
    $args = !empty($query) ? array( 's' => $query, 'post_type' => 'any' ) : array('post_type' => 'any');
    $posts = get_posts(array(
      's' => !empty($_POST['query']) ? $_POST['query'] : '',
      'orderby' => 'post_date',
      'order' => 'DESC',
      'post_status' => 'any',
      'suppress_filters' => 0,
      'post_type' => $this->postTypeOfRelated != '' ? $this->postTypeOfRelated : $post->post_type));
    $results = array();
    foreach($posts as $post) {
      $results[] = array('id' => $post->ID, 'value' => $post->post_title, 'label' => $post->post_title);
    }
    echo json_encode($results);
    wp_die();
  }

  public function renderInnerInput ($post, $data = array())
  {
    if (empty($data['value'])) $data['value'] = $this->default;

    if ($this->postTypeOfRelated == 'page') {
      $posts = get_pages(array(
        'include' => array_map('intval', explode(',', trim($data['value'], ','))),
        'suppress_filters' => 0));
    }
    else {
      $posts = get_posts(array(
        'post_status' => 'any',
        'suppress_filters' => 0,
        'post__in' => array_map('intval', explode(',', trim($data['value'], ','))),
        'post_type' => $this->postTypeOfRelated != '' ? $this->postTypeOfRelated : $post->post_type));
    }

    echo '<div class="form-inline multi-select-field ui-front">';

    echo '<label>Add item to list:</label><input class="form-control input-sm multi-select-filter" size="20" placeholder="', __('start digit...', 'wpcms'),'" />';

    echo '<div class="options-list-sortable" style="width:100%;min-height:50px;max-height:200px;">';

    foreach ($posts as $post) {
      echo '<div class="multi-select-field-item" id="option-sort-', $post->ID, '">', apply_filters('the_title', $post->post_title), '<div class="remove dashicons dashicons-dismiss"></div></div>';
    }

    echo '</div>';

    echo '<input type="hidden" value="', esc_attr($data['value']), '" class="input" id="', $data['id'], '" name="', $data['name'], '" />';

    echo '</div>';

  }

  public function save ($postID, $suffix = '') {

    $field_name = $this->id . $suffix;
    if ($this->inverse) $inverse = WPCmsStatus::getStatus()->getData('pre') . $this->inverse . $suffix;
    else $inverse = $field_name . '__related_as';

    $old = get_post_meta($postID, $field_name, true);
    $new = isset($_POST[$field_name]) ? $_POST[$field_name] : false;


    if ($new && $new != $old) {

      if (get_magic_quotes_gpc()) $new = stripslashes($new);

      update_post_meta($postID, $field_name, $new);

      // Related inverse
      $newValues = explode(',', (string)$new);
      $oldValues = explode(',', (string)$old);

      // Fix revisions...
      if ($post = wp_get_post_revision($postID))
        $postID = $post->post_parent;

      if (is_array($oldValues)) {
        foreach ($oldValues as $key => $relatedID) {
          $relatedOld = get_post_meta($relatedID, $inverse, true);
          $relatedOldValues = explode(',', (string)$relatedOld);

          if (in_array($postID, $relatedOldValues)) {
            foreach (array_keys($relatedOldValues, $postID) as $key) {
              unset($relatedOldValues[$key]);
            }
            update_post_meta($relatedID, $inverse, trim(implode(',', $relatedOldValues), ','));
          }
        }
      }

      if (is_array($newValues)) {
        foreach ($newValues as $key => $relatedID) {
          $relatedNew = get_post_meta($relatedID, $inverse, true);
          $relatedNewValues = explode(',', (string)$relatedNew);

          if (!in_array($postID, $relatedNewValues)) {
            $relatedNewValues[] = $postID;
            update_post_meta($relatedID, $inverse, trim(implode(',', $relatedNewValues), ','));
          }
        }
      }
    }
    elseif ('' == $new && $old) {

      delete_post_meta($postID, $field_name, $old);

      $oldValues = explode(',', (string)$old);

      // Fix revisions...
      if ($post = wp_get_post_revision($postID))
        $postID = $post->post_parent;

      if (is_array($oldValues)) {
        foreach ($oldValues as $key => $relatedID) {
          $relatedOld = get_post_meta($relatedID, $inverse, true);
          $relatedOldValues = explode(',', (string)$relatedOld);

          if (in_array($postID, $relatedOldValues)) {
            foreach (array_keys($relatedOldValues, $postID) as $key) {
              unset($relatedOldValues[$key]);
            }
            update_post_meta($relatedID, $inverse, trim(implode(',', $relatedOldValues), ','));
          }
        }
      }
    }
  }

}