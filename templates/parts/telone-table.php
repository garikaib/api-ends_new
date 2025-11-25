<?php
/**
 * Template part for displaying TelOne prices.
 *
 * Variables available:
 * - $prices: Array of package prices
 * - $rates: Array of exchange rates
 * - $type: Connection type (normalized to lowercase)
 * - $header_text: Header text for the table
 * - $date: Formatted date string
 */

if (!defined('ABSPATH')) {
    exit;
}

$product_table = '';

foreach ($prices as $product) {
    if (strtolower($product['last_mile']) === $type) {
        $usd_price_display = ($product['usd_price'] > 0) ? zp_format_prices($product['usd_price'], "usd") : zp_format_prices($product['zig_price'] / $rates['rates']['ZiG_Mid'], "usd");
        $zig_price_display = (($product['zig_price'] ?? 0) > 0) ? zp_format_prices($product['zig_price'], "zig") : zp_format_prices($product['usd_price'] * $rates['rates']['ZiG_Mid'], "zig");

        $product_table .= '<tr>';
        $product_table .= '<td>' . esc_html($product['package_name']) . '</td>';
        $product_table .= '<td>' . ZP_TelOne::telone_is_capped('data', $product) . '</td>';
        $product_table .= '<td>' . ZP_TelOne::telone_is_capped('night_data', $product) . '</td>';
        $product_table .= '<td>' . $usd_price_display . '</td>';
        $product_table .= '<td>' . $zig_price_display . '</td>';
        $product_table .= '</tr>';
    }
}
?>

<h4>TelOne <?php echo esc_html($header_text); ?> package prices on <?php echo esc_html($date); ?></h4>
<figure class='wp-block-table'>
    <table>
        <thead>
            <tr>
                <th>Package Name</th>
                <th>Normal Data(GB)</th>
                <th>Night Data(GB)</th>
                <th><?php echo (($type !== "usd" && $type !== "vsat") ? 'Estimated Price in $USD' : 'Price In USD (Official Price)'); ?></th>
                <th><?php echo (($type === "usd" || $type === "vsat") ? "Estimated Cost in ZiG" : "Price in ZiG"); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php echo $product_table; ?>
        </tbody>
    </table>
    <figcaption>Latest TelOne <?php echo esc_html($header_text); ?> Prices</figcaption>
</figure>
<p><strong>Last Updated on <?php echo esc_html($rates['rates']['updatedAt']); ?></strong></p>
<?php echo zp_show_footer(); ?>
