<?php
/**
 * Template for Delta Alcohol / Drinks prices.
 *
 * Variables:
 * - $data: Combined data array
 */

use ZPC\ApiEnds\Utils\DateUtil;
use ZPC\ApiEnds\Utils\PriceUtil;

if (!defined('ABSPATH')) {
    exit;
}

$prices_data = $data['prices'] ?? [];
$rates_data = $data['rates'] ?? [];

$prices = $prices_data['prices']['prices'] ?? [];
$rates = $rates_data['rates'] ?? [];

$zig_bm_buy = $rates['ZiG_BMBuy'] ?? 0;
$updated_at = $rates['updatedAt'] ?? 'N/A';

$today_date = DateUtil::todayFull();

const RETAIL_MARKUP = 1.2;

/**
 * Convert volume in ml to liters or ml string.
 */
$convert_volume = function(int $volume): string {
    if ($volume >= 1000) {
        return ($volume / 1000) . 'L';
    }
    return $volume . 'ml';
};

$table_rows = '';
if (!empty($prices)) {
    foreach ($prices as $product) {
        $item = $product['item'] ?? '';
        $quantity = $product['quantity'] ?? 0;
        $unit = $product['unit'] ?? 0;
        $usd_price = $product['usd_price'] ?? 0;

        $qty_vol = $quantity . 'x' . $convert_volume($unit);
        $retail_usd = RETAIL_MARKUP * $usd_price;
        $retail_zig = RETAIL_MARKUP * $zig_bm_buy * $usd_price;

        $table_rows .= '<tr>';
        $table_rows .= '<td>' . esc_html($item) . '</td>';
        $table_rows .= '<td>' . esc_html($qty_vol) . '</td>';
        $table_rows .= '<td>' . PriceUtil::format($retail_usd, 'usd') . '</td>';
        $table_rows .= '<td>' . PriceUtil::format($retail_zig, 'zig') . '</td>';
        $table_rows .= '</tr>';
    }
}
?>

<h4>Latest Liquor Prices <?php echo esc_html($today_date); ?></h4>
<figure class="wp-block-table">
    <table>
        <thead>
            <tr>
                <th>Product</th>
                <th>Quantity</th>
                <th>Price in USD</th>
                <th>Price in ZiG (Estimated)</th>
            </tr>
        </thead>
        <tbody>
            <?php echo $table_rows; ?>
        </tbody>
    </table>
    <figcaption>The latest Beer, Alcohol, Liquor Prices</figcaption>
</figure>
<p><strong>Last Updated on <?php echo esc_html($updated_at); ?></strong></p>
