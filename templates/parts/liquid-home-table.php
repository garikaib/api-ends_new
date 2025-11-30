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
$local_currency_header = "Price in ZiG";
$usd_currency_header = "Price In USD (Official Price)";
$currency_mode = 'zig'; // Default

// Filter packages first to determine available columns
$filtered_packages = [];
$has_night = false;
$has_streaming = false;
$has_priority = false;
$has_voice = false;

foreach ($prices as $p) {
    if (strtolower($p['last_mile']) === $type) {
        $filtered_packages[] = $p;
        
        if (($p['night_data'] ?? 0) > 0) $has_night = true;
        if (($p['netflix_data'] ?? 0) > 0 || ($p['yt_data'] ?? 0) > 0) $has_streaming = true;
        if (($p['priority_data'] ?? 0) > 0) $has_priority = true;
        if (($p['onnet_minutes'] ?? 0) > 0 || ($p['offnet_minutes'] ?? 0) > 0) $has_voice = true;
    }
    
    // Determine currency mode
    if (isset($p['zwl_price']) && $p['zwl_price'] > 0) {
        $currency_mode = 'zwl';
        $local_currency_header = "ZWL Price";
    } elseif (isset($p['zwg_price']) || isset($p['zig_price'])) {
        $currency_mode = 'zwg';
        $local_currency_header = "Price in ZiG";
    }
}

// Get the BMSell and BMBuy rates for calculations
$zig_bmsell_rate = $rates['rates']['ZiG_BMSell'] ?? ($rates['rates']['ZiG_Mid'] ?? 1);
if ($zig_bmsell_rate <= 0) $zig_bmsell_rate = 1;

$zig_bmbuy_rate = $rates['rates']['ZiG_BMBuy'] ?? ($rates['rates']['ZiG_Mid'] ?? 1);
if ($zig_bmbuy_rate <= 0) $zig_bmbuy_rate = 1;

$is_usd_primary = false; // Default, will be set in loop but needed for footer

foreach ($filtered_packages as $product) {
    $last_mile = strtoupper($product['last_mile']);
    $is_usd_primary = (strpos($last_mile, 'USD') !== false) || ($last_mile === 'VSAT') || ($last_mile === 'WIFI');

    $usd_price_display = 'N/A';
    $local_price_display = 'N/A';

    if ($is_usd_primary) {
        // USD Primary Logic
        $usd_val = $product['usd_price'] ?? 0;
        $usd_price_display = zp_format_prices($usd_val, "usd");
        
        // Estimated ZiG Price
        if ($currency_mode === 'zwg') {
             // Use ZiG_BMBuy for USD -> ZiG conversion
             $est_zig = $usd_val * $zig_bmbuy_rate;
             $local_price_display = '$' . number_format($est_zig, 0) . ' ZWG';
             $local_currency_header = "Estimated Cost in ZiG";
        } else {
             $local_price_display = 'N/A';
        }
    } else {
        // ZiG Primary Logic
        $local_val = $product['zwg_price'] ?? ($product['zig_price'] ?? 0);
        
        if ($currency_mode === 'zwg') {
            $local_price_display = '$' . number_format($local_val, 0) . ' ZWG';
            $local_currency_header = "Price in ZiG";
        } elseif ($currency_mode === 'zwl') {
            $local_val = $product['zwl_price'] ?? 0;
            $local_price_display = zp_format_prices($local_val, "zwl");
            $local_currency_header = "ZWL Price";
        }

        // Estimated USD Price
        // Use ZiG_BMSell for ZiG -> USD conversion
        $est_usd = $local_val / $zig_bmsell_rate;
        $usd_price_display = zp_format_prices($est_usd, "usd");
        $usd_currency_header = 'Estimated Price in $USD';
    }

    $product_table .= '<tr>';
    $product_table .= '<td>' . esc_html($product['package_name']) . '</td>';
    $product_table .= '<td>' . ZP_Liquid_Home::liquid_is_limited('data', $product) . '</td>';
    
    // Night / Other Data Column
    if ($has_streaming || $has_night) {
        $product_table .= '<td>';
        if ($has_streaming) {
            $product_table .= '<ul style="list-style: none; padding: 0; margin: 0;">';
            if (($product['night_data'] ?? 0) > 0) $product_table .= '<li>Night: ' . esc_html($product['night_data']) . 'GB</li>';
            if (($product['netflix_data'] ?? 0) > 0) $product_table .= '<li>Netflix: ' . esc_html($product['netflix_data']) . 'GB</li>';
            if (($product['yt_data'] ?? 0) > 0) $product_table .= '<li>YouTube: ' . esc_html($product['yt_data']) . 'GB</li>';
            $product_table .= '</ul>';
        } else {
            $product_table .= ZP_Liquid_Home::liquid_is_limited('night_data', $product);
        }
        $product_table .= '</td>';
    }

    // Priority Data Column
    if ($has_priority) {
        $priority_val = $product['priority_data'] ?? 0;
        $priority_display = ($priority_val >= 1000) ? ($priority_val / 1000) . 'TB' : $priority_val . 'GB';
        $product_table .= '<td>' . esc_html($priority_display) . '</td>';
    }

    // Call Minutes Column
    if ($has_voice) {
        $product_table .= '<td><ul style="list-style: none; padding: 0; margin: 0;">';
        if (($product['onnet_minutes'] ?? 0) > 0) $product_table .= '<li>On-net: ' . esc_html($product['onnet_minutes']) . ' min</li>';
        if (($product['offnet_minutes'] ?? 0) > 0) $product_table .= '<li>Off-net: ' . esc_html($product['offnet_minutes']) . ' min</li>';
        $product_table .= '</ul></td>';
    }

    $product_table .= '<td>' . $usd_price_display . '</td>';
    $product_table .= '<td>' . $local_price_display . '</td>';
    $product_table .= '</tr>';
}
?>

<h4>Liquid Home (ZOL) <?php echo esc_html($header_text); ?> Internet Packages Prices on <?php echo esc_html($date); ?></h4>
<figure class='wp-block-table'>
    <table>
        <thead>
            <tr>
                <th>Package Name</th>
                <th>Normal Data(GB)</th>
                <?php if ($has_streaming): ?>
                    <th>Other Data</th>
                <?php elseif ($has_night): ?>
                    <th>Night Data(GB)</th>
                <?php endif; ?>
                <?php if ($has_priority): ?>
                    <th>Priority Data</th>
                <?php endif; ?>
                <?php if ($has_voice): ?>
                    <th>Call Minutes</th>
                <?php endif; ?>
                <th><?php echo $usd_currency_header; ?></th>
                <th><?php echo $local_currency_header; ?></th>
            </tr>
        </thead>
        <tbody>
            <?php echo $product_table; ?>
        </tbody>
    </table>
    <figcaption>Latest Liquid Home <?php echo esc_html($header_text); ?> Prices</figcaption>
</figure>
<p><strong>Last Updated on <?php echo esc_html($rates['rates']['updatedAt'] ?? 'N/A'); ?></strong></p>
<?php 
$calculated_currency = $is_usd_primary ? 'ZiG' : 'USD';
echo ZP_Table_Footer::render([
    'calculated_currency' => $calculated_currency,
    'bundle_description' => $header_text
]); 
?>
