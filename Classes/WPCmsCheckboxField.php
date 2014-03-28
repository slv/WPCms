<?php

Class WPCmsCheckboxField Extends WPCmsField {

  public function renderSettingLabel () {
    echo '<label for="', $this->id, '" class="col-sm-2 control-label">', __($this->name, WPCmsStatus::getStatus()->getData('textdomain')), '</label>';
  }

  public function renderInnerInput ($post, $data = array()) {
    echo '<div class="checkbox"><label><input type="checkbox" name="', $data['name'], '" id="', $data['id'], '" value="on"', ($data['value'] == "on" ? ' checked="checked" ' : ''), ' />',
      ($this->description ? $this->description : ''),
    '</label></div>';
  }

  public function save ($postID, $suffix = '') {

    $field_name = $this->id . $suffix;

    $old = get_post_meta($postID, $field_name, true);
    $new = isset($_POST[$field_name]) && $_POST[$field_name] == "on";

    if ($new) {

      update_post_meta($postID, $field_name, "on");
    }
    elseif ($old) {

      delete_post_meta($postID, $field_name, $old);
    }
  }
}