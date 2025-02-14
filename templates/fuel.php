<?php

require_once plugin_dir_path(__DIR__) . "includes/format-prices.php";

/**
 * Builds an HTML table displaying diesel and petrol prices in Zimbabwe.
 *
 * @param array $data The data containing prices and updatedAt information.
 * @param array $rates The exchange rate data.
 * @return string The HTML table string.
 */
function build_fuel_table(array $data, array $rates)
{
    $date = zp_today_full_date();

    // Check if the keys exist and are not zero or undefined
    $petrol_price = isset($data['prices']['Petrol_ZiG']) && $data['prices']['Petrol_ZiG'] !== 0 ? $data['prices']['Petrol_ZiG'] : $data['prices']['Petrol_USD'] * $rates['rates']['ZiG_Mid'];
    $diesel_price = isset($data['prices']['Diesel_ZiG']) && $data['prices']['Diesel_ZiG'] !== 0 ? $data['prices']['Diesel_ZiG'] : $data['prices']['Diesel_USD'] * $rates['rates']['ZiG_Mid'];

    $table_data = [
        [
            'item' => 'Blend Petrol (E10) Price in USD',
            'price' => zp_format_prices($data['prices']['Petrol_USD'], 'usd'),
        ],
        [
            'item' => 'Blend Petrol (E10) Price in ZiG',
            'price' => zp_format_prices($petrol_price, 'zig'),
        ],
        [
            'item' => 'Diesel 50 Price in USD',
            'price' => zp_format_prices($data['prices']['Diesel_USD'], 'usd'),
        ],
        [
            'item' => 'Diesel Price in ZiG',
            'price' => zp_format_prices($diesel_price, "zig"),
        ],
    ];

    $table_header = '
    <h4>Diesel and Petrol Prices in Zimbabwe on ' . wp_kses_post($date) . '</h4>
    <figure class="wp-block-table"> <table>

            <thead>
                <tr>
                    <th scope="col">Item</th>
                    <th scope="col">Price Per Litre</th>
                </tr>
            </thead>
            <tbody>
    ';

    $table_rows = '';
    foreach ($table_data as $row) {
        $table_rows .= '
            <tr>
                <td>' . wp_kses_post($row['item']) . '</td>
                <td>' . wp_kses_post($row['price']) . '</td>
            </tr>
        ';
    }

    $table_footer = '
            </tbody>
        </table></figure>
        <p><strong><em>Last updated ' . wp_kses_post($data['prices']['updatedAt']) . '</em></strong></p>
    ';

    return '<div class="fuel-prices-table">' . $table_header . $table_rows . $table_footer . '</div>';
}
