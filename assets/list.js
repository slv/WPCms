if (typeof _WPCmsGlobalInit == "undefined") _WPCmsGlobalInit = {};

_WPCmsGlobalInit.List = function ($) {

  $('.wpcms-list-field, .wpcms-multi-list-field').each(function (k, field) {

    if ($(this).data('init')) return;
    $(this).data('init', true);

    var payload = $(field).attr('data-payload'),
        sortables = $(this).find('.wpcms-list-items-container');

    function adjustAttributes () {
      $(field).find('.form-control').each(function (k) {
        $(this).find('input').attr('name', function () {
          return $(this).attr('name').replace(/\[\d*\]\[(\d*)\]$/g, '[' + k + '][$1]');
        });
      });
    }

    sortables.sortable({
      axis: 'y',
      handle: '.reorder-handle',
      update: adjustAttributes
    });
    adjustAttributes();

    var last = 1,
        tot = sortables.find('.form-control').get().length;

    $(sortables.find('.form-control').get().reverse()).each(function (k) {
      if (k+1 == tot) return;

      if (last && !$(this).find('input').filter(function () { return $(this).val(); }).length)
        $(this).remove();
      else
        last = 0;
    });

    $(this).find('.wpcms-list-field-button').click(function (e) {
      e.preventDefault();
      if (!$(field).find('.form-control').first()) return;

      var newItem = $(field).find('.form-control').first().clone();

      sortables.append(newItem).sortable('refresh');
      adjustAttributes();

      newItem.find('input').val('').first().focus();
    });
  });

  $('.wpcms-dynamic-list-field').each(function (k, field) {

    if ($(this).data('init')) return;
    $(this).data('init', true);

    $(this).find('select').change(function () {
      sortables.html('');

      if (!$(this).val()) return;

      $.ajax({
        type: 'POST',
        dataType: 'html',
        url: window.ajaxurl,
        data: {
          action: payload,
          fields_id: $(this).val()
        }
      }).done(function (html) {
        sortables.html(html);
        init();
      });
    });

    var sortables = $(this).find('.wpcms-dynamic-list-items-container'),
        originalLabels = [];

    function init () {
      var last = 1;
      $(sortables.find('.wpcms-fields-group').get().reverse()).each(function() {
        if (last && !$(this).find('label').text())
          $(this).remove();
        else
          last = 0;
      });
      originalLabels = sortables.find('label').get().map(function (label) { return $(label).html(); });
    }

    sortables.sortable({
      axis: 'y',
      handle: '.reorder-handle',
      change: function (ui) {
        var k = 0, current = '';

        sortables.find('.wpcms-fields-group').each(function () {
          if ($(this).hasClass('ui-sortable-helper')) return;

          if ($(this).hasClass('ui-sortable-placeholder')) current = originalLabels[k];
          $(this).find('label').html(originalLabels[k]);
          k++;
        });

        if (current) sortables.find('.ui-sortable-helper label').html(current);
      },
      update: function () {
        sortables.find('label').each(function (k) {
          $(this).html(originalLabels[k]);
        })
      }
    });

    init();

  });
};

jQuery(document).ready(_WPCmsGlobalInit.List);