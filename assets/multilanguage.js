_WPCmsGlobalInit.MultiLanguageField = function ($) {

  $(".multilingual-switcher.ord-0").addClass('button-primary');

  $('.multilingual-wrapper').hide();
  $('.multilingual-wrapper.ord-0').show();

  $(".multilingual-switcher").click(function (e) {
    e.preventDefault();

    $('.multilingual-wrapper').hide();
    $('.multilingual-wrapper.lang-' + $(this).text()).show();

    $('.multilingual-switcher.button-primary').removeClass('button-primary');
    $('.multilingual-switcher.lang-' + $(this).text()).addClass('button-primary');
  });
};

jQuery(document).ready(_WPCmsGlobalInit.MultiLanguageField);