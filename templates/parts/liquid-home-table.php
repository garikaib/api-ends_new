<?php
/**
 * Template part for displaying Liquid Home prices.
 *
 * Variables available:
 * - $prices: Array of package prices
 * - $rates: Array of exchange rates
 * - $type: Connection type (Fibre, LTE, etc.)
 * - $header_text: Header text for the table
 * - $date: Formatted date string
 */

if (!defined('ABSPATH')) {
    exit;
}

$product_table = '';

foreach ($prices as $product) {
    if ($product['last_mile'] === $type) {
        $zig_price = isset($product['zig_price']) && $product['zig_price'] != 0 ? $product['zig_price'] : $rates['rates']['ZiG_Mid'] * $product['usd_price'];
        $usd_price = isset($product['usd_price']) && $product['usd_price'] != 0 ? $product['usd_price'] : $product['zig_price'] / $rates['rates']['ZiG_Mid'];
        
        $product_table .= '<tr>';
        $product_table .= '<td>' . esc_html($product['package_name']) . '</td>';
        $product_table .= '<td>' . ZP_Liquid_Home::liquid_is_limited('data', $product) . '</td>';
        $product_table .= '<td>' . ZP_Liquid_Home::liquid_is_limited('night_data', $product) . '</td>';
        $product_table .= '<td>' . zp_format_prices($zig_price, "zig") . '</td>';
        $product_table .= '<td>' . zp_format_prices($usd_price, 'usd') . '</td>';
        $product_table .= '</tr>';
    }
}
?>

<h4>Liquid Home (ZOL) <?php echo esc_html($header_text); ?> Internet Packages Prices on <?php echo esc_html($date); ?></h4>
<figure class='wp-block-table'>
    <table>
        <thead>
            <tr>
                <th>Package Name</th>
                <th>Normal Data(GB)</th>
                <th>Night Data(GB)</th>
                <?php echo ($type === 'LTE_USD' || $type === 'Fibre_USD' ? '<th>Estimated Price in ZiG</th>' : '<th>Price in ZiG</th>'); ?>
                <th>Price in USD</th>
            </tr>
        </thead>
        <tbody>
            <?php echo $product_table; ?>
        </tbody>
    </table>
    <figcaption>Latest Liquid Home <?php echo esc_html($header_text); ?> Prices</figcaption>
</figure>
<p><strong>Last Updated on <?php echo esc_html($rates['rates']['updatedAt']); ?></strong></p>
<?php echo zp_show_footer(); ?>
