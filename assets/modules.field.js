if (typeof _WPCmsGlobalInit === "undefined") _WPCmsGlobalInit = {};

jQuery(document).ready(function ($) {

  $('.wpcms-modules-field').each(function (k, field) {

    $(field).find('.wpcms-modules-field-start-button').click(function () {
      $(field).addClass('wpcms-modules-field-active');
      $("#publish").appendTo($(field).find('.wpcms-modules-field-save-button-wrapper')).attr('class', 'btn btn-success btn-lg btn-block');
    });
    $(field).find('.wpcms-modules-field-close-button').click(function () {
      $(field).removeClass('wpcms-modules-field-active');
      $("#publish").appendTo($('#publishing-action')).attr('class', 'button button-primary button-large');
    });
    $(field).find('.wpcms-modules-field-toggle-button').click(function () {
      var open = $(field).find('.modules-list-droppable .module').first() && $(field).find('.modules-list-droppable .module').first().hasClass('module-open');
      $(field).find('.modules-list-droppable .module')[open ? 'removeClass' : 'addClass']('module-open');
    });

    if ($(this).data('init')) return;
    $(this).data('init', true);

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
        });
      });

      $.each(_WPCmsGlobalInit, function (Field, Init) {
        Init($);
      });
    }

    $(field).find('.modules-list-droppable').sortable({
      start: function (event, ui) {
      },
      stop: function (event, ui) {
      },
      update: function (event, ui) {
        setFields($(this));
      }
    });

    setFields($(field).find('.modules-list-droppable'));

    var id = 0;

    $(field).find('.modules-list > .module').draggable({
      containment: $(field),
      connectToSortable: $(field).find('.modules-list-droppable'),
      appendTo: $(field),
      helper: "clone"
    });

    $(field).find('.modules-list-droppable').droppable({
      accept: ".module",
      hoverClass: "drop-hover"
    });

  });
});

