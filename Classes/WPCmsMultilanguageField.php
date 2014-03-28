<?php

Class WPCmsMultilanguageField {

  function __construct ($field, $languages = null) {
    $this->field = $field;

    if (is_null($languages))
      $languages = WPCmsStatus::getStatus()->getData('languages');
    if (is_null($languages) || !is_array($languages))
      die('Cannot Instantiate ' . __CLASS__ . ' without setting languages in constructor or in WPCmsStatus...');

    $languages = array_map(array($this, "normalize"), $languages);

    $this->languages = $languages;

    $this->id = $this->field->id;
    $this->name = $this->field->name;
    $this->description = $this->field->description;

    return $this;
  }

  var $settings_input_class = 'col-sm-9';
  var $posttype_input_class = 'col-sm-9';

  public function normalize ($str) {
    return preg_replace(array("/(\s+)/", "/([^a-zA-Z0-9_]*)/", "/(_+)/"), array("_", "", "_"), $str);
  }

  public function addActionAdminEnqueueScripts ($hook) {
    wp_enqueue_script('wpcms-multilanguage', WPCMS_STYLESHEET_URI . '/WPCms/assets/multilanguage.js', array('jquery'));
    wp_enqueue_style('wpcms-multilanguage', WPCMS_STYLESHEET_URI . '/WPCms/assets/multilanguage.css');
    $this->field->addActionAdminEnqueueScripts($hook);
  }

  public function render ($post, $data = array()) {
    if (isset($data['value']) && is_array($data['value']))
      $meta = $data['value'];
    else
      $meta = $this->value($post->ID);

    $this->field->willRender($post);
    $this->field->renderLabel($post);

    echo '<div class="wpcms-multilingual-field ', $this->posttype_input_class, '"><div>',
        '<div class="wpcms-multilingual-field-switcher">';

    foreach ($this->languages as $k => $lang) {
      echo '<a class="multilingual-switcher ord-', $k, ' lang-', $lang, ' btn btn-xs btn-default">', $lang, '</a>';
    }

    echo '</div>';


    foreach ($this->languages as $k => $lang) {

      $field_data = array(
        'id' => $this->field->id . '[' . $lang . ']',
        'name' => $this->field->id . '[' . $lang . ']',
        'value' => $meta[$lang]
      );
      if (isset($data['id']))
        $field_data['id'] = str_replace("[" . $data['name'] . "]", "[" . $data['name'] . "][" . $lang . "]", $data['id']);

      echo '<div class="multilingual-wrapper ord-', $k, ' lang-', $lang, '">';

      $this->field->renderInnerInput($post, $field_data);

      echo '</div>';
    }

    echo '</div></div>';

    $this->field->didRender($post);
  }

  public function value ($postID) {
    $out = $this->field->value($postID);

    if (!is_array($out))
      $out = array();

    foreach ($this->languages as $lang) {

      $out[$lang] = isset($out[$lang]) ? $out[$lang] : '';
    }
    return $out;
  }

  public function save ($postID) {
    $this->field->save($postID);
  }

  public function handleRevision ($postID) {
    $this->field->handleRevision($postID);
  }

  public function handleRestoreRevision ($postID, $revisionID, $suffix = '') {
    $this->field->handleRestoreRevision($postID, $revisionID);
  }

  public function revisionFields ($fields) {

    foreach ($this->languages as $lang) {

      $fields[$this->id][$lang] = $this->name . " [$lang]";
    }
    return $fields;
  }

  public function addRevisionFilter ($suffix = '') {
    $this->field->addRevisionFilter($suffix);
  }

//
// Settings Page
//

  public function settingValue ($suffix = '') {
    $out = $this->field->settingValue($suffix);

    if (!is_array($out))
      $out = array();

    foreach ($this->languages as $lang) {

      $out[$lang] = isset($out[$lang]) ? $out[$lang] : '';
    }
    return $out;
  }

  public function registerSettingInOptionsGroup ($optionsGroup) {
    $this->field->registerSettingInOptionsGroup ($optionsGroup);
  }

  public function renderSetting () {

    $option = $this->settingValue();

    $this->field->willRenderSetting();
    $this->field->renderSettingLabel();

    echo '<div class="wpcms-multilingual-field ', $this->settings_input_class, '"><div>',
        '<div class="wpcms-multilingual-field-switcher">';

    foreach ($this->languages as $k => $lang) {
      echo '<a class="multilingual-switcher ord-', $k, ' lang-', $lang, ' btn btn-xs btn-default">', $lang, '</a>';
    }

    echo '</div>';


    foreach ($this->languages as $k => $lang) {

      $data = array(
        'id' => $this->field->id . '[' . $lang . ']',
        'name' => $this->field->id . '[' . $lang . ']',
        'value' => $option[$lang]
      );

      echo '<div class="multilingual-wrapper ord-', $k, ' lang-', $lang, '">';

      $this->field->renderInnerInput(null, $data);

      echo '</div>';
    }

    echo '</div></div>';

    $this->field->didRenderSetting();
  }
}