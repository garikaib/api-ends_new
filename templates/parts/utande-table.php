<?php
/**
 * Template part for displaying Utande prices.
 *
 * Variables available:
 * - $prices: Array of package prices
 * - $rates: Array of exchange rates
 * - $type: Connection type
 * - $date: Formatted date string
 */

if (!defined('ABSPATH')) {
    exit;
}

use ZPC\ApiEnds\Utils\PriceUtil;

$package_description = [
    "LTE" => "LTE",
    "Fibre" => "Fibre",
    "WIFI" => 'WIFI',
    "VSAT" => 'VSAT',
];

$connType = $type; // Map variable name
$header_text = $package_description[$connType] ?? "Internet";

// Helper closure
$utande_is_capped = function(string $required, array $product): string {
    if (!$product['capped']) {
        return "Uncapped";
    } else {
        return esc_html($product[$required]);
    }
};

$product_table = "";
foreach ($prices as $product) {
    if ($product['last_mile'] === $connType) {
        
        $usd_price = $product['usd_price'];
        $zig_price_est = $usd_price * ($rates['rates']['ZiG_Mid'] ?? 0);

        $product_table .= '<tr>';
        $product_table .= '<td>' . esc_html($product['package_name']) . '</td>';
        $product_table .= '<td>' . $utande_is_capped('data', $product) . '</td>';
        $product_table .= '<td>' . $utande_is_capped('night_data', $product) . '</td>';
        $product_table .= '<td>' . PriceUtil::format($usd_price, "usd") . '</td>';
        $product_table .= '<td>' . PriceUtil::format($zig_price_est, "zig") . '</td>';
        $product_table .= '</tr>';
    }
}
?>

<h4>Utande <?php echo esc_html($header_text); ?> package prices on <?php echo esc_html($date); ?></h4>
<figure class='wp-block-table'>
  <table>
    <thead>
      <tr>
        <th>Package Name</th>
        <th>Normal Data(GB)</th>
        <th>Bonus Time Data(GB)</th>
        <th>Price In $USD</th>
        <th>Estimated Price In ZiG</th>
      </tr>
    </thead>
    <tbody>
        <?php echo $product_table; ?>
    </tbody>
  </table>
  <figcaption>Latest Utande <?php echo esc_html($connType); ?> Prices</figcaption>
</figure>
<p><strong>Last Updated on <?php echo esc_html($prices[0]['updatedAt'] ?? 'N/A'); ?></strong></p>
