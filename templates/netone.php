<?php

require_once plugin_dir_path(__DIR__) . "includes/relative-time.php";
require_once plugin_dir_path(__DIR__) . "includes/format-data.php";
require_once plugin_dir_path(__DIR__) . "includes/usd-equivalent.php";
require_once plugin_dir_path(__DIR__) . "includes/show-price-footer.php";
require_once plugin_dir_path(__DIR__) . "includes/format-prices.php";
require_once plugin_dir_path(__DIR__) . "includes/show-mobile-data.php";

/**
 * Builds a table with NetOne prices.
 *
 * @param array  $data       An array of data to show in the table.
 * @param array  $rates      An array of exchange rates.
 * @param string $bundleType The type of bundle to show.
 * @param string $filter     The filter to apply to the data.
 *
 * @return string The HTML code for the table.
 */
function build_netone_prices(array $data, array $rates, string $bundleType, string $filter)
{
    // Captions to use.
    $captions = [
        'wifi' => 'One-Fi Bundles',
        'hybrid' => 'One Fusion Bundles',
        'data' => 'NetOne Data',
    ];

    // Build the table.
    $table = '<figure class="wp-block-table">' .
    '<table>' .
    '<thead>' .
    '<tr>' .
    '<th>Package Name</th>' .
    '<th>Price In ZWL</th>' .
    '<th>Price In $USD</th>' .
    '<th>What you get</th>' .
    '<th>Validity</th>' .
    '</tr>' .
    '</thead>' .
    '<tbody>' . showData($data, $rates, $bundleType, $filter) . '</tbody>' .
    '</table>' .
    '<figcaption>Latest ' . showCaption($bundleType, $captions) . ' Prices</figcaption>' .
    '</figure>' .
    '<p><strong>Last Updated on ' . $rates['rates']['updatedAt'] . '</strong></p>' .
    zp_show_footer();

    return $table;
}
