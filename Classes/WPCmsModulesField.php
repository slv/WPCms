<?php

Class WPCmsModulesField Extends WPCmsField {

  function __construct ($config)
  {
    $this->id = WPCmsStatus::getStatus()->getData('pre') . $this->normalize($config['id']);
    $this->name = isset($config['name']) ? $config['name'] : '';
    $this->description = isset($config['description']) ? $config['description'] : '';
    $this->background = isset($config['background']) ? $config['background'] : '';
    $this->default = isset($config['default']) ? $config['default'] : '';
    $this->modules = isset($config['modules']) ? $config['modules'] : array();

    if (is_array($this->modules)) foreach ($this->modules as $key => $module) {
      $this->modules[$key]['fields'] = require get_template_directory() . "/Modules/" . $module['type'] . "/admin.php";

      if (!empty($module['fields'])) foreach ($module['fields'] as $field) {
        $field->id = preg_replace("/^" . WPCmsStatus::getStatus()->getData('pre') . "/", "", $field->id);
      }
    }

    return $this;
  }

  public function addActionAdminEnqueueScripts ($hook)
  {
    wp_enqueue_script('wpcms-modules', WPCMS_STYLESHEET_URI . '/assets/modules.field.js', array('jquery', 'jquery-ui-core', 'jquery-ui-droppable', 'jquery-ui-sortable'));
    wp_enqueue_style('wpcms-modules', WPCMS_STYLESHEET_URI . '/assets/modules.field.css');

    foreach ($this->modules as $module) {
      if (!empty($module['fields'])) foreach ($module['fields'] as $field) {
        $field->addActionAdminEnqueueScripts($hook);
      }
    }

  }

  public function addActionRegister ($slug)
  {
    foreach ($this->modules as $module) {
      if (!empty($module['fields'])) foreach ($module['fields'] as $field) {
        $field->addActionRegister($slug);
      }
    }

  }

  public function renderLabel ($post) {
  }

  public function renderInput ($post, $data = array()) {
    $data = array(
      'id' => isset($data['id']) ? $data['id'] : $this->id,
      'name' => isset($data['name']) ? $data['name'] : $this->id,
      'value' => isset($data['value']) ? $data['value'] : $this->value($post->ID)
    );
    $this->renderInnerInput($post, $data);
  }

  public function renderInnerInput ($post, $data = array())
  {
    $modules_cache = array();
    foreach ($this->modules as $module) {
      $modules_cache[$module['type']] = $module;
    }

    echo '<div class="modules-field" id="', $data['id'], '">';
    echo '<div class="modules-list-buttons"><div class="wpcms-modules-field-toggle-button"><span class="dashicons dashicons-exerpt-view"></span> Open/Close All Modules</div></div>';

    echo '<div class="modules-list-droppable" id="', $data['id'], '_droppable"', ($this->background ? ' style="background:' . $this->background . ';"' : ''), '>';


    if (is_array($data['value'])) {
      foreach ($data['value'] as $order => $module_data) {
        if (!isset($modules_cache[$module_data['widget_type']])) continue;

        $module = $modules_cache[$module_data['widget_type']];

        if (file_exists(get_template_directory() . "/Modules/" . $module['type'] . "/screenshot.png"))
          $name = '<div class="wpcms-modules-field-preview"><img width="120" src="' . get_stylesheet_directory_uri() . '/Modules/' . $module['type'] . '/screenshot.png" /></div>';
        else
          $name = '<span>' . $module['name'] . '</span>';

        echo '<div class="module">
          <a>', $name, '</a>
          <div class="module-inside"><h3>', $module['name'], '</h3><div class="form">
          <input type="hidden" id="', $data['id'], '____[widget_type]" value="', $module['type'], '" />';

        if (!empty($module['fields'])) foreach ($module['fields'] as $field) {
          $field_data = array(
            'id' => isset($field->id) ? $data['id'] . '____[' . $field->id . ']' : '',
            'name' => isset($field->id) ? $field->id : '',
            'value' => isset($module_data[$field->id]) ? $module_data[$field->id] : ''
          );

          $field->render($post, $field_data);
        }



        echo '</div></div>
          <div class="wpcms-modules-field-buttons">
            <div class="module-add dashicons dashicons-plus-alt"></div>
            <div class="module-toggle dashicons dashicons-welcome-write-blog"></div>
            <div class="module-remove dashicons dashicons-dismiss"></div>
          </div>
        </div>';

      }
    }

    echo '</div>';
    echo '<div class="modules-list-buttons"><div class="wpcms-modules-field-add-button"><span class="dashicons dashicons-plus-alt"></span> Add Module</div></div>';
    echo '<div class="modules-list" id="', $data['id'], '_wrapper"', ($this->background ? ' style="background:' . $this->background . ';"' : ''), '><div>';
    echo '<div class="modules-list-buttons"><div class="wpcms-modules-field-add-button"><span class="dashicons dashicons-dismiss"></span> Close</div></div>';

    $current_module_category = '';
    foreach ($this->modules as $module) {
      if (file_exists(get_template_directory() . "/Modules/" . $module['type'] . "/screenshot.png"))
        $name = '<div class="wpcms-modules-field-preview"><img width="120" src="' . get_stylesheet_directory_uri() . '/Modules/' . $module['type'] . '/screenshot.png" /></div>';
      else
        $name = '<span>' . $module['name'] . '</span>';

      if (isset($module['category']) && $module['category'] != $current_module_category) {
        $current_module_category = $module['category'];
        echo '<div class="title-module"><h3>', $current_module_category, '</h3></div>';
      }

      echo '<div class="module">
        <a>', $name, '</a>
        <div class="module-inside"><h3>', $module['name'], '</h3><div class="form">
        <input type="hidden" id="', $data['id'], '____[widget_type]" value="', $module['type'], '" />';

        if (!empty($module['fields'])) foreach ($module['fields'] as $field) {
          $field_data = array(
            'id' => isset($field->id) ? $data['id'] . '____[' . $field->id . ']' : '',
            'name' => isset($field->id) ? $field->id : '',
            'value' => isset($field->default) ? $field->default : ''
          );

          $field->render($post, $field_data);

        }

      echo '</div></div>
        <div class="wpcms-modules-field-buttons">
          <div class="module-add dashicons dashicons-plus-alt"></div>
          <div class="module-toggle dashicons dashicons-welcome-write-blog"></div>
          <div class="module-remove dashicons dashicons-dismiss"></div>
        </div>
      </div>';
    }

    echo '</div>';
    echo '</div>';
    echo '</div>';

  }

  public function renderSettingLabel () {
  }

  public function renderSettingInput () {
    $this->renderInnerInput(null, array(
      'id' => $this->id,
      'name' => $this->id,
      'value' => $this->settingValue()
    ));
  }

}
