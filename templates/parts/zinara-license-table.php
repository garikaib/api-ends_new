<?php
/**
 * Template for displaying Zinara License fees.
 *
 * Variables available:
 * @var array $prices API response for fees.
 * @var array $rates API response for exchange rates.
 * @var string $currency 'zig' or 'usd'.
 */

if (!defined('ABSPATH')) {
    exit;
}

require_once API_END_BASE . "includes/format-prices.php";
require_once API_END_BASE . "includes/show-price-footer.php";
require_once API_END_BASE . "includes/dates.php";

$wanted_key = ($currency === 'usd') ? 'usd_fees' : 'zig_fees';
$header_currency = ($currency === 'usd') ? 'USD' : 'ZiG';
$format_string = ($currency === 'usd') ? 'usd' : 'zig';

if (!function_exists('zp_calculate_license_fee')) {
    function zp_calculate_license_fee($fee, $months) {
        $defaultMonths = 4;
        $feePerMonth = $fee / $defaultMonths;
        return $feePerMonth * $months;
    }
}

?>

<figure class='wp-block-table'>
    <table>
        <thead>
            <tr>
                <th>Vehicle Class (Net Mass in kg)</th>
                <th>4 Months Fees in <?php echo $header_currency; ?></th>
                <th>6 Months Fees in <?php echo $header_currency; ?></th>
                <th>8 Months Fees in <?php echo $header_currency; ?></th>
                <th>10 Months Fees in <?php echo $header_currency; ?></th>
                <th>12 Months Fees in <?php echo $header_currency; ?></th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($prices['prices']['fees'])): ?>
                <?php foreach ($prices['prices']['fees'] as $fee): ?>
                    <?php 
                        $base_fee = isset($fee[$wanted_key]) ? $fee[$wanted_key] : 0;
                    ?>
                    <tr>
                        <td><?php echo isset($fee['vehicle_class']) ? htmlspecialchars($fee['vehicle_class']) : ''; ?></td>
                        <td><?php echo zp_format_prices($base_fee, $format_string); ?></td>
                        <td><?php echo zp_format_prices(zp_calculate_license_fee($base_fee, 6), $format_string); ?></td>
                        <td><?php echo zp_format_prices(zp_calculate_license_fee($base_fee, 8), $format_string); ?></td>
                        <td><?php echo zp_format_prices(zp_calculate_license_fee($base_fee, 10), $format_string); ?></td>
                        <td><?php echo zp_format_prices(zp_calculate_license_fee($base_fee, 12), $format_string); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6">No data available.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</figure>

<?php 
if (function_exists('zp_show_footer')) {
    echo zp_show_footer();
}
?>
