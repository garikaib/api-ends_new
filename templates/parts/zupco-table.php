<?php
/**
 * Template for displaying ZUPCO fares.
 *
 * Variables available:
 * @var array $prices API response for fares.
 * @var array $rates API response for exchange rates.
 */

if (!defined('ABSPATH')) {
    exit;
}

require_once API_END_BASE . "includes/format-prices.php";
require_once API_END_BASE . "includes/show-price-footer.php";
require_once API_END_BASE . "includes/dates.php";

$zig_rate = isset($rates["rates"]["ZiG_Mid"]) ? $rates["rates"]["ZiG_Mid"] : 0;

?>

<div class="fuel-prices-table">
<h4>ZUPCO Fares Table on <?php echo htmlspecialchars(zp_today_full_date()); ?></h4>
<figure class='wp-block-table'>
    <table>
        <thead>
            <tr>
                <th>Distance</th>
                <th>ZUPCO Bus Fare ZiG</th>
                <th>ZUPCO Bus USD</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($prices['prices']['fares'])): ?>
                <?php foreach ($prices['prices']['fares'] as $product): ?>
                    <?php 
                        $usd_price = isset($product['bus_usd_price']) ? $product['bus_usd_price'] : 0;
                        $zig_price = $usd_price * $zig_rate;
                    ?>
                    <tr>
                        <td><?php echo isset($product['route']) ? htmlspecialchars($product['route']) : ''; ?></td>
                        <td><?php echo zp_format_prices($zig_price, "zig"); ?></td>
                        <td><?php echo zp_format_prices($usd_price, "usd"); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="3">No data available.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
    <figcaption>The Latest ZUPCO Kombi and Bus Fares</figcaption>
</figure>

<?php if (!empty($rates['rates']['updatedAt'])): ?>
    <p><strong>Last Updated on <?php echo htmlspecialchars($rates['rates']['updatedAt']); ?></strong></p>
<?php endif; ?>
</div>

<?php 
if (function_exists('zp_show_footer')) {
    echo zp_show_footer();
}
?>
