<?php
/**
 * Builds a table of exchange rates for the Zimbabwe Gold (ZiG) currency.
 *
 * This function takes the data returned from the ZIMAPI and formats it into an HTML table
 * with the latest exchange rates. It includes rates for various currencies against ZiG,
 * as well as the ZiG to USD rate and other relevant information.
 *
 * @param array $data The data returned from the ZIMAPI, containing the latest exchange rates.
 * @param array $oe_rates The data for the official exchange rates.
 * @return string The HTML table with the formatted exchange rates.
 */
add_shortcode('show-latest-rates', 'show_latest_exchange_rates');
function show_latest_exchange_rates()
{
    if (is_singular() && has_shortcode(get_post()->post_content, 'show-latest-rates')) {

        $zim_rates = new ZIMAPI(ZIMAPI_BASE);
        $endpoints = [
            'rates' => [
                'endpoint' => '/rates/fx-rates',
            ],
            'oe_rates' => [
                'endpoint' => '/rates/oe-rates/raw',
            ],
        ];
        try {

            $data = $zim_rates->multiCallApi($endpoints, zp_get_remote_ip());

            // require_once plugin_dir_path(__FILE__) . 'templates/rates.php';
            return zp_build_rates_table($data['rates']['data'], $data['oe_rates']['data']);
        } catch (Exception $e) {
            // Log the error
            error_log('Error the latest Exchange Rates: ' . $e->getMessage());
            // Return an error message to the user
            require_once plugin_dir_path(__FILE__) . 'includes/class-show-notice.php';

            return ZP_SHOW_NOTICE::showError("We couldn't retrieve the latest Exchange rates at the moment. Please try again later.");
        }
    }
}

function zp_build_rates_table(array $data, array $oe_rates = [])
{
    // Enqueue the rates badge CSS file and flag icons
    wp_enqueue_style('rates-badge', plugin_dir_url(__FILE__) . 'css/rates-badge.css');
    $date = new DateTime('now', new DateTimeZone('Africa/Harare'));
    $formatted_date = $date->format('l, j F Y');
    $oe_array = build_oe_array($oe_rates);
    $zig_zag_array = zig_zag($oe_array, $data['rates']['ZiG_Mid']);
    $data['rates'] = array_merge($data["rates"], $zig_zag_array);
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

    $headers = [
        '1 USD to ZiG' => 'ZiG_Mid',
        '1 ZiG to USD' => 'zig_to_usd',
        'Maximum Rate Businesses Can Use' => 'max_bus_rate', // 10%
        '1 USD to ZiG Lowest Informal Sector Rate' => 'ZiG_BMBuy',
        '1 USD to ZiG Highest Informal Sector Rate' => 'ZiG_BMSell',
        '1 ZiG to ZWL' => 'ZiG_ZWL',
        '1 USD to ZiG Cash rate' => 'ZiG_Cash',
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

    $rows = '';
    foreach ($headers as $header => $key) {
        if ($key === 'zig_to_usd') {
            $zig_to_usd = number_format(1 / $data['rates']['ZiG_Mid'], 4, '.', '');
            $value = 'US$' . $zig_to_usd;
        } elseif ($key === 'max_bus_rate') {
            $value = number_format($data['rates']['ZiG_Ask'] * 1.0, 4, '.', '') . ' ZiG';
        } else {
            $value = array_key_exists($key, $data['rates']) ? esc_html($data['rates'][$key]) : 'N/A';
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
        if ($key === 'zig_to_usd' && $value !== 'N/A') {
            $value .= "<sup class='rates-badge'>Official</sup>";
        }

        $rows .= "
            <tr>
                <td>{$header}</td>
                <td>{$value}</td>
            </tr>
        ";
    }

    $output = "
    <h4>Zimbabwe Gold (ZiG) Exchange Rates on " . wp_kses_post($formatted_date) . "</h4>

    <figure class='wp-block-table'>
        <table>
            <thead>
                <tr>
                    <th>Rate</th>
                    <th>Value</th>
                </tr>
            </thead>
            <tbody>
                {$rows}
            </tbody>
        </table>
    </figure>
    <p><strong>Last Updated on " . esc_html($data['rates']['updatedAt']) . "</strong></p>
    ";

    // Retrieve rates notes from Carbon Fields
    $rates_notes_title = carbon_get_theme_option('rates_notes_title');
    $rates_notes = carbon_get_theme_option('rates_notes');

    // Append rates notes if they exist
    if ($rates_notes) {
        $output .= "
        <h3 class='wp-block-heading'>" . esc_html($rates_notes_title) . "</h3>
        " . wpautop($rates_notes) . "
        ";
    }

    return $output;
}
if (!function_exists('build_oe_array')) {
    function build_oe_array(array $oe_rates)
    {
        // Array of wanted symbols
        $wanted_currencies = [
            'ZAR', 'BWP', 'ZMW', 'GBP', 'EUR', 'JPY', 'AUD', 'TZS', 'CNY', 'NZD', 'NGN',
        ];

        $oe_array = [];

        foreach ($oe_rates['rates'] as $rate) {
            // Check if $rate is an array
            if (is_array($rate)) {
                // Iterate over each key-value pair inside $rate
                foreach ($rate as $i => $value) {
                    $symbol = key($rate[$i]);
                    if (in_array($symbol, $wanted_currencies)) {
                        $oe_array[$symbol] = $rate[$i][$symbol];
                    }
                }
            }
        }

        return $oe_array;
    }
}
if (!function_exists('zig_zag')) {
    function zig_zag(array $oe_array, float $mid)
    {
        $oe_zig_zag = array();
        //to zig loop
        foreach ($oe_array as $key => $value) {
            $the_key = strtolower($key) . "_to_zig";
            $reverse_key = "zig_to_" . strtolower($key);

            $oe_zig_zag[$the_key] = $mid / $value;
            $oe_zig_zag[$reverse_key] = 1 / $oe_zig_zag[$the_key];
            $oe_zig_zag[$the_key] = number_format($oe_zig_zag[$the_key], 4);
            $oe_zig_zag[$reverse_key] = number_format($oe_zig_zag[$reverse_key], 4);

        }

        return $oe_zig_zag;
    }
}
