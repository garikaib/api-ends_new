<?php

/**
 * Registers the [zig-usd] shortcode.
 */
add_shortcode('zig-usd', 'zpc_zig_usd_shortcode');

function zpc_zig_usd_shortcode()
{
    if (is_singular() && has_shortcode(get_post()->post_content, 'zig-usd')) {
        $table = new ZP_ZiG_USD_Table();
        return $table->render();
    }
    return '';
}
