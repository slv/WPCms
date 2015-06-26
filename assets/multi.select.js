if (typeof _WPCmsGlobalInit == "undefined") _WPCmsGlobalInit = {};

_WPCmsGlobalInit.MultiSelectField = function ($) {

  $('.wpcms-relation-field').each(function (k, field) {

    if ($(this).data('init')) return;
    $(this).data('init', true);


    var payload = $(field).attr('data-payload'),
        sortables = $(field).find(".options-list-sortable").first(),
        values = $(field).find('.input').val().length ? $(field).find('.input').val().split(',') : [];

    $.each(values, function (k, id) {
      var item = sortables.find('#option-sort-' + id);
        item.find('.remove').click(function (e) {
          e.preventDefault();
          item.remove();
          sortables.sortable('refresh');
          setValues();
        });
        item.appendTo(sortables);
    });

    function buildItem (id, label) {
      var item = $('<div class="multi-select-field-item" id="option-sort-' + id + '">' + label + '</div>');
      $('<div class="remove dashicons dashicons-dismiss"></div>').click(function (e) {
        e.preventDefault();
        item.remove();
        sortables.sortable('refresh');
        setValues();
      }).appendTo(item);
      return item;
    }

    $(field).find('.multi-select-filter')
      .autocomplete({
        delay: 200,
        minLength: 0,
        source: function( request, response ) {
          $.ajax({
            type: 'POST',
            dataType: 'json',
            url: window.ajaxurl,
            data: {
              action: payload,
              query: request.term
            }
          }).done(function (data) {
            if (!data || !data.length || !data.filter) data = [];
            response(data.filter(function (item) { return (values.indexOf('' + item.id) < 0); }))
          });
        },
        select: function (event, ui) {
          var item = buildItem(ui.item.id, ui.item.label);
          item.appendTo(sortables);
          sortables.sortable('refresh');
          setValues();
          this.value = '';
          $(this).blur();
          return false;
        }
      })
      .focus(function() {
        $(this).autocomplete("search", "");
      });

    sortables.sortable({
      axis: 'y',
      stop: function (event, ui) {
        setValues();
      }
    });

    function setValues () {
      var ids = sortables.sortable("toArray");
      values = ids.map(function (id) { return id.replace(/option-sort-/gi, ''); });
      $(field).find('.input').val(values.join(','));
    };

  });
};

jQuery(document).ready(_WPCmsGlobalInit.MultiSelectField);

