<?php
/**
 * Template part for displaying Government Fees tables.
 *
 * Variables available:
 * - $title: Table title (e.g., 'Passport Fees')
 * - $fees_data: Array of fee items
 * - $rates: Rates array
 * - $updated_at: Last updated date string
 * - $caption: (Optional) specific caption
 */

use ZPC\ApiEnds\Utils\DateUtil;
use ZPC\ApiEnds\Utils\PriceUtil;

if (!defined('ABSPATH')) {
    exit;
}

$formatted_date = DateUtil::todayFull();
$zipit_rate = $rates['rates']['ZiG_Mid'] ?? 0;

$table_rows = '';
if (!empty($fees_data)) {
    foreach ($fees_data as $fee) {
        $service = $fee['service'] ?? '';
        $usd_amount = $fee['usd_amount'] ?? 0;
        $strict_usd = $fee['strict_usd'] ?? false;
        
        $usd_display = PriceUtil::format($usd_amount, 'usd');
        
        if ($strict_usd) {
            $zig_display = 'N/A';
        } else {
            $zig_val = $usd_amount * $zipit_rate;
            $zig_display = PriceUtil::format($zig_val, 'zig', 0); // 0 decimals for ZiG usually? Legacy used 0.
        }

        $table_rows .= '<tr>';
        $table_rows .= '<td>' . esc_html($service) . '</td>';
        $table_rows .= '<td>' . $usd_display . '</td>';
        $table_rows .= '<td>' . $zig_display . '</td>';
        $table_rows .= '</tr>';
    }
}
?>

<h4><?php echo esc_html($title); ?> on <?php echo esc_html($formatted_date); ?></h4>
<figure class="wp-block-table">
    <table>
        <thead>
            <tr>
                <th>Service</th>
                <th>Cost Amount in USD</th>
                <th>Amount in ZiG</th>
            </tr>
        </thead>
        <tbody>
            <?php echo $table_rows; ?>
        </tbody>
    </table>
    <figcaption><?php echo esc_html($caption ?? $title); ?></figcaption>
</figure>
<p><strong>Last Updated on <?php echo esc_html($updated_at); ?></strong></p>
