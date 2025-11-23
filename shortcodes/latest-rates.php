<?php

/**
 * Registers the [show-latest-rates] shortcode.
 */
add_shortcode('show-latest-rates', 'zpc_show_latest_rates_shortcode');

function zpc_show_latest_rates_shortcode()
{
    if (is_singular() && has_shortcode(get_post()->post_content, 'show-latest-rates')) {
        $exchange_rates = new ZP_Exchange_Rates();
        return $exchange_rates->render();
    }
    return '';
}
