jQuery(function($) {

  var fields = {

    text: {

      init: function ($field, inputName) {
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
    },

    image: {

      init: function ($field, inputName) {
        var $input = $('<input>', {
          type: 'hidden',
          name: inputName
        }).appendTo($field);

        var $gallery = $('<div>', {
          'class': 'wpcms-editor-plugin-preview',
          css: {
            width: '100%',
            overflow: 'hidden'
          }
        }).appendTo($field);

        $('<button>', {
          'class': 'secondary-btn',
          text: 'Add Media',
          on: {
            click: function (e) {
              e.preventDefault();

              if (mojo_media_frame) {
                  mojo_media_frame.open();
                  return;
              }
              var mojo_media_frame = wp.media.frames.mojo_media_frame = wp.media({
                  className: 'media-frame mojo-media-frame',
                  frame: 'select',
                  multiple: $field.attr('data-wpcms-editor-plugin-multiple') ? 'add' : false,
                  library: {
                      type: 'image'
                  }
              });
              mojo_media_frame.on('select', function () {
                  var selection = mojo_media_frame.state().get('selection');
                  var val = '';
                  $gallery.html('');
                  selection.map(function(attachment) {
                      if (!attachment.id || !attachment.attributes || !attachment.attributes.sizes) return;
                      var thumbnail = attachment.attributes.sizes.full;
                      if (typeof attachment.attributes.sizes.thumbnail !== "undefined") thumbnail = attachment.attributes.sizes.thumbnail;
                      if (val != '') val += ',';
                      val += attachment.id;
                      $('<div class="gallery-sort-item" id="gallery-sort-'+attachment.id+'"><img src="' + thumbnail.url + '" /></div>').appendTo($gallery);
                  });
                  $input.val(val);
              });
              mojo_media_frame.on('open', function () {
                  var selection = mojo_media_frame.state().get('selection');
                  var ids = $input.val().split(',');
                  $.each(ids, function (k, id) {
                      var attachment = wp.media.attachment(id);
                      attachment.fetch();
                      if (attachment) selection.add([attachment]);
                  });
              });

              mojo_media_frame.open();
            }
          }
        }).appendTo($field);
      },

      set: function ($field, v) {
        $field.find('input').val(v);
        $field.find('.wpcms-editor-plugin-preview').html(v ? 'data changed' : '');
        console.log(wp.media)
      },

      get: function ($field) {
        return $field.find('input').val();
      }
    }
  }

  function buildShortcode (atts, content, tag) {
    var pieces = ['[', tag];

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
            $fields = $form.find('.wpcms-editor-plugin-input'),
            shortcodeName = $form.attr('data-shortcode');

        if (!shortcodeName) return;

        $form.editMode = false;
        $form.innercontent = '';

        $fields.each(function () {
          var inputType = $(this).attr('data-wpcms-editor-plugin-type'),
              inputName = $(this).attr('data-wpcms-editor-plugin-name');

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
                var inputType = $(this).attr('data-wpcms-editor-plugin-type'),
                    inputName = $(this).attr('data-wpcms-editor-plugin-name');

                if (!fields[inputType]) return;

                values[inputName] = fields[inputType].get($(this));
              });

              var content = $form.editMode ? $form.innercontent : ed.selection.getContent(),
                  text = buildShortcode(values, content, shortcodeName);

              ed.execCommand('mceInsertContent', 0, text);

              $fields.each(function () {
                var inputType = $(this).attr('data-wpcms-editor-plugin-type');

                if (!fields[inputType]) return;

                fields[inputType].set($(this), '');
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
          tb_show('Shortcode: ' + shortcodeName, '#TB_inline?inlineId=wpcms-editor-plugin-' + shortcodeName);
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

              if (!fields[inputType]) return;

              fields[inputType].set($(this), values[inputName]);
            });

            $form.editMode = true;
            $form.innercontent = this.shortcode.content;

            setTimeout(function () {
              $(window).trigger('focus');
              tb_show('Shortcode: ' + shortcodeName, '#TB_inline?inlineId=wpcms-editor-plugin-' + shortcodeName);
            }, 20)
          }
        });

      });

    }

  });

  tinymce.PluginManager.add('WPCmsEditor', tinymce.plugins.Plugin_WPCmsEditor);
});




