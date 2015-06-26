if (typeof _WPCmsGlobalInit == "undefined") _WPCmsGlobalInit = {};

_WPCmsGlobalInit.DatePicker = function ($) {

    $('.wpcms-date-picker-field').each(function (k, datePickerField) {

        if ($(this).data('init')) return;
        $(this).data('init', true);


        $(datePickerField).find('input').each(function () {
            $(this).datetimepicker();
        });
    });
};

jQuery(document).ready(_WPCmsGlobalInit.DatePicker);

