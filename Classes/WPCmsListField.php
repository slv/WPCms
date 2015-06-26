<?php

Class WPCmsListField Extends WPCmsField {

  public function addActionAdminEnqueueScripts ($hook)
  {
    wp_enqueue_script('wpcms-list', WPCMS_STYLESHEET_URI . '/assets/list.js', array('jquery', 'jquery-ui-sortable'));
  }

  public function renderInnerInput ($post, $data = array()) {

    echo '<div class="wpcms-list-items-container">';

    if (empty($data['value']) || !is_array($data['value']) || !count($data['value'])) {

      echo '<div class="form-control"><input type="text" id="', $data['id'], '[]" name="', $data['name'], '[]" value="" size="30" /><span class="reorder-handle dashicons dashicons-menu"></span></div>';

    } else {

      foreach ($data['value'] as $key => $value) {
        echo '<div class="form-control"><input type="text" id="', $data['id'], '[]" name="', $data['name'], '[]" value="', esc_attr($value), '" size="30" /><span class="reorder-handle dashicons dashicons-menu"></span></div>';
      }

    }

    echo '</div>';

    echo '<button style="margin:5px 0;" class="', $this->hyphenizeFromCamelCase(get_class($this)), '-button button button-primary">Add new</button>';
  }

}