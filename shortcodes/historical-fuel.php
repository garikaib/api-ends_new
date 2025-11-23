<?php

/**
 * Shortcode: [historical-fuel-prices-table]
 */

function zpc_historical_fuel_prices_table_shortcode($atts)
{
    $historical_fuel = new ZP_Historical_Fuel();
    return $historical_fuel->render();
}
add_shortcode('historical-fuel-prices-table', 'zpc_historical_fuel_prices_table_shortcode');
