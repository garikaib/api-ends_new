<?php
/**
 * Template for displaying Tollgate prices.
 *
 * Variables available:
 * @var array $prices API response for tollgate prices.
 * @var array $rates API response for exchange rates.
 * @var string $type 'standard' or 'premium'.
 */

if (!defined('ABSPATH')) {
    exit;
}

// Ensure helper functions are loaded if not already
require_once API_END_BASE . "includes/format-prices.php";
require_once API_END_BASE . "includes/show-price-footer.php";
require_once API_END_BASE . "includes/dates.php";

$is_premium = ($type === 'premium');
$suffix = $is_premium ? '_prem' : '';

$headers = [
    'Vehicle Type' => 'vehicle_type',
    'Toll Gate Fees in USD' => 'usd_price' . $suffix,
    'Toll Gate Fees in ZiG' => 'zig_price' . $suffix,
    'Toll in BWP (Pula)' => 'bwp_price' . $suffix,
    'Toll in ZAR (Rand)' => 'zar_price' . $suffix,
];

$title = $is_premium 
    ? 'ZINARA Tolls At Premium Road Tolling Points on ' . htmlspecialchars(zp_today_full_date())
    : 'ZINARA Tollgate fees at Other Road Tolling Points on ' . htmlspecialchars(zp_today_full_date());

$caption = $is_premium
    ? 'The Latest ZINARA Tollgate Fees Along Premium Roads'
    : 'The Latest ZINARA Tollgate Fees';

?>

<h4><?php echo $title; ?></h4>
<figure class="wp-block-table">
    <table>
        <thead>
            <tr>
                <?php foreach ($headers as $header => $key): ?>
                    <th><?php echo htmlspecialchars($header); ?></th>
                <?php endforeach; ?>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($prices['prices']['prices'])): ?>
                <?php foreach ($prices['prices']['prices'] as $product): ?>
                    <tr>
                        <?php foreach ($headers as $header => $key): ?>
                            <?php 
                                $value = isset($product[$key]) ? $product[$key] : '';
                                // Use zp_getCellValue if available, otherwise just use value
                                if (function_exists('zp_getCellValue')) {
                                    $formattedValue = zp_getCellValue($product, $key);
                                } else {
                                    $formattedValue = $value; 
                                }
                            ?>
                            <td><?php echo htmlspecialchars($formattedValue); ?></td>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="<?php echo count($headers); ?>">No data available.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
    <figcaption><?php echo $caption; ?></figcaption>
</figure>

<?php if (!empty($rates['rates']['updatedAt'])): ?>
    <p><strong>Last Updated on <?php echo htmlspecialchars($rates['rates']['updatedAt']); ?></strong></p>
<?php endif; ?>

<?php 
if (function_exists('zp_show_footer')) {
    echo zp_show_footer();
}
?>
