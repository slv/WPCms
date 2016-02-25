var WPCmsEditorFields = WPCmsEditorFields || {};

WPCmsEditorFields.text = {

  init: function ($, $field, inputName) {
    var $input = $('<input>', {
      type: 'text',
      name: inputName
    }).appendTo($field);
  },

  set: function ($field, v) {
    $field.find('input').val(v);
  },

  get: function ($field) {
    return $field.find('input').val();
  }
}