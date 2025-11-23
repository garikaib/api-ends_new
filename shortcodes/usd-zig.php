<?php

/**
 * Registers the [usd-zig] shortcode.
 */
add_shortcode('usd-zig', 'zpc_usd_zig_shortcode');

function zpc_usd_zig_shortcode()
{
    if (is_singular() && has_shortcode(get_post()->post_content, 'usd-zig')) {
        $table = new ZP_USD_ZiG_Table();
        return $table->render();
    }
    return '';
}
