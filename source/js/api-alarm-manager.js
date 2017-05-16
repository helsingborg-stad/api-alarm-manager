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

    $('#disturbance-template').on('change', function () {
        var $selected = $(this).find('option:selected');
        var title = $selected.data('title');
        var message = $selected.data('message');

        $('#title').focus().val(title).blur();
        tinymce.editors[0].setContent(message);
    });

});
