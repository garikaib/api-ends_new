<?php

require_once plugin_dir_path(__DIR__) . "includes/usd-equivalent.php";
require_once plugin_dir_path(__DIR__) . "includes/format-prices.php";
require_once plugin_dir_path(__DIR__) . "includes/usd-equivalent.php";
// require_once plugin_dir_path(__DIR__) . "includes/show-price-footer.php";
require_once plugin_dir_path(__DIR__) . "includes/dates.php";

function zp_getCitCellValue($array, $key, $rates)
{
    $value = isset($array[$key]) ? $array[$key] : '';

    if (is_numeric($value)) {
        if ($key === 'usd_amount') {
            $formattedValue = 'US$' . number_format($value, 2);
        }
    } else {
        $formattedValue = $value;
    }

    return $formattedValue;
}

function buildCitRegistrationTable(array $data, array $rates)
{
    // Define the headers and their corresponding keys
    $headers = [
        'Service' => 'service',
        'Cost Amount in USD' => 'usd_amount',
        'Amount in ZiG' => 'zig_amount',
    ];

    // Generate the table header HTML
    $headerHTML = '';
    foreach ($headers as $header => $key) {
        $headerHTML .= '<th>' . $header . '</th>';
    }

    // Generate the table body HTML
    $bodyHTML = '';
    foreach ($data['prices']['fees'] as $fee) {
        $rowHTML = '<tr>';

        foreach ($headers as $header => $key) {
            if ($key === 'zig_amount') {
                // Check if strict_usd is true for the current fee
                $strictUSD = isset($fee['strict_usd']) ? $fee['strict_usd'] : false;

                if ($strictUSD) {
                    // If strict_usd is true, set the ZiG amount to "N/A"
                    $formattedValue = 'N/A';
                } else {
                    // Get the USD amount
                    $usdAmount = isset($fee['usd_amount']) ? $fee['usd_amount'] : 0;
                    // Get the ZIPIT rate from the rates array
                    $zipitRate = isset($rates['rates']['ZiG_Mid']) ? $rates['rates']['ZiG_Mid'] : 0;
                    // Calculate the amount in ZiG
                    $amountInZiG = $usdAmount * $zipitRate;
                    // Format the amount in ZiG with currency symbol and space separators
                    $formattedValue = number_format($amountInZiG, 0, '.', ' ') . ' ZiG';
                }
            } else {
                // For other keys, get the formatted value using zp_getCitCellValue function
                $formattedValue = zp_getCitCellValue($fee, $key, $rates);
            }

            $rowHTML .= '<td>' . htmlspecialchars($formattedValue) . '</td>';
        }

        $rowHTML .= '</tr>';
        $bodyHTML .= $rowHTML;
    }

    // Generate the complete table HTML
    $tableHTML = '<h4>Citizen Status Fees on ' . htmlspecialchars(zp_today_full_date()) . '</h4>
        <figure class="wp-block-table">
            <table>
                <thead>
                    <tr>' . $headerHTML . '</tr>
                </thead>
                <tbody>' . $bodyHTML . '</tbody>
            </table>
            <figcaption>Birth and Death Certificate Fees</figcaption>
        </figure>
        <p><strong>Last Updated on ' . htmlspecialchars($data['prices']['updatedAt']) . '</strong></p>';

    return $tableHTML;
}
