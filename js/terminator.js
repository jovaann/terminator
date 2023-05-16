jQuery(function($) {
    // Attach a click event to the clear button for each row
    $('body').on('click', '.terminator-clear', function() {
        var row = $(this).closest('tr');
        var optionName = row.data('option-name');
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'terminator_clear_autoload',
                option_name: optionName
            },
            success: function() {
                row.fadeOut(500, function() {
                    row.remove();
                });
            }
        });
    });

    // Attach a click event to the clear all button
    $('#terminator-clear-all').click(function() {
        var button = $(this);
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'terminator_clear_all_autoload'
            },
            beforeSend: function() {
                button.prop('disabled', true);
            },
            success: function() {
                button.prop('disabled', false);
                $('#terminator-options-list').empty();
            }
        });
    });
});
