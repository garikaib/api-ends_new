<?php

require_once plugin_dir_path(__DIR__) . "includes/usd-equivalent.php";
require_once plugin_dir_path(__DIR__) . "includes/format-prices.php";
require_once plugin_dir_path(__DIR__) . "includes/show-price-footer.php";
require_once plugin_dir_path(__DIR__) . "includes/dates.php";

function buildZINARATable(array $data, array $rates = [], string $wanted = "zig_fees")
{
    $format_string = "";
    $wantedh = "";
    if ($wanted === "zig_fees") {
        $wantedh = "Fees in ZiG";
        $format_string = "zig";
    } else {
        $wantedh = "Fees in USD";
        $format_string = "usd";
    }
    // Initialize the table HTML
    $html = "<figure class='wp-block-table'><table>
                <thead>
                    <tr>
                        <th>Vehicle Class (Net Mass in kg)</th>
                        <th>4 Months " . $wantedh . "</th>
                        <th>6 Months " . $wantedh . "</th>
                        <th>8 Months " . $wantedh . "</th>
                        <th>10 Months " . $wantedh . "</th>
                        <th>12 Months " . $wantedh . "</th>
                    </tr>
                </thead>
                <tbody>";

    // Loop through the data and add rows to the table
    foreach ($data['prices']['fees'] as $fee) {
        $html .= "<tr>
                    <td>{$fee['vehicle_class']}</td>
                    <td>" . zp_format_prices($fee[$wanted], $format_string) . "</td>
                    <td>" . zp_format_prices(calculateFee($fee[$wanted], 6), $format_string) . "</td>
                    <td>" . zp_format_prices(calculateFee($fee[$wanted], 8), $format_string) . "</td>
                    <td>" . zp_format_prices(calculateFee($fee[$wanted], 10), $format_string) . "</td>
                    <td>" . zp_format_prices(calculateFee($fee[$wanted], 12), $format_string) . "</td>
                </tr>";
    }

    // Close the table HTML
    $html .= "</tbody></table></figure>";

    return $html;
}

function calculateFee($fee, $months)
{
    // Assuming zig_fees_per_month or usd_fees_per_month is not provided, calculating based on the default 4 months fee
    $defaultMonths = 4;
    $feePerMonth = $fee / $defaultMonths;
    return $feePerMonth * $months;
}
