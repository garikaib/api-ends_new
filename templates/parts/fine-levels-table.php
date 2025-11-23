<?php
/**
 * Template part for displaying fine levels.
 *
 * @var array $fines_data The fine levels data.
 * @var array $rates_data The rates data.
 */

require_once API_END_BASE . "includes/format-prices.php";
require_once API_END_BASE . "includes/dates.php";

$date = zp_today_full_date();
$fines = isset($fines_data['prices']['fines']) ? $fines_data['prices']['fines'] : [];
$is_usd = !empty($fines) && (isset($fines[0]['usd_amount']) || key_exists('usd_amount', $fines[0]));

// Get ZiG BMSell rate, fallback to ZiG Mid if not available or zero
$zig_rate = isset($rates_data['rates']['ZiG_BMSell']) && $rates_data['rates']['ZiG_BMSell'] > 0 
    ? $rates_data['rates']['ZiG_BMSell'] 
    : (isset($rates_data['rates']['ZiG_Mid']) ? $rates_data['rates']['ZiG_Mid'] : 0);

?>

<div class="fine-levels-table">
    <h4>Fine Levels in Zimbabwe on <?php echo esc_html($date); ?></h4>
    <figure class="wp-block-table is-style-stripes">
        <table>
            <thead>
                <tr>
                    <th scope="col">Level</th>
                    <th scope="col">Standard Fine (<?php echo $is_usd ? 'USD' : 'ZiG'; ?>)</th>
                    <?php if ($is_usd && $zig_rate > 0) : ?>
                        <th scope="col">Est. ZiG Equivalent</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($fines as $fine) : ?>
                    <tr>
                        <td scope="row"><?php echo esc_html($fine['level']); ?></td>
                        <td>
                            <?php
                            $amount = isset($fine['zwl_amount']) ? $fine['zwl_amount'] : (isset($fine['usd_amount']) ? $fine['usd_amount'] : 0);
                            if ($is_usd) {
                                echo zp_format_prices($amount, 'usd');
                            } else {
                                echo '$' . number_format($amount, 0, '.', ' ') . ' ZWG';
                            }
                            ?>
                        </td>
                        <?php if ($is_usd && $zig_rate > 0) : ?>
                            <td>
                                <?php
                                $zig_amount = $amount * $zig_rate;
                                echo '$' . number_format($zig_amount, 0, '.', ' ') . ' ZWG';
                                ?>
                            </td>
                        <?php endif; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <figcaption class="wp-element-caption">Standard fines for various offense levels.</figcaption>
    </figure>

    <?php if ($is_usd && $zig_rate > 0) : ?>
        <p class="has-small-font-size">
            <em>* ZiG equivalents are estimated using a rate of <strong><?php echo number_format($zig_rate, 2); ?></strong>. Actual amounts payable at the station may vary depending on the prevailing rate used by the ZRP.</em>
        </p>
    <?php endif; ?>

    <p>
        <strong><em>Last updated <?php echo esc_html($fines_data['prices']['updatedAt']); ?></em></strong>
    </p>
</div>
