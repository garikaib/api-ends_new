<?php
/**
 * Add exchange rate schema to the Yoast JSON LD output.
 *
 * @param array $data The existing Yoast JSON LD data.
 *
 * @return array The updated Yoast JSON LD data with the exchange rate schema added.
 */

function add_exchange_rate_schema($pieces, $context)
{
    //Include ZPRatesSchema Class
    require plugin_dir_path(__FILE__) . 'rates-schema-class.php';
    if ($context->id === 5325) {
        $remote_ip = zp_get_remote_ip();
        // Create an instance of the ZIMAPI class
        $api = new ZIMAPI(ZIMAPI_BASE);

// Call the callApi method and store the result in a variable
        try {
            // Call the callApi method and store the result in a variable
            $data = $api->callApi("/rates/fx-rates", zp_get_remote_ip());

            if ($data && array_key_exists("rates", $data) && array_key_exists("ZIPIT", $data["rates"])) {

                $pieces[] = new ZPRatesSchema($context, $data);}

        } catch (Exception $e) {
            // If an exception is caught, log the error and continue silently
            error_log("Exception caught while calling exchange API: " . $e->getMessage());
        }

    }
    //This is a filter return pieces no matter what happens
    return $pieces;
}
add_filter('wpseo_schema_graph_pieces', 'add_exchange_rate_schema', 11, 2);
