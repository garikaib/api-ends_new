<?php
/**
 * Add fuel schema to the Yoast JSON LD output.
 *
 * @param array $pieces The existing Yoast JSON LD data.
 * @param WP_Post $context The current post context.
 *
 * @return array The updated Yoast JSON LD data with the fuel schema added.
 */
function add_fuel_schema($pieces, $context)
{
    // Include FuelPricesSchema Class
    require plugin_dir_path(__FILE__) . 'fuel-schema-class.php';

    // Get remote IP
    $remote_ip = zp_get_remote_ip();

    // Create an instance of the ZIMAPI class
    $api = new ZIMAPI(ZIMAPI_BASE);

    try {
        // Call the callApi method and store the result in a variable
        $data = $api->callApi("/fuel", $remote_ip);

        if ($data) {
            $pieces[] = new FuelPricesSchema($context, $data);
        }
    } catch (Exception $e) {
        // If an exception is caught, log the error and continue silently
        error_log("Exception caught while calling fuel API: " . $e->getMessage());
    }

    return $pieces;
}
add_filter('wpseo_schema_graph_pieces', 'add_fuel_schema', 11, 2);
