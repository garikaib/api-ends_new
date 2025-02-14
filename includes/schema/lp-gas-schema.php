<?php
/**
 * Add LP Gas fuel schema to the Yoast JSON LD output.
 *
 * @param array $pieces The existing Yoast JSON LD data.
 * @param WP_Post $context The current post context.
 *
 * @return array The updated Yoast JSON LD data with the LP Gas fuel schema added.
 */
function add_lp_gas_schema($pieces, $context)
{
    // Include LPGasPricesSchema Class
    require plugin_dir_path(__FILE__) . 'lp-gas-schema-class.php';

    // Get remote IP
    $remote_ip = zp_get_remote_ip();

    // Create an instance of the ZIMAPI class
    $api = new ZIMAPI(ZIMAPI_BASE);

    try {
        // Call the callApi method and store the result in a variable
        $data = $api->callApi("/fuel/lp-gas", $remote_ip);

        if ($data) {
            $pieces[] = new LPGasPriceSchema($context, $data);

        }
    } catch (Exception $e) {
        // If an exception is caught, log the error and continue silently
        error_log("Exception caught while calling LP Gas fuel API: " . $e->getMessage());
    }

    return $pieces;
}
add_filter('wpseo_schema_graph_pieces', 'add_lp_gas_schema', 11, 2);
