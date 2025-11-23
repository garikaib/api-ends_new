<?php
/**
 * Template part for displaying latest exchange rates.
 *
 * @var array $processed_data The processed rates data.
 */

wp_enqueue_style('zpc-rates-badge', API_END_URL . 'assets/css/rates-badge.css', [], '1.0.2');

$date = new DateTime('now', new DateTimeZone('Africa/Harare'));
$formatted_date = $date->format('l, j F Y');

$curr_symbols = [
    "aud_to_zig" => "ZiG",
    "zig_to_aud" => "AUS$",
    "bwp_to_zig" => "ZiG",
    "zig_to_bwp" => "P",
    "cny_to_zig" => "ZiG",
    "zig_to_cny" => "CN¥",
    "eur_to_zig" => "ZiG",
    "zig_to_eur" => "€",
    "gbp_to_zig" => "ZiG",
    "zig_to_gbp" => "£",
    "jpy_to_zig" => "ZiG",
    "zig_to_jpy" => "JP¥",
    "nzd_to_zig" => "ZiG",
    "zig_to_nzd" => "NZ$",
    "tzs_to_zig" => "ZiG",
    "zig_to_tzs" => "TSh",
    "zar_to_zig" => "ZiG",
    "zig_to_zar" => "R",
    "zmw_to_zig" => "ZiG",
    "zig_to_zmw" => "ZMW",
    'zig_to_usd' => 'US$',
    'ngn_to_zig' => 'ZiG',
    'zig_to_ngn' => '₦',
    'ZiG_Mid' => 'ZiG',
    'ZiG_Ask' => 'ZiG',
    'ZiG_Cash' => 'ZiG',
    'ZiG_ZWL' => 'ZWL$',
    'ZiG_Bid' => 'ZiG',
    'ZiG_BMBuy' => 'ZiG',
    'ZiG_BMSell' => 'ZiG',
];

$usd_headers = [
    '1 USD to ZiG' => 'ZiG_Mid',
    '1 ZiG to USD' => 'zig_to_usd',
    'Maximum Rate Businesses Can Use' => 'max_bus_rate', // 10%
    '1 USD to ZiG Lowest Informal Sector Rate' => 'ZiG_BMBuy',
    '1 USD to ZiG Highest Informal Sector Rate' => 'ZiG_BMSell',
    '1 ZiG to ZWL' => 'ZiG_ZWL',
    '1 USD to ZiG Cash rate' => 'ZiG_Cash',
];

$cross_headers = [
    'ZiG to South African Rand (ZAR)' => 'zig_to_zar',
    '1 South African Rand to ZiG' => 'zar_to_zig',
    '1 ZiG to Botswana Pula' => 'zig_to_bwp',
    '1 Botswana Pula to ZiG' => 'bwp_to_zig',
    '1 ZiG to Zambian Kwacha' => 'zig_to_zmw',
    '1 Zambian Kwacha to ZiG' => 'zmw_to_zig',
    '1 ZiG to British Pound' => 'zig_to_gbp',
    '1 British Pound to ZiG' => 'gbp_to_zig',
    '1 Euro to ZiG' => 'eur_to_zig',
    '1 Japanese Yen to ZiG' => 'jpy_to_zig',
    '1 Australian Dollar to ZiG' => 'aud_to_zig',
    '1 ZiG to Tanzanian Shillings' => 'zig_to_tzs',
    '1 ZiG to Chinese Yuan' => 'zig_to_cny',
    '1 New Zealand Dollar to ZiG' => 'nzd_to_zig',
    '1 Nigerian Naira to ZiG' => 'ngn_to_zig',
    '1 ZiG to Nigerian Naira' => 'zig_to_ngn',
];

function zp_generate_rows($headers, $processed_data, $curr_symbols) {
    $rows = '';
    foreach ($headers as $header => $key) {
        if ($key === 'zig_to_usd') {
            $zig_to_usd = number_format(1 / $processed_data['rates']['ZiG_Mid'], 4, '.', '');
            $value = 'US$' . $zig_to_usd;
        } elseif ($key === 'max_bus_rate') {
            $value = number_format($processed_data['rates']['ZiG_Ask'] * 1.0, 4, '.', '') . ' ZiG';
        } else {
            $value = array_key_exists($key, $processed_data['rates']) ? esc_html($processed_data['rates'][$key]) : 'N/A';
            if ($value !== 'N/A' && is_numeric($value)) {
                $value = number_format($value, 4, '.', '');
                $symbol = isset($curr_symbols[$key]) ? $curr_symbols[$key] : '';
                if ($symbol === 'ZiG') {
                    $value .= ' ' . $symbol;
                } else {
                    $value = $symbol . $value;
                }
            }
        }
        if ($key === 'ZiG_Mid' && $value !== 'N/A') {
            $value .= "<sup class='zpc-rates-badge-official'>Official</sup>";
        }

        $rows .= "
            <tr>
                <td>{$header}</td>
                <td>{$value}</td>
            </tr>
        ";
    }
    return $rows;
}

$usd_rows = zp_generate_rows($usd_headers, $processed_data, $curr_symbols);
$cross_rows = zp_generate_rows($cross_headers, $processed_data, $curr_symbols);
?>

<h4>Zimbabwe Gold (ZiG) Exchange Rates on <?php echo wp_kses_post($formatted_date); ?></h4>

<h5 class="wp-block-heading">USD Exchange Rates</h5>
<figure class='wp-block-table'>
    <table>
        <thead>
            <tr>
                <th>Rate</th>
                <th>Value</th>
            </tr>
        </thead>
        <tbody>
            <?php echo $usd_rows; ?>
        </tbody>
    </table>
</figure>

<?php if (!empty($processed_data['rates']['business_rates']) && is_array($processed_data['rates']['business_rates'])) : ?>
    <h5 class="wp-block-heading">Business Rates</h5>
    <figure class='wp-block-table'>
        <table>
            <thead>
                <tr>
                    <th>Business/Shop</th>
                    <th>Rate</th>
                    <th>Currency</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($processed_data['rates']['business_rates'] as $business) : ?>
                    <tr>
                        <td><?php echo esc_html($business['name']); ?></td>
                        <td><?php echo esc_html(number_format($business['rate'], 4)); ?></td>
                        <td><?php echo esc_html($business['currency']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </figure>
<?php endif; ?>

<h5 class="wp-block-heading">Cross Rates</h5>
<figure class='wp-block-table'>
    <table>
        <thead>
            <tr>
                <th>Rate</th>
                <th>Value</th>
            </tr>
        </thead>
        <tbody>
            <?php echo $cross_rows; ?>
        </tbody>
    </table>
</figure>
<p><strong>Last Updated on <?php echo esc_html($processed_data['rates']['updatedAt']); ?></strong></p>

<?php
// Retrieve rates notes from Carbon Fields
$rates_notes_title = carbon_get_theme_option('rates_notes_title');
$rates_notes = carbon_get_theme_option('rates_notes');

// Append rates notes if they exist
if ($rates_notes) {
    echo "
    <h3 class='wp-block-heading'>" . esc_html($rates_notes_title) . "</h3>
    " . wpautop($rates_notes) . "
    ";
}
?>
