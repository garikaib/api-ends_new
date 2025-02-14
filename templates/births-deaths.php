<?php

require_once plugin_dir_path(__DIR__) . "includes/usd-equivalent.php";
require_once plugin_dir_path(__DIR__) . "includes/format-prices.php";
require_once plugin_dir_path(__DIR__) . "includes/dates.php";

function zp_get_bd_cell_value($array, $key, $rates)
{
    $value = isset($array[$key]) ? $array[$key] : '';

    if (is_numeric($value)) {
        if ($key === 'usd_amount') {
            $formatted_value = 'US$' . number_format($value, 2);
        }
    } else {
        $formatted_value = $value;
    }

    return $formatted_value;
}

function build_bd_registration_table(array $data, array $rates)
{
    // Define the headers and their corresponding keys
    $headers = [
        'Service' => 'service',
        'Cost Amount in USD' => 'usd_amount',
        'Amount in ZiG' => 'zig_amount',
    ];

    // Generate the table header HTML
    $header_html = '';
    foreach ($headers as $header => $key) {
        $header_html .= '<th>' . $header . '</th>';
    }

    // Generate the table body HTML
    $body_html = '';
    foreach ($data['prices']['fees'] as $fee) {
        $row_html = '<tr>';

        foreach ($headers as $header => $key) {
            if ($key === 'zig_amount') {
                // Check if strict_usd is true for the current fee
                $strict_usd = isset($fee['strict_usd']) ? $fee['strict_usd'] : false;

                if ($strict_usd) {
                    // If strict_usd is true, set the ZiG amount to "N/A"
                    $formatted_value = 'N/A';
                } else {
                    // Get the USD amount
                    $usd_amount = isset($fee['usd_amount']) ? $fee['usd_amount'] : 0;
                    // Get the ZIPIT rate from the rates array
                    $zipit_rate = isset($rates['rates']['ZiG_Mid']) ? $rates['rates']['ZiG_Mid'] : 0;
                    // Calculate the amount in ZiG
                    $amount_in_zig = $usd_amount * $zipit_rate;
                    // Format the amount in ZiG with currency symbol and space separators
                    $formatted_value = number_format($amount_in_zig, 0, '.', ' ') . ' ZiG';
                }
            } else {
                // For other keys, get the formatted value using zp_get_bd_cell_value function
                $formatted_value = zp_get_bd_cell_value($fee, $key, $rates);
            }

            $row_html .= '<td>' . htmlspecialchars($formatted_value) . '</td>';
        }

        $row_html .= '</tr>';
        $body_html .= $row_html;
    }

    // Generate the complete table HTML
    $table_html = '<h4>National, Birth and Death Registration Fees on ' . htmlspecialchars(zp_today_full_date()) . '</h4>
        <figure class="wp-block-table">
            <table>
                <thead>
                    <tr>' . $header_html . '</tr>
                </thead>
                <tbody>' . $body_html . '</tbody>
            </table>
            <figcaption>Birth and Death Certificate Fees</figcaption>
        </figure>
        <p><strong>Last Updated on ' . htmlspecialchars($data['prices']['updatedAt']) . '</strong></p>';

    return $table_html;
}
