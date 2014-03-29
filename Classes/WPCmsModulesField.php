<?php

Class WPCmsModulesField Extends WPCmsField {

  function __construct ($config)
  {
    $this->id = WPCmsStatus::getStatus()->getData('pre') . $this->normalize($config['id']);
    $this->name = isset($config['name']) ? $config['name'] : '';
    $this->description = isset($config['description']) ? $config['description'] : '';
    $this->default = isset($config['default']) ? $config['default'] : '';
    $this->modules = isset($config['modules']) ? $config['modules'] : array();

    if (is_array($this->modules)) foreach ($this->modules as $module) {
      $module['fields'] = require get_template_directory() . "/Modules/" . $module['type'] . "/admin.php";

      if (is_array($module['fields'])) foreach ($module['fields'] as $field) {
        $field->id = preg_replace("/^" . WPCmsStatus::getStatus()->getData('pre') . "/", "", $field->id);
      }
    }

    return $this;
  }

  public function addActionAdminEnqueueScripts ($hook)
  {
    wp_enqueue_script('wpcms-modules', WPCMS_STYLESHEET_URI . '/WPCms/assets/modules.field.js', array('jquery', 'jquery-ui-core', 'jquery-ui-droppable', 'jquery-ui-sortable'));
    wp_enqueue_style('wpcms-modules', WPCMS_STYLESHEET_URI . '/WPCms/assets/modules.field.css');

    foreach ($this->modules as $module) {
      $fields = require get_template_directory() . "/Modules/" . $module['type'] . "/admin.php";
      foreach ($fields as $field) {
        $field->addActionAdminEnqueueScripts($hook);
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

    echo '<div class="btn btn-success btn-lg btn-block wpcms-modules-field-start-button">Open ', $this->name, '</div>';
    echo '<div class="modules-field" id="', $data['id'], '">';


    echo '<div class="modules-list-droppable" id="', $data['id'], '_droppable">';


    if (is_array($data['value'])) {
      foreach ($data['value'] as $order => $module_data) {
        if (!isset($modules_cache[$module_data['widget_type']])) continue;

        $module = $modules_cache[$module_data['widget_type']];

        if (file_exists(get_template_directory() . "/Modules/" . $module['type'] . "/screenshot.png"))
          $name = '<div class="wpcms-modules-field-preview"><img src="' . WPCMS_STYLESHEET_URI . '/Modules/' . $module['type'] . '/screenshot.png" /></div>';
        else
          $name = '<span>' . $module['name'] . '</span>';

        echo '<div class="module">
          <a>', $name, '</a>
          <div class="module-inside"><h3>', $module['name'], '</h3><div class="form">
          <input type="hidden" id="', $data['id'], '____[widget_type]" value="', $module['type'], '" />';

        $module['fields'] = require get_template_directory() . "/Modules/" . $module['type'] . "/admin.php";
        foreach ($module['fields'] as $field) {
          $field_data = array(
            'id' => isset($field->id) ? $data['id'] . '____[' . $field->id . ']' : '',
            'name' => isset($field->id) ? $field->id : '',
            'value' => isset($module_data[$field->id]) ? $module_data[$field->id] : ''
          );

          $field->render($post, $field_data);
        }



        echo '</div></div>
          <div class="wpcms-modules-field-buttons">
            <div class="module-toggle btn btn-default btn-xs">edit</div>
            <div class="module-remove btn btn-danger btn-xs">remove</div>
          </div>
        </div>';

      }
    }

    echo '</div>';
    echo '<hr />';
    echo '<div class="modules-list" id="', $data['id'], '_wrapper">';
    echo '<div class="wpcms-modules-field-save-button-wrapper">
      <div class="btn btn-danger btn-sm btn-block wpcms-modules-field-close-button">Close  ', $this->name, '</div>
      <div class="btn btn-info btn-sm btn-block wpcms-modules-field-toggle-button">Toggle All Modules</div>
    </div>';


    foreach ($this->modules as $module) {
      if (file_exists(get_template_directory() . "/Modules/" . $module['type'] . "/screenshot.png"))
        $name = '<div class="wpcms-modules-field-preview"><img src="' . WPCMS_STYLESHEET_URI . '/Modules/' . $module['type'] . '/screenshot.png" /></div>';
      else
        $name = '<span>' . $module['name'] . '</span>';

      echo '<div class="module"><div class="module-inside"><h3>', $module['name'], '</h3><div class="form">
        <input type="hidden" id="', $data['id'], '____[widget_type]" value="', $module['type'], '" />';

        $module['fields'] = require get_template_directory() . "/Modules/" . $module['type'] . "/admin.php";
        foreach ($module['fields'] as $field) {
          $field_data = array(
            'id' => isset($field->id) ? $data['id'] . '____[' . $field->id . ']' : '',
            'name' => isset($field->id) ? $field->id : '',
            'value' => isset($field->default) ? $field->default : ''
          );

          $field->render($post, $field_data);

        }

      echo '</div></div>
        <a>', $name, '</a>
        <div class="wpcms-modules-field-buttons">
          <div class="module-toggle btn btn-default btn-xs">edit</div>
          <div class="module-remove btn btn-danger btn-xs">remove</div>
        </div>
      </div>';
    }

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
