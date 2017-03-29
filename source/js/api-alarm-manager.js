jQuery(document).ready(function ($) {

    $('[data-action="start-alarm-import"]').on('click', function () {
        var $button = $(this);
        var data = {
            action: 'schedule_import'
        };

        $.post(ajaxurl, data, function (response) {
            $button.removeAttr('data-action').prop('disabled', true).text(apiAlarmManagerLang.importing);
        });
    });

});
