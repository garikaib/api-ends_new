<?php
function build_lp_gas_table(array $data, array $rates)
{
    $date = esc_html(zp_today_full_date());

    // Define the table rows and their corresponding keys and formatting options
    $rows = [
        [
            'label' => 'ZERA Maximum Price in USD',
            'price_key' => 'LPGas_USD',
            'price_format' => 'usd',
        ],
        [
            'label' => 'ZERA Maximum Price in ZiG',
            'price_key' => 'LPGas_USD',
            'price_format' => 'zig',
            'price_multiplier' => isset($data['prices']['LPGas_ZiG']) && $data['prices']['LPGas_ZiG'] != 0 ? $data['prices']['LPGas_ZiG'] : $rates['rates']['ZiG_Mid'],
        ],
        [
            'label' => 'LP Gas Black Market Price in USD',
            'price_key' => 'BM_USD',
            'price_format' => 'usd',
        ],
        [
            'label' => 'LP Gas Black Market Price in ZiG',
            'price_key' => 'LPGas_ZiG', // Use the 'LPGas_ZiG' value from the JSON data
            'price_format' => 'zig',
        ],
    ];

    // Build the table rows
    $table_rows = '';
    foreach ($rows as $row) {
        if (isset($data['prices'][$row['price_key']])) {
            $price = $data['prices'][$row['price_key']];
            if (isset($row['price_multiplier']) && !isset($data['prices']['LPGas_ZiG'])) {
                $price *= $row['price_multiplier'];
            }
            $formatted_price = zp_format_prices($price, $row['price_format']);
            $table_rows .= "
                <tr>
                    <td>{$row['label']}</td>
                    <td>{$formatted_price}</td>
                </tr>
            ";
        }
    }

    $table_html = "
    <h4> Liquid Petroleum (LP) Gas Prices on {$date}</h4>
    <figure class='wp-block-table'>
        <table>
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Price Per KG</th>
                </tr>
            </thead>
            <tbody>
                {$table_rows}
            </tbody>
        </table>
    </figure>
    <p>
        <strong><em>Last updated " . esc_html($data['prices']['updatedAt']) . "</em></strong>
    </p>";

    return $table_html;
}
