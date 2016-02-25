var WPCmsEditorFields = WPCmsEditorFields || {};

WPCmsEditorFields.select = {

  init: function ($, $field, inputName, attributes) {
    var options= JSON.parse(attributes.options);
    if (!attributes.options) return;

    var $select = $('<select>', {
      name: inputName
    }).appendTo($field);

    $.each(options, function (k, option) {
      $('<option>', {
        value: option.value
      }).text(option.label).appendTo($select);
    });
  },

  set: function ($field, v) {
    $field.find("select").val(v);
  },

  get: function ($field) {
    return $field.find('select').val();
  }
}