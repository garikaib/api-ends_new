<?php
/**
 * Template part for displaying ZESA Tariffs Explanation.
 *
 * Variables:
 * - $data: Combined data array
 * - $zesaService: Instance of ZesaService (passed from controller)
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
$zig_mid = $rates['ZiG_Mid'] ?? 1; // Avoid div by zero

// Sort bands
usort($bands, fn($a, $b) => $a['min_units'] - $b['min_units']);

$date_str = isset($prices_data['prices']['Date']) ? date_i18n("l, d F Y", strtotime($prices_data['prices']['Date'])) : 'Unknown Date';
$cheap_total_zig = $zesaService->getCheapQuotaCost($prices_data, true);
$cheap_total_usd = ($zig_mid > 0) ? ($cheap_total_zig / $zig_mid) : 0;
$discounted_units = 0;

$band_totals = $zesaService->getBandTotals($prices_data);
if (!empty($band_totals)) {
    $last_total = end($band_totals);
    $discounted_units = $last_total['cumulative_total'] ?? 0;
}

$highest_price = $zesaService->getHighestZigPriceRea($prices_data);

// Helper for date logic (Next month first day etc)
$next_month_first = date_i18n('j F', strtotime('first day of next month'));
$current_month = date_i18n('F');

// Helper to generate band list HTML
$band_list = "<ul>";
if (!empty($bands)) {
    $band_num = 0;
    $band_count = count($bands);
    foreach ($bands as $product) {
        $desc = strtolower($product['description']);
        $units_desc = (strpos($desc, 'units') === false) ? ' Units' : '';
        $the_prefix = ($band_num === 0) ? ' the ' : '';
        
        $price_zig = $product['zig_price_rea'] ?? 0;
        $price_usd = isset($product['usd_price']) ? $product['usd_price'] : (($zig_mid > 0) ? ($price_zig / $zig_mid) : 0);

        $total_text = '';
        // Add total text for non-last bands
        if ($band_num < ($band_count - 1) && isset($band_totals[$band_num])) {
            $bt = $band_totals[$band_num];
            $total_cost_zig = $bt['total_units'] * $bt['zig_price_rea'];
            $cumulative_cost = $zesaService->calculateCost($prices_data, $bt['cumulative_total']);
            
            $total_text = ", for a total of " . PriceUtil::format($total_cost_zig, 'zig') . 
                          ". The total discounted units up to this point are " . $bt['cumulative_total'] . 
                          " units which will cost you a total of " . PriceUtil::format($cumulative_cost, 'zig');
        }

        $band_list .= "<li>For " . $the_prefix . esc_html($desc) . $units_desc . 
                      ", you will pay " . PriceUtil::format($price_zig, 'zig') . " per unit (about " . 
                      PriceUtil::format($price_usd, 'usd') . " per unit)" . $total_text . "</li>";
        
        $band_num++;
    }
}
$band_list .= "</ul>";

// Generate Cost Table
$cost_table_rows = '';
$rea_factor = $zesaService->getReaFactor($prices_data);
$units_to_show = [50, 100];
for ($i = 1; $i <= 10; $i++) $units_to_show[] = ($i + 1) * 100;

foreach ($units_to_show as $u) {
    $z_price = $zesaService->calculateCost($prices_data, $u);
    $c_excl = ($rea_factor > 0) ? ($z_price / $rea_factor) : $z_price;
    $rea_val = $z_price - $c_excl;
    
    $cost_table_rows .= "<tr>
        <td>{$u}</td>
        <td>" . PriceUtil::format($c_excl, 'zig') . "</td>
        <td>" . PriceUtil::format($rea_val, 'zig') . "</td>
        <td>" . PriceUtil::format($z_price, 'zig') . "</td>
    </tr>";
}
?>

<h4>When will the tariffs start to apply?</h4>
<p>According to the approval given by ZERA, the tariffs are coming into effect immediately. The new tariffs came into effect on <?php echo esc_html($date_str); ?>. It is important to note that sometimes tariffs just come into effect without being publicly announced.</p>

<h4>What are the current tariffs for each band?</h4>
<p>If you're looking to save money on your ZESA bill, it's important to understand the stepped tariff system. With this system, the more power you consume, the more you'll pay per unit. Here are the current tariffs for each band:</p>
<?php echo $band_list; ?>

<h5>NB</h5>
<p>In order for you to take advantage of the affordable bands, you need to spend almost <?php echo PriceUtil::format($cheap_total_zig, 'zig'); ?> (<?php echo PriceUtil::format($cheap_total_usd, 'usd'); ?>) after levy. This will get you <?php echo $discounted_units; ?> kWh of electricity. If you spend more than this everything above <?php echo PriceUtil::format($cheap_total_zig, 'zig'); ?> (<?php echo PriceUtil::format($cheap_total_usd, 'usd'); ?>) will be charged at the expensive tariff of <?php echo PriceUtil::format($highest_price, 'zig'); ?> per unit. If you spend less it means you are not taking advantage of the preferential tariffs available to you.</p>

<h4>Is electricity cheaper on the first day of the month?</h4>
<p>The answer is yes and no. Each month you are entitled to a discounted <?php echo $discounted_units; ?> units (kWh) of electricity which costs about <?php echo PriceUtil::format($cheap_total_zig, 'zig'); ?> (<?php echo PriceUtil::format($cheap_total_usd, 'usd'); ?>) at current tariffs.</p>
<!-- Abbreviated text for clarity and maintenance, keeping core logic -->
<p>This quota is restored on the first day of each month. This means that if you bought up all your <?php echo $discounted_units; ?> kWh in <?php echo $current_month; ?> from <?php echo $next_month_first; ?> you can now buy that <?php echo $discounted_units; ?> kWh at <?php echo PriceUtil::format($cheap_total_zig, 'zig'); ?> (<?php echo PriceUtil::format($cheap_total_usd, 'usd'); ?>).</p>

<h4>What is a stepped tariff?</h4>
<figure class='wp-block-table'>
    <table>
        <thead>
            <tr><th>Units</th><th>Cost Excl REA</th><th>REA in ZiG</th><th>Total Charge (ZiG)</th></tr>
        </thead>
        <tbody>
            <?php echo $cost_table_rows; ?>
        </tbody>
    </table>
</figure>

<p>ZESA does not use a flat tariff like it used to. Instead, they use a stepped tariff. What this means is that the first few units you buy are cheaper.</p>
<h4>I am still confused how many units will I get if I spend this much?</h4>
<p>How much you will get depends on how much electricity you have already bought in that particular month. If this is your first time buying electricity that month you will get electricity at a cheaper rate.<a href='https://zimpricecheck.com/price-updates/zesa-tariff-and-token-calculator/'> You can use our ZESA calculator here to know exactly how many units you will get.</a></p>
