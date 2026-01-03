<?php
/**
 * Template part for displaying ZESA Tariffs table.
 *
 * Variables:
 * - $data: Combined data array ['prices' => ..., 'rates' => ...]
 */

use ZPC\ApiEnds\Utils\DateUtil;
use ZPC\ApiEnds\Utils\PriceUtil;

if (!defined('ABSPATH')) {
    exit;
}

$prices_data = $data['prices'] ?? [];
$rates_data = $data['rates'] ?? [];

$bands = $prices_data['prices']['bands'] ?? [];
$rates = $rates_data['rates'] ?? [];

$today_date = DateUtil::todayFull();
$updated_at = $rates['updatedAt'] ?? 'N/A';
$zig_mid = $rates['ZiG_Mid'] ?? 0;

$table_rows = '';
if (!empty($bands)) {
    // Ensure sorted (though API usually sends sorted, good to be safe)
    usort($bands, fn($a, $b) => $a['min_units'] - $b['min_units']);

    foreach ($bands as $band) {
        $zig_price_rea = $band['zig_price_rea'] ?? 0;
        $zig_price = $band['zig_price'] ?? 0;
        
        // Calculate USD using ZiG Mid (Legacy: zp_to_usd($mid, $price))
        $usd_val = ($zig_mid > 0) ? ($zig_price / $zig_mid) : 0;
        
        $table_rows .= '<tr>';
        $table_rows .= '<td>' . esc_html($band['description']) . '</td>';
        $table_rows .= '<td>' . PriceUtil::format($zig_price, 'zig') . '</td>';
        $table_rows .= '<td>' . PriceUtil::format($zig_price_rea, 'zig') . '</td>';
        $table_rows .= '<td>' . PriceUtil::format($usd_val, 'usd') . '</td>';
        $table_rows .= '</tr>';
    }
}
?>

<div class="fuel-prices-table">
<h4>Latest ZESA Tariffs <?php echo esc_html($today_date); ?></h4>
<figure class="wp-block-table">
    <table>
        <thead>
            <tr>
                <th>Consumption Bands</th>
                <th>Price per Unit in ZiG</th>
                <th>Est Price including 6% REA (ZiG)</th>
                <th>Price in per Unit in USD (Estimated)</th>
            </tr>
        </thead>
        <tbody>
            <?php echo $table_rows; ?>
        </tbody>
    </table>
    <figcaption>The latest ZESA tariffs</figcaption>
</figure>
<p><strong>Last Updated on <?php echo esc_html($updated_at); ?></strong></p>
</div>
