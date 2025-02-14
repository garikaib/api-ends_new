<?php

require_once plugin_dir_path(__DIR__) . 'includes/format-data.php';
require_once plugin_dir_path(__DIR__) . 'includes/usd-equivalent.php';
require_once plugin_dir_path(__DIR__) . 'includes/show-price-footer.php';
require_once plugin_dir_path(__DIR__) . 'includes/format-prices.php';
require_once plugin_dir_path(__DIR__) . 'includes/dates.php';
/**
 * Builds the ZESA table.
 *
 * @param array $data The data to use in building the table.
 * @param array $rates The exchange rates to use.
 * @return string The built ZESA table.
 */
function build_zesa_table(array $data, array $rates)
{
    // Captions to use.
    $captions = [];

    $html = '<h4>Latest ZESA Tariffs %s</h4>
    <figure class="wp-block-table">
        <table>
            <thead>
                <tr>
                    <th>Consumption Bands</th>
                    <th>Price per Unit in ZiG</th>
                    <th>Est Price including 6%% REA (ZiG)</th>
                    <th>Price in per Unit in USD (Estimated)</th>
                </tr>
            </thead>
            <tbody>%s
            </tbody>
        </table>
        <figcaption>The latest ZESA tariffs</figcaption>
    </figure>
    <p><strong>Last Updated on %s</strong></p>%s';

    $table_body = show_zesa_table($data, $rates);
    $today_date = zp_today_full_date();
    $rates_updated_at = $rates['rates']['updatedAt'];
    $footer = zp_show_footer();

    $output = sprintf($html, esc_html($today_date), $table_body, esc_html($rates_updated_at), $footer);

    return wp_kses_post($output);
}

/**
 * Shows the ZESA table.
 *
 * @param array $data The data to use in building the table.
 * @param array $rates The exchange rates to use.
 * @return string The built ZESA table.
 */
function show_zesa_table(array $data, array $rates)
{
    $product_table = '';
    $prices = $data['prices']['bands'];

    foreach ($prices as $product) {
        $product_table .= '<tr>
            <td>' . esc_html($product['description']) . '</td>
            <td>' . esc_html($product['zig_price']) . ' ZiG</td>
            <td>' . esc_html($product['zig_price_rea']) . ' ZiG</td>
            <td>' . esc_html(zp_format_prices(zp_to_usd($rates['rates']['ZiG_Mid'], $product['zig_price']), 'usd')) . '</td>
        </tr>';
    }

    return $product_table;
}
