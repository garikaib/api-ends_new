<?php
/**
 * Returns the HTML table containing list of ZiG notes and their USD equivalent values.
 *
 * @param array $data The data containing prices and updatedAt information.
 * @return string The HTML table string.
 */
function build_zig_to_usd_table(array $rates, array $oe_rates)
{
    // Build the oe_array
    $oe_array = build_oe_array($oe_rates);

    // Convert date to DateTime object and set the timezone
    $date = new DateTime('now', new DateTimeZone('Africa/Harare'));
    // Format the date in the desired format
    $formatted_date = $date->format('l, j F Y');

    // ZiG notes with their values
    $zig_notes = [
        1 => '1 ZiG',
        2 => '2 ZiG',
        5 => '5 ZiG',
        10 => '10 ZiG',
        20 => '20 ZiG',
        50 => '50 ZiG',
        100 => '100 ZiG',
        200 => '200 ZiG',
    ];

    // Start building the HTML table
    $table_header = '
    <h4>Convert Zimbabwe Gold (ZiG) notes to USD on ' . wp_kses_post($formatted_date) . '</h4>
    <figure class="wp-block-table"> <table>

            <thead>
                <tr>
                    <th scope="col">ZiG Note <img src="https://zimpricecheck.com/wp-content/uploads/2024/04/tobias-Flag-of-Zimbabwe-scaled.webp" alt="Zimbabwe Flag" class="flag-icon"></th>
                    <th scope="col">Equivalent Value in USD <img src="https://zimpricecheck.com/wp-content/uploads/2024/04/jp-draws-US-Flag.webp" alt="USA Flag" class="flag-icon"></th>
                    <th scope="col">Equivalent Value in Rand (ZAR) <img src="https://zimpricecheck.com/wp-content/uploads/2024/04/Anonymous-Flag-of-South-Africa.webp" alt="South Africa Flag" class="flag-icon"></th>
                    <th scope="col">Equivalent Value in GBP <img src="https://zimpricecheck.com/wp-content/uploads/2024/04/Anonymous-Flag-of-Britain.webp" alt="UK Flag" class="flag-icon"></th>
                </tr>
            </thead>
            <tbody>
    ';

    $table_rows = '';
    foreach ($zig_notes as $value => $note) {
        // Calculate equivalent value in USD
        $usd_equivalent = number_format($value / $rates['rates']['ZiG_Mid'], 2, '.', ' ');

        // Calculate equivalent value in Rand (ZAR)
        $zar_equivalent = number_format(($usd_equivalent) * $oe_array['ZAR'], 2, '.', ' ');

        // Calculate equivalent value in GBP
        $gbp_equivalent = number_format(($usd_equivalent) * $oe_array['GBP'], 2, '.', ' ');

        // Add row to the table
        $table_rows .= '
            <tr>
                <td>' . wp_kses_post($note) . '</td>
                <td>US$' . wp_kses_post($usd_equivalent) . '</td>
                <td>R' . wp_kses_post($zar_equivalent) . '</td>
                <td>£' . wp_kses_post($gbp_equivalent) . '</td>
            </tr>
        ';
    }

    // End building the HTML table
    $table_footer = '
            </tbody>
        </table></figure>
        <p><strong><em>Last updated ' . wp_kses_post($rates['rates']['updatedAt']) . '</em></strong></p>
    ';

    return '<div class="fuel-prices-table">' . $table_header . $table_rows . $table_footer . '</div>';
}

if (!function_exists('build_oe_array')) {
    function build_oe_array(array $oe_rates)
    {
        // Array of wanted symbols
        $wanted_currencies = [
            'ZAR', 'BWP', 'ZMW', 'GBP', 'EUR', 'JPY', 'AUD', 'TZS', 'CNY', 'NZD', 'NGN',
        ];

        $oe_array = [];

        foreach ($oe_rates['rates'] as $rate) {
            // Check if $rate is an array
            if (is_array($rate)) {
                // Iterate over each key-value pair inside $rate
                foreach ($rate as $i => $value) {
                    $symbol = key($rate[$i]);
                    if (in_array($symbol, $wanted_currencies)) {
                        $oe_array[$symbol] = $rate[$i][$symbol];
                    }
                }
            }
        }

        return $oe_array;
    }
}
