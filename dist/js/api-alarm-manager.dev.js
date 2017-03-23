jQuery(document).ready(function ($) {

    $('[data-action="start-alarm-import"]').on('click', function () {
        var data = {
            action: 'schedule_import'
        };

        $.post(ajaxurl, data, function (response) {
            alert(response);
            return;
        });
    });

});
