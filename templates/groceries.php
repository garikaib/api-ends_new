<?php

//Groceries
add_shortcode('groceries', 'zp_grocery_prices');

function zp_grocery_prices($attr)
{
    require_once plugin_dir_path(__DIR__) . 'includes/usd-equivalent.php';
    require_once plugin_dir_path(__DIR__) . 'includes/show-price-footer.php';
    require_once plugin_dir_path(__DIR__) . 'includes/format-prices.php';
    require_once plugin_dir_path(__DIR__) . 'includes/dates.php';
    try {
        $type = "basic"; //Default type is all
        if (is_array($attr) && array_key_exists("type", $attr)) {
            $type = $attr["type"];
        }

        $groceries = new ZIMAPI(ZIMAPI_BASE);

        // $latest_rates = $groceries->callApi($endPoint, zp_get_remote_ip());
        if ($type === "basic") {
            $priceEndPoint = "";
            $endpoints = [
                'rates' => [
                    'endpoint' => '/rates/fx-rates',
                ],
                'prices' => [
                    'endpoint' => '/prices/groceries/basic',
                ],
            ];
            $data = $groceries->multiCallApi($endpoints, zp_get_remote_ip());
            return build_groceries_table($data['prices']['data'], $data['rates']['data']);
        }
    } catch (Exception $e) {
        // Log the error
        error_log('Error retrieving Grocery prices: ' . $e->getMessage());
        // Return an error message to the user
        require_once plugin_dir_path(__FILE__) . 'includes/class-show-notice.php';

        return ZP_SHOW_NOTICE::showError("We couldn't retrieve the latest Grocery prices at the moment. Please try again later.");
    }
}
/**
 * Builds the grocery prices table.
 *
 * @param array $data The data to display.
 * @param array $rates The currency rates.
 *
 * @return string The HTML for the grocery prices table.
 */
function build_groceries_table(array $data, array $rates)
{
    // Check if the required data is available
    if (isset($data['prices']['prices'])) {
        // Build the table HTML.
        $table_html = '<h4> Latest Grocery Prices on ' . date_i18n('l, d F Y') . '</h4>
            <figure class="wp-block-table">
                <table>
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Prices in Tuckshops USD</th>
                            <th>Supermarket Prices in ZiG</th>
                        </tr>
                    </thead>
                    <tbody>' . show_groceries_table($data, $rates) . '</tbody>
                </table>
                <figcaption>The latest Supermarket Prices</figcaption>
            </figure>
            <p><strong>Last Updated on ' . $rates['rates']['updatedAt'] . '</strong></p>
            ' . zp_show_footer();

        return $table_html;
    } else {
        // Return an error message if the required data is missing
        return '<p>We are sorry we couldn\'t retrieve the latest grocery prices, please come back later.</p>';
    }
}

/**
 * Builds the table rows for the grocery prices table.
 *
 * @param array $data The data to display.
 * @param array $rates The currency rates.
 *
 * @return string The HTML for the table rows.
 */
function show_groceries_table(array $data, array $rates)
{
    $product_table = '';

    // Check if the prices data is available
    if (isset($data['prices']['prices'])) {
        $prices = $data['prices']['prices'];

        foreach ($prices as $product) {
            // Check if the required fields are present in the product data
            if (isset($product['item']) && isset($product['tuckshops_usd'])) {
                $product_table .= '<tr>
                    <td>' . esc_html($product['item']) . '</td>
                    <td>' . zp_format_prices($product['tuckshops_usd']) . '</td>
                    <td>' . zp_format_prices($rates['rates']['ZiG_Mid'] * $product['tuckshops_usd'] * 1.2, "zig") . '</td>
                </tr>';
            }
        }
    }

    return $product_table;
}
