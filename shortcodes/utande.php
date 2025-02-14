<?php
// API_END_BASE is already defined in api-ends.php

//Get latest Utande prices and show in tables
function utande_internet_prices($attr)
{
    try {
        $type = "All"; //Default type is all
        if (is_array($attr) && array_key_exists("type", $attr)) {
            $type = $attr["type"];
        }

        require_once API_END_BASE . 'includes/utils.php';
        require_once API_END_BASE . 'templates/utande.php';

        $utande = new ZIMAPI(ZIMAPI_BASE);
        $endPoint = "/prices/isp/utande";
        $latest_prices = $utande->callApi($endPoint, zp_get_remote_ip());
        $endPoint = "/rates/fx-rates";
        $latest_rates = $utande->callApi($endPoint, zp_get_remote_ip());
        return buildUtandePrices($latest_prices, $type, $latest_rates);
    } catch (Exception $e) {
        // Log the error
        error_log('Error retrieving Utande prices: ' . $e->getMessage());
        // Return an error message to the user
        return '<p><strong>Sorry, we could not retrieve the latest Utande prices at the moment. Please try again later.</strong></p>';
    }
}
add_shortcode('utande', 'utande_internet_prices');
