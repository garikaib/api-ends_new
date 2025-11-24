jQuery(document).ready(function ($) {
    $('#zpc_flush_cache_btn').on('click', function (e) {
        e.preventDefault();

        var $btn = $(this);
        var $status = $('#zpc_flush_cache_status');

        $btn.prop('disabled', true).text('Flushing...');
        $status.text('').removeClass('zpc-success zpc-error');

        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'zpc_flush_cache',
                nonce: zpc_admin_vars.nonce
            },
            success: function (response) {
                if (response.success) {
                    $status.text('Cache flushed successfully!').addClass('zpc-success');
                } else {
                    $status.text('Error flushing cache.').addClass('zpc-error');
                }
            },
            error: function () {
                $status.text('Request failed.').addClass('zpc-error');
            },
            complete: function () {
                $btn.prop('disabled', false).text('Flush Cache Now');
                setTimeout(function () {
                    $status.fadeOut(function () {
                        $(this).text('').show().removeClass('zpc-success zpc-error');
                    });
                }, 3000);
            }
        });
    });
});
