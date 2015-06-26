<?php
/*

new WPCmsMultiListField(array(
  'id' => 'demo-field',
  'name' => 'This is A demo field',
  'description' => 'Insert your variants',
  'fields_list' => array('Description', 'Price', 'Link')
))

*/

Class WPCmsMultiListField Extends WPCmsField {

  function __construct ($config) {

    $this->id = WPCmsStatus::getStatus()->getData('pre') . $this->normalize($config['id']);
    $this->name = isset($config['name']) ? $config['name'] : '';
    $this->description = isset($config['description']) ? $config['description'] : '';
    $this->fieldsList = isset($config['fields_list']) ? $config['fields_list'] : '';

    return $this;
  }

  public function addActionAdminEnqueueScripts ($hook)
  {
    wp_enqueue_script('wpcms-list', WPCMS_STYLESHEET_URI . '/assets/list.js', array('jquery', 'jquery-ui-sortable'));
  }

  public function renderInnerInput ($post, $data = array()) {

    $fieldWidth = 100/count($this->fieldsList) . '%';

    echo '<div class="wpcms-list-labels">';

    foreach ($this->fieldsList as $order => $label) {
      echo '<label class="wpcms-label" style="width:', $fieldWidth, '">', $label, '</label>';
    }

    echo '</div>';

    echo '<div class="wpcms-list-items-container">';

    if (empty($data['value']) || !is_array($data['value']) || !count($data['value'])) {

      echo '<div class="form-control">';

      foreach ($this->fieldsList as $order => $label) {
        echo '<input class="wpcms-input"  style="width:', $fieldWidth, '" type="text" id="', $data['id'], '[][', $order, ']" name="', $data['name'], '[][', $order, ']" value="" size="30" />';
      }

      echo '<span class="reorder-handle dashicons dashicons-menu"></span>';

      echo '</div>';

    } else {

      foreach ($data['value'] as $key => $value) {
        echo '<div class="form-control">';

        foreach ($this->fieldsList as $order => $label) {
          echo '<input class="wpcms-input"  style="width:', $fieldWidth, '" type="text" id="', $data['id'], '[][', $order, ']" name="', $data['name'], '[][', $order, ']" value="', esc_attr($value[$order]), '" size="30" />';

        }

        echo '<span class="reorder-handle dashicons dashicons-menu"></span>';

        echo '</div>';

      }

    }

    echo '</div>';

    echo '<button style="margin:5px 0;" class="wpcms-list-field-button button button-primary">Add new</button>';
  }

  public function save_filter ($value) {
    if (empty($value)) return '';

    $out = array();

    foreach ($value as $k => $item) {
      foreach ($this->fieldsList as $order => $label) {
        if (!empty($item[$order])) {
          $out[] = $item;
          break;
        }
      }
    }

    if (!count($out)) return '';

    return $out;
  }

}