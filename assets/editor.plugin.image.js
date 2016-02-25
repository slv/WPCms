var WPCmsEditorFields = WPCmsEditorFields || {};

WPCmsEditorFields.image = {

  init: function ($, $field, inputName) {
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
  },

  get: function ($field) {
    return $field.find('input').val();
  }

}