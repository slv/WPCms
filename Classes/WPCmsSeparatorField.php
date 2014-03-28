<?php

Class WPCmsSeparatorField Extends WPCmsField {

  public function renderInnerInput ($post, $data = array()) {
    echo '<h3>' . $this->name . '</h3>';
    if ($this->description != '') {
      echo '<p>' . $this->description . '</d>';
    }
  }

  public function render ($post, $data = array()) {
    echo '<div class="wpcms-field ', $this->hyphenizeFromCamelCase(get_class($this)), ' ', $this->id,'-wrapper">';

    $this->renderInnerInput(null);

    echo '</div>';
  }

//
// Get The Value
//

  public function value ($postID, $suffix = '') {}
  public function save ($postID, $suffix = '') {}
  public function handleRevision ($postID, $suffix = '') {}
  public function handleRestoreRevision ($postID, $revisionID, $suffix = '') {}

  public function revisionFields ($fields, $suffix = '') {
    return $fields;
  }

  public function addRevisionFilter ($suffix = '') {}
  public function thisRevisionField ($value, $field, $metadataType = 'default') {}

//
// Settings Page
//

  public function settingValue ($suffix = '') {}
  public function registerSettingInOptionsGroup ($optionsGroup, $suffix = '') {}

  public function sanitizeSetting ($data) {
    return $data;
  }

  public function renderSetting () {

    $this->willRenderSetting();
    $this->renderInnerInput(null);
    $this->didRenderSetting();
  }

  public function willRenderSetting () {
    echo '<div class="wpcms-field ', $this->hyphenizeFromCamelCase(get_class($this)), ' ', $this->id,'-wrapper">';
  }

  public function renderSettingLabel () {}
  public function renderSettingInput () {}

  public function didRenderSetting () {
    echo '</div>';
  }

};