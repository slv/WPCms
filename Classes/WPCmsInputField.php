<?php

Class WPCmsInputField Extends WPCmsField {

  public function renderInnerInput ($post, $data = array()) {
    echo '<input class="form-control input-sm" type="text" name="', $data['name'], '" id="', $data['id'], '" value="', esc_attr($data['value']), '" size="30" />';
  }

}