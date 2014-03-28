if (typeof _WPCmsGlobalInit == "undefined") _WPCmsGlobalInit = {};

_WPCmsGlobalInit.MultiLanguageField = function ($) {

  $(".multilingual-switcher.ord-0").addClass('btn-primary');

  $('.multilingual-wrapper').hide();
  $('.multilingual-wrapper.ord-0').show();

  $(".multilingual-switcher").click(function (e) {
    e.preventDefault();

    $('.multilingual-wrapper').hide();
    $('.multilingual-wrapper.lang-' + $(this).text()).show();

    $('.multilingual-switcher.btn-primary').removeClass('btn-primary');
    $('.multilingual-switcher.lang-' + $(this).text()).addClass('btn-primary');
  });

  $('.wpcms-modules-field .modules-list-droppable .module').each(function (k, field) {
    if ($(this).data('init')) return;
        $(this).data('init', true);

    $(field).find('.wpcms-multilingual-field-switcher').each(function (k) {
      if (k)
        $(this).hide();
      else if ($(this).parents('.form').length)
        $(this).prependTo($(field).find('.form')[0]);
    });
  });

  $('.wpcms-settings-page').each(function (k, field) {
    if ($(this).data('init')) return;
        $(this).data('init', true);

    var k = 0;
    $(field).find('.wpcms-multilingual-field-switcher').each(function (k) {
      if ($(this).parents('.module').length)
        return;
      if (k)
        $(this).hide();
      else if ($(this).parents('.wpcms-settings-page'))
        $(this).appendTo($(this).parents('.wpcms-settings-page')[0]);

      k = k+1;
    });
  });

  $('.postbox > .inside').each(function (k, field) {
    if ($(this).data('init')) return;
        $(this).data('init', true);

    var k = 0;
    $(field).find('.wpcms-multilingual-field-switcher').each(function (k) {
      if ($(this).parents('.module').length)
        return;
      if (k)
        $(this).hide();
      else
        $(this).prependTo($(field));

      k = k+1;
    });
  });
};

jQuery(document).ready(_WPCmsGlobalInit.MultiLanguageField);