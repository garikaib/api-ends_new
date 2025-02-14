<?php
/**
 * Add Mbare schema to the Yoast JSON LD output.
 *
 * @param array $pieces The existing Yoast JSON LD data.
 * @param WP_Post $context The current post context.
 *
 * @return array The updated Yoast JSON LD data with the Mbare schema added.
 */
function add_mbare_schema($pieces, $context)
{
    // Include MbareSchema Class
    require plugin_dir_path(__FILE__) . 'mbare-schema-class.php';

    // Get remote IP
    $remote_ip = zp_get_remote_ip();

    // Create an instance of the ZIMAPI class
    $api = new ZIMAPI(ZIMAPI_BASE);

    // Call the callApi method and store the result in a variable
    $data = $api->callApi("/prices/mbare/all", $remote_ip);

    if ($data) {
        $pieces[] = new MbarePricesSchema($context, $data);
    }

    return $pieces;
}
add_filter('wpseo_schema_graph_pieces', 'add_mbare_schema', 11, 2);
