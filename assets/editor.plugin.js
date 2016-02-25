var WPCmsEditorFields = WPCmsEditorFields || {};

jQuery(function($) {

  function buildShortcode (atts, content, tag) {
    var pieces = ['[', tag];

    delete atts.innercontent;

    $.each(atts, function (name, value) {
      pieces.push(' ', name, '="', value, '"');
    });

    pieces.push(']');

    if (content)
      pieces.push(content);

    pieces.push('[/', tag, ']');

    return pieces.join('');
  }

  tinymce.create('tinymce.plugins.Plugin_WPCmsEditor', {

    init: function (ed, url) {

      $('.wpcms-editor-plugin-item').each(function () {
        var $form = $(this),
            $fields = $form.find('.wpcms-editor-plugin-field-input'),
            shortcodeName = $form.attr('data-shortcode');

        if (!shortcodeName) return;

        $form.editMode = false;
        $form.innercontent = '';

        $fields.each(function () {
          var attributes = {};

          $.each(this.attributes, function (k, v) {
            if (/^data\-wpcms\-editor-plugin\-/gi.test(v.name))
              attributes[v.name.replace(/^data\-wpcms\-editor-plugin\-/gi, '')] = v.value;
          })

          var inputType = $(this).attr('data-wpcms-editor-plugin-type'),
              inputName = $(this).attr('data-wpcms-editor-plugin-name');

          if (!WPCmsEditorFields[inputType]) return;

          WPCmsEditorFields[inputType].init($, $(this), inputName, attributes);
        });

        $('<button>', {
          'class': 'secondary-btn',
          text: 'Insert',
          on: {
            click: function (e) {
              e.preventDefault();

              var values = {};

              $fields.each(function () {
                var inputType = $(this).attr('data-wpcms-editor-plugin-type'),
                    inputName = $(this).attr('data-wpcms-editor-plugin-name');

                if (!WPCmsEditorFields[inputType]) return;

                values[inputName] = WPCmsEditorFields[inputType].get($(this));
              });

              var content = $form.editMode ? $form.innercontent : ed.selection.getContent(),
                  text = buildShortcode(values, content, shortcodeName);

              ed.execCommand('mceInsertContent', 0, text);

              $fields.each(function (k, v) {
                var inputType = $(this).attr('data-wpcms-editor-plugin-type');

                if (!WPCmsEditorFields[inputType]) return;

                WPCmsEditorFields[inputType].set($(this), '');
              });

              $form.editMode = false;
              $form.innercontent = '';

              tb_remove();
            }
          }
        }).appendTo($form);

        ed.addButton(shortcodeName, {
          title: shortcodeName,
          cmd: shortcodeName,
          image: url + '/icon.png'
        });

        ed.addCommand(shortcodeName, function () {
          $form.editMode = false;
          $form.innercontent = '';

          $fields.each(function () {
            var inputType = $(this).attr('data-wpcms-editor-plugin-type'),
                inputName = $(this).attr('data-wpcms-editor-plugin-name');

            if (!WPCmsEditorFields[inputType]) return;

            WPCmsEditorFields[inputType].set($(this), '');
          });

          tb_show('Shortcode: ' + shortcodeName, '#TB_inline?inlineId=wpcms-editor-plugin-' + shortcodeName);
          $("#TB_ajaxContent").removeAttr('style');
          $("#TB_window").css('overflow-y', 'auto');
        });

        wp.mce.views.register(shortcodeName, {

          postID: $('#post_ID').val(),

          loader: false,

          getContent: function () {
            return $('<div>').append($('<div>', {
              text: buildShortcode(this.shortcode.attrs.named, this.shortcode.content, this.shortcode.tag),
              css: {
                padding: 10,
                background: '#ddddff'
              }
            })).html();
          },

          edit: function (node) {
            var values = this.shortcode.attrs.named;
            values['innercontent'] = this.shortcode.content;

            $fields.each(function () {
              var inputType = $(this).attr('data-wpcms-editor-plugin-type'),
                  inputName = $(this).attr('data-wpcms-editor-plugin-name');

              if (!WPCmsEditorFields[inputType]) return;

              WPCmsEditorFields[inputType].set($(this), values[inputName]);
            });

            $form.editMode = true;
            $form.innercontent = this.shortcode.content;

            setTimeout(function () {
              $(window).trigger('focus');
              tb_show('Shortcode: ' + shortcodeName, '#TB_inline?inlineId=wpcms-editor-plugin-' + shortcodeName);
              $("#TB_ajaxContent").removeAttr('style');
              $("#TB_window").css('overflow-y', 'auto');
            }, 20)
          }
        });

      });

    }

  });

  tinymce.PluginManager.add('WPCmsEditor', tinymce.plugins.Plugin_WPCmsEditor);
});




