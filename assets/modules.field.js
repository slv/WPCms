if (typeof _WPCmsGlobalInit === "undefined") _WPCmsGlobalInit = {};

_WPCmsGlobalInit.ModulesField = function ($) {

  $('.wpcms-modules-field').each(function (k, field) {

    if ($(this).data('init')) return;
    $(this).data('init', true);

    $(field).find('.wpcms-modules-field-toggle-button').click(function (e) {
      e.preventDefault();
      var open = $(field).find('.modules-list-droppable .module').first() && $(field).find('.modules-list-droppable .module').first().hasClass('module-open');
      $(field).find('.modules-list-droppable .module')[open ? 'removeClass' : 'addClass']('module-open');
      $.each(_WPCmsGlobalInit, function (Field, Init) { Init($, true); });
    });

    $(field).find('.wpcms-modules-field-add-button').click(function (e) {
      e.preventDefault();
      $(field).find('.modules-list').toggleClass('modules-list-open');
    });

    function setFields (sortable) {
      sortable.find('.module').each(function (k) {
        var module = $(this),
            order = k+1;
        module.find('input, select, textarea').each(function (k) {
          var startingId = $(this).attr('id'),
              checked;

          if ($(this).attr('type') === 'radio') {
            startingId = module.find('fieldset').first().attr('id');
            checked = $(this).hasClass('initial-checked');
          }

          if (startingId && startingId.indexOf('____') >= 0) {
            var name = startingId.replace('____', '[' + order + ']');
            $(this).attr('name', name);
          }

          if ($(this).attr('type') === 'radio' && checked)
            $(this).attr('checked', 'checked');
        });
        module.find('.module-remove').unbind('click').click(function (e) {
          e.preventDefault();
          $(module).remove();
        });
        module.find('.module-toggle').unbind('click').click(function (e) {
          e.preventDefault();
          module.toggleClass('module-open');
          $.each(_WPCmsGlobalInit, function (Field, Init) { Init($, true); });
        });

      });

      $.each(_WPCmsGlobalInit, function (Field, Init) {
        Init($);
      });
    }

    $(field).find('.modules-list-droppable').sortable({
      axis: "y",
      start: function (event, ui) {
      },
      stop: function (event, ui) {
      },
      update: function (event, ui) {
        setFields($(this));
      }
    });

    setFields($(field).find('.modules-list-droppable'));


    $(field).find('.modules-list .module').each(function () {
      var module = $(this);

      module.find('.module-add').unbind('click').click(function () {
        module.clone().appendTo($(field).find('.modules-list-droppable')).addClass('module-open');
        setFields($(field).find('.modules-list-droppable'));
        $(field).find('.modules-list').removeClass('modules-list-open');
      });
    });

  });
};

jQuery(document).ready(_WPCmsGlobalInit.ModulesField);