<?php

/**
 * Shortcode: [show-latest-lpgas-prices]
 */

function zpc_show_latest_lpgas_prices_shortcode($atts)
{
    // Only run if the shortcode is actually present in the post content
    if (is_singular() && has_shortcode(get_post()->post_content, 'show-latest-lpgas-prices')) {
        $lpgas = new ZP_LP_Gas();
        return $lpgas->render();
    }
    return '';
}
add_shortcode('show-latest-lpgas-prices', 'zpc_show_latest_lpgas_prices_shortcode');
