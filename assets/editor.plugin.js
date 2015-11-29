jQuery(function($) {

  var fields = {

    text: {

      init: function ($field, inputName) {
        var text = $('<input>', {
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
  }

  tinymce.create('tinymce.plugins.Plugin_WPCmsEditor', {

    init: function (ed, url) {

      $('.wpcms-editor-plugin-item').each(function () {
        var $form = $(this),
            $fields = $form.find('.wpcms-editor-plugin-input'),
            shortcodeName = $form.attr('data-shortcode');

        if (!shortcodeName) return;

        $fields.each(function () {
          var inputType = $(this).attr('data-input-type'),
              inputName = $(this).attr('data-input-name');

          if (!fields[inputType]) return;

          fields[inputType].init($(this), inputName);
        });

        $('<button>', {
          'class': 'secondary-btn',
          text: 'Insert',
          on: {
            click: function (e) {
              e.preventDefault();

              var values = {};

              $fields.each(function () {
                var inputType = $(this).attr('data-input-type'),
                    inputName = $(this).attr('data-input-name');

                if (!fields[inputType]) return;

                values[inputName] = fields[inputType].get($(this));
              });

              var pieces = ['[', shortcodeName];

              $.each(values, function (name, value) {
                pieces.push(' ', name, '="', value, '"');
              });

              pieces.push(']');

              var content = ed.selection.getContent();

              if (content)
                pieces.push(content);

              pieces.push('[/', shortcodeName, ']');

              ed.execCommand('mceInsertContent', 0, pieces.join(''));
            }
          }
        }).appendTo($form);

        ed.addButton(shortcodeName, {
          title: shortcodeName,
          cmd: shortcodeName,
          image: url + '/icon.png'
        });

        ed.addCommand(shortcodeName, function () {
          tb_show('Shortcode: ' + shortcodeName, '#TB_inline?inlineId=wpcms-editor-plugin-' + shortcodeName);
        });

      });
    }

  });

  tinymce.PluginManager.add('WPCmsEditor', tinymce.plugins.Plugin_WPCmsEditor);
});