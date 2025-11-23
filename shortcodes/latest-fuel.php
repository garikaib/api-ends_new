<?php

/**
 * Shortcode: [show-latest-fuel-prices]
 */

function zpc_show_latest_fuel_prices_shortcode($atts)
{
    $fuel_table = new ZP_Latest_Fuel();
    return $fuel_table->render();
}
add_shortcode('show-latest-fuel-prices', 'zpc_show_latest_fuel_prices_shortcode');
