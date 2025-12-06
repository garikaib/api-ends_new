<?php
/**
 * Template for displaying Intercity Bus Fares.
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

?>

<h4>Zimbabwe Intercity Bus Fares on <?php echo htmlspecialchars(zp_today_full_date()); ?></h4>
<figure class='wp-block-table'>
    <table>
        <thead>
            <tr>
                <th>From</th>
                <th>To</th>
                <th>Distance in KM</th>
                <th>Bus Fare in US$</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($prices['prices']['fares'])): ?>
                <?php foreach ($prices['prices']['fares'] as $product): ?>
                    <tr>
                        <td><?php echo isset($product['from']) ? htmlspecialchars($product['from']) : ''; ?></td>
                        <td><?php echo isset($product['to']) ? htmlspecialchars($product['to']) : ''; ?></td>
                        <td><?php echo isset($product['distance']) ? htmlspecialchars($product['distance']) . ' km' : ''; ?></td>
                        <td><?php echo zp_format_prices(isset($product['usd_fare']) ? $product['usd_fare'] : 0); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4">No data available.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
    <figcaption>The Latest Intercity Bus Fares</figcaption>
</figure>

<?php if (!empty($rates['rates']['updatedAt'])): ?>
    <p><strong>Last Updated on <?php echo htmlspecialchars($rates['rates']['updatedAt']); ?></strong></p>
<?php endif; ?>
