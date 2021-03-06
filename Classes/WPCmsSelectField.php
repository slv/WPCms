<?php

Class WPCmsSelectField Extends WPCmsField {

  function __construct ($config) {
    $this->id = WPCmsStatus::getStatus()->getData('pre') . $this->normalize($config['id']);
    $this->name = isset($config['name']) ? $config['name'] : '';
    $this->description = isset($config['description']) ? $config['description'] : '';
    $this->options = isset($config['options']) ? $config['options'] : array();
    $this->default = isset($config['default']) ? $config['default'] : '';

    return $this;
  }

  var $settings_input_class = 'col-sm-4';
  var $posttype_input_class = 'col-sm-4';

  public function renderInnerInput ($post, $data = array()) {
    echo '<select class="form-control" type="text" name="', $data['name'], '" id="', $data['id'], '">';

    if (empty($data['value']) && !isset($this->options[$default]))
      echo '<option value="">', __('Select', 'wpcms'),'...</option>';

    foreach ($this->options as $value => $label) {
      $selected = ($value == $data['value'] ? ' selected="selected"' : '');

      echo '<option ', $selected,' value="', esc_attr($value), '">', htmlentities($label), '</option>';
    }

    echo '</select>';
  }

}