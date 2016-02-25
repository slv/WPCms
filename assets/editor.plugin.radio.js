var WPCmsEditorFields = WPCmsEditorFields || {};

WPCmsEditorFields.radio = {

  init: function ($, $field, inputName, attributes) {
    var options= JSON.parse(attributes.options);
    if (!attributes.options) return;

    $.each(options, function (k, v) {
      var $input = $('<input>', {
        type: 'radio',
        name: inputName,
        value: v.value,
      });

      var $label = $('<label>', {
        text: v.label
      }).prepend($input).appendTo($field);
    });
  },

  set: function ($field, v) {
    $field.find("input").removeAttr('checked');
    if (v)
      $field.find("input[value='" + v + "']").attr('checked', 'checked');
  },

  get: function ($field) {
    return $field.find('input:checked').val();
  }
}