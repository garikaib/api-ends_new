<?php

require_once plugin_dir_path(__DIR__) . "includes/usd-equivalent.php";
require_once plugin_dir_path(__DIR__) . "includes/format-prices.php";
require_once plugin_dir_path(__DIR__) . "includes/usd-equivalent.php";
require_once plugin_dir_path(__DIR__) . "includes/show-price-footer.php";
require_once plugin_dir_path(__DIR__) . "includes/dates.php";function buildTGTable(array $data, array $rates)
{
    // Define the headers and their corresponding keys
    $headers = [
        'Vehicle Type' => 'vehicle_type',
        'Toll Gate Fees in USD' => 'usd_price',
        'Toll Gate Fees in ZiG' => 'zig_price',
        'Toll in BWP (Pula)' => 'bwp_price',
        'Toll in ZAR (Rand)' => 'zar_price',
    ];

    // Generate the table header HTML
    $headerHTML = '';
    foreach ($headers as $header => $key) {
        $headerHTML .= '<th>' . $header . '</th>';
    }

    // Generate the table body HTML
    $bodyHTML = '';
    foreach ($data['prices']['prices'] as $product) {
        $rowHTML = '<tr>';
        foreach ($headers as $header => $key) {
            $formattedValue = zp_getCellValue($product, $key);
            $rowHTML .= '<td>' . htmlspecialchars($formattedValue) . '</td>';
        }
        $rowHTML .= '</tr>';
        $bodyHTML .= $rowHTML;
    }

    // Generate the complete table HTML
    $tableHTML = '<h4>ZINARA Tollgate fees at Other Road Tolling Points on ' . htmlspecialchars(zp_today_full_date()) . '</h4>
        <figure class="wp-block-table">
            <table>
                <thead>
                    <tr>' . $headerHTML . '</tr>
                </thead>
                <tbody>' . $bodyHTML . '</tbody>
            </table>
            <figcaption>The Latest ZINARA Tollgate Fees</figcaption>
        </figure>
        <p><strong>Last Updated on ' . htmlspecialchars($rates['rates']['updatedAt']) . '</strong></p>
        ';

    return $tableHTML;
}
