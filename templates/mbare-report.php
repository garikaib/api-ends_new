<?php

// Get and show latest Mbare Prices
add_shortcode('mbare-musika', 'mbare_report');
function mbare_report()
{
    // Check if it's a singular page and the shortcode is present
    if (is_singular() && has_shortcode(get_post()->post_content, 'mbare-musika')) {
        try {
            $mbare = new ZIMAPI(ZIMAPI_BASE);
            $endpoints = [
                'rates' => [
                    'endpoint' => '/rates/fx-rates',
                ],
                'prices' => [
                    'endpoint' => '/prices/mbare/all',
                ],
            ];

            $data = $mbare->multiCallApi($endpoints, zp_get_remote_ip());

            // Check if data is retrieved successfully
            if (isset($data['prices']['data'], $data['rates']['data'])) {
                return build_mbare_report($data['prices']['data'], $data['rates']['data']);
            } else {
                // Handle the case where data retrieval fails
                return esc_html__("Error: Unable to retrieve data from the API.", 'text-domain');
            }
        } catch (Exception $e) {
            // Log the error
            error_log('Error retrieving Mbare Musika prices: ' . $e->getMessage());

            // Return an error message to the user
            return esc_html__("We couldn't retrieve the latest Mbare Musika prices at the moment. Please try again later.", 'text-domain');
        }
    } else {
        // Handle the case where the shortcode is not used on a singular page
        return '';
    }
}

/**
 * Builds the report for Mbare Musika prices.
 *
 * @param array $pricesData Prices data.
 * @param array $rates  Exchange rates data.
 *
 * @return string HTML string of the report.
 */
function build_mbare_report(array $pricesData, array $rates = [])
{
    require_once plugin_dir_path(__DIR__) . 'includes/format-prices.php';

    $product_table = '';
    error_log(print_r($pricesData, true)); // For debugging - check the structure

    // Access the correct level for 'produce_prices' and handle potential errors
    if (isset($pricesData['prices']['produce_prices'])) {
        foreach ($pricesData['prices']['produce_prices'] as $product) {
            $product_table .= sprintf(
                '<tr><td>%s</td><td>%s (%s)</td><td>%s</td><td>%s</td></tr>',
                esc_html($product['descr']),
                esc_html($product['m_unit']),
                esc_html($product['est_qty']),
                esc_html(zp_format_prices($product['max_price'], 'usd')),
                esc_html(zp_format_prices($product['max_price'] * $rates['rates']['ZiG_Cash'], 'zig'))
            );
        }
    } else {
        // Handle the case where 'produce_prices' is not found
        $product_table = '<tr><td colspan="4">' . esc_html__('No product prices found.', 'text-domain') . '</td></tr>';
    }

    $current_date = date('l, j F Y', strtotime('now', time()));

    // Access the correct level for 'updatedAt' and provide a default value
    $updatedAt = isset($pricesData['prices']['updatedAt']) ? $pricesData['prices']['updatedAt'] : '';

    // Use sprintf to build the HTML output
    $output = sprintf(
        '<h4>%s</h4>
        <figure class="wp-block-table">
  <table>
    <thead>
      <tr>
        <th>%s</th>
        <th>%s</th>
        <th>%s</th>
        <th>%s</th>
      </tr>
    </thead>
    <tbody>%s</tbody>
  </table>
  <figcaption>%s</figcaption>
</figure>
<p><strong>%s %s</strong></p>',
        esc_html("Mbare Musika Prices on $current_date"),
        esc_html__('Item', 'text-domain'),
        esc_html__('Quantity', 'text-domain'),
        esc_html__('Price In $USD', 'text-domain'),
        esc_html__('Price in ZiG', 'text-domain'),
        $product_table,
        esc_html__('Latest prices from Mbare Musika', 'text-domain'),
        esc_html__('Last Updated on', 'text-domain'),
        esc_html($updatedAt) // Display the updated time
    );

    return $output;
}
