jQuery(document).ready(function ($) {

    $('[data-action="start-alarm-import"]').on('click', function () {
        var $button = $(this),
            buttonText = $button.text();

        $.ajax({
            url: ajaxurl,
            type: 'post',
            data: {
                action: 'import_alarms'
            },
            beforeSend: function (response) {
                $button.prop('disabled', true).text(apiAlarmManagerLang.importing);
            },
            complete: function (response) {
                $button.prop('disabled', false).text(buttonText);
                window.location.reload();
            }
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
