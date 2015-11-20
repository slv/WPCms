<?php

Class WPCmsDynamicListField Extends WPCmsField {

  public function addActionAdminEnqueueScripts ($hook)
  {
    wp_enqueue_script('wpcms-list', WPCMS_STYLESHEET_URI . '/assets/list.js', array('jquery', 'jquery-ui-sortable'));
  }

  public function addActionRegister ($slug) {

    $this->list_post_type = $slug . '_' . preg_replace("/^wpcms_(.*)/", "$1", $this->id);

    if (strlen($this->list_post_type) > 20) wp_die('WPCmsDynamicListField doesn\'t accept id: ' . $this->list_post_type . ' because is > 20 chars', 'WPCmsDynamicListField Error');

    $listPostType = new WPCmsPostType(array(
      'post_type' => $this->list_post_type,
      'fields' => array(
        'details-box' => array(
          'title' => $this->name . ' Fields',
          'fields' => array(
            new WPCmsListField (array(
              'id' => $this->list_post_type . '_fields',
              'name' => $this->name . ' Fields',
              'description' => ''))
          )
        )
      )
    ));

    $listPostType->setLabels(array(
        'name' => $this->name,
        'singular_name' => $this->name,
        'menu_name' => $this->name . ' List'
    ));

    $listPostType->setArgs(array(
      'menu_icon' => 'dashicons-images-alt',
      'show_in_menu' => 'edit.php?post_type=' . $slug,
      'supports' => array('title')
    ));

    $listPostType->register();

    add_action('wp_ajax_' . $this->list_post_type, array($this, 'ajaxCallback'));

    $this->payload = $this->list_post_type;
  }

  public function ajaxCallback ()
  {
    if (empty($_POST['fields_id'])) wp_die();

    $listFields = _m($this->list_post_type . '_fields', $_POST['fields_id']);

    if ($listFields) {
      foreach ($listFields as $key => $value) {
        echo '<div class="wpcms-fields-group"><label>', $value, '</label>',
              '<textarea type="text" name="', $data['name'], '[fields][]" size="30"></textarea>',
              '<span class="reorder-handle dashicons dashicons-menu"></span>',
            '</div>';
      }
    }

    wp_die();
  }

  public function renderInnerInput ($post, $data = array()) {

    $posts = get_posts(array(
      'numberposts' => -1,
      'orderby' => 'post_date',
      'order' => 'DESC',
      'post_status' => 'any',
      'suppress_filters' => 0,
      'post_type' => $this->list_post_type));

    echo '<select class="form-control" type="text" name="', $data['name'], '[type]" id="', $data['id'], '">';

    if (empty($data['value']) || empty($data['value']['type'])) {
      $data['value'] = array('type' => '', 'fields' => array());
    }

    echo '<option value="">', __('Select', 'wpcms'), '...</option>';

    $listFields = false;

    foreach ($posts as $post) {
      $value = $post->ID;
      $selected = ($value == $data['value']['type'] ? ' selected="selected"' : '');

      if ($selected) $listFields = _m($this->list_post_type . '_fields', $post->ID);

      echo '<option ', $selected,' value="', esc_attr($value), '">', htmlentities($post->post_title), '</option>';
    }

    echo '</select>';

    echo '<div class="wpcms-dynamic-list-items-container">';

    if ($listFields) {
      foreach ($listFields as $key => $value) {
        echo '<div class="wpcms-fields-group"><label>', $value, '</label>',
              '<textarea type="text" name="', $data['name'], '[fields][]" size="30">',
                (!empty($data['value']) && !empty($data['value']['fields']) && !empty($data['value']['fields'][$key]) ? $data['value']['fields'][$key] : ''),
              '</textarea>',
              '<span class="reorder-handle dashicons dashicons-menu"></span>',
            '</div>';
      }
    }

    echo '</div>';

  }

}