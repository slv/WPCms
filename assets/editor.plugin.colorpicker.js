var WPCmsEditorFields = WPCmsEditorFields || {};

WPCmsEditorFields.colorpicker = {

  init: function ($, $field, inputName) {
    var $input = $('<input>', {
      type: 'text',
      name: inputName
    }).appendTo($field).wpColorPicker();
  },

  set: function ($field, v) {
    $field.find('input.wp-color-picker').wpColorPicker('color', v);
  },

  get: function ($field) {
    return $field.find('input.wp-color-picker').val();
  }
}