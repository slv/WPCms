<?php

Class WPCmsDatePickerField Extends WPCmsField {
  public function addActionAdminEnqueueScripts ($hook)
  {
    wp_enqueue_style('jquery.datetimepicker', WPCMS_STYLESHEET_URI . '/assets/jquery.datetimepicker.css');
    wp_enqueue_script('datetimepicker', WPCMS_STYLESHEET_URI . '/assets/jquery.datetimepicker.js', array('jquery'));
    wp_enqueue_script('wpcms-datepicker', WPCMS_STYLESHEET_URI . '/assets/datepicker.js', array('jquery'));
  }

  public function renderInnerInput ($post, $data = array())
  {
    echo '<input class="form-control input-sm" type="text" name="', $data['name'], '" id="', $data['id'], '" value="', esc_attr($data['value']), '" size="30" />';
  }

}