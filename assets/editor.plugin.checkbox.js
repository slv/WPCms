var WPCmsEditorFields = WPCmsEditorFields || {};

WPCmsEditorFields.checkbox = {

  init: function ($, $field, inputName) {
    var $input = $('<input>', {
      type: 'checkbox',
      name: inputName
    }).appendTo($field);
  },

  set: function ($field, v) {
    if (v == 'on')
      $field.find('input').prop('checked', 'checked');
  },

  get: function ($field) {
    return $field.find('input').is(':checked') ? 'on' : '';
  }
};