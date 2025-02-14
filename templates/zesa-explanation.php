<?php

require_once plugin_dir_path(__DIR__) . "includes/format-data.php";
require_once plugin_dir_path(__DIR__) . "includes/usd-equivalent.php";
require_once plugin_dir_path(__DIR__) . "includes/show-price-footer.php";
require_once plugin_dir_path(__DIR__) . "includes/format-prices.php";
require_once plugin_dir_path(__DIR__) . "includes/zesa-tariff-calculator.php";
require_once plugin_dir_path(__DIR__) . "includes/zesa-highest-zig.php";

function buildZESAExp(array $data, array $rates)
{
    $discounted = total_discounted_units($data);
    return wp_kses_post("<h4>When will the tariffs start to apply?</h4>
    <p>According to the approval given by ZERA, the tariffs are coming into effect immediately. The new tariffs came into effect on " .
        esc_html(date_i18n("l, d F Y", strtotime($data["prices"]["Date"]))) .
        ". It is important to note that sometimes tariffs just come into effect without being publicly announced.</p>
    <h4>What are the current tariffs for each band?</h4>
<p>If you're looking to save money on your ZESA bill, it's important to understand the stepped tariff system. With this system, the more power you consume, the more you'll pay per unit. Here are the current tariffs for each band:</p>
" .
        zesa_exp_bands($data, $rates) .
        "
<h5>NB</h5>
<p>In order for you to take advantage of the affordable bands, you need to spend almost " .
        zp_format_prices(cheap_zesa_total($data, true), "zig") .
        " (US" .
        zp_format_prices(
            zp_to_usd(
                $rates["rates"]["ZiG_Mid"],
                cheap_zesa_total($data, true),
                ""
            )
        ) .
        ") after levy. This will get you " . $discounted . " kWh of electricity. If you spend more than this everything above " .
        zp_format_prices(cheap_zesa_total($data, true), "zig") .
        " (US" .
        zp_format_prices(
            zp_to_usd(
                $rates["rates"]["ZiG_Mid"],
                cheap_zesa_total($data, true)
            )
        ) .
        ")" .
        " will be charged at the expensive tariff of " . zp_format_prices(get_highest_zig_price_rea($data), "zig") . " per unit. If you spend less it means you are not taking advantage of the preferential tariffs available to you.</p>
<h4>Is electricity cheaper on the first day of the month?</h4>
<p>The answer is yes and no. Each month you are entitled to a discounted " . $discounted . " units (kWh) of electricity which costs about  " .
        zp_format_prices(cheap_zesa_total($data, true), "zig") .
        " (US$" .
        zp_format_prices(
            zp_to_usd(
                $rates["rates"]["ZiG_Mid"],
                cheap_zesa_total($data, true),
                "usd"
            )
        ) .
        ")" .
        " at current tariffs. So the first  " .
        zp_format_prices(cheap_zesa_total($data, true), "zig") .
        "  (US$" .
        zp_format_prices(
            zp_to_usd(
                $rates["rates"]["ZiG_Mid"],
                cheap_zesa_total($data, true),
                "usd"
            )
        ) .
        ")" .
        " you spend gets you " . $discounted . " kWh of electricity. Therefore even if you buy electricity on the 10th as long as it is your first purchase of the month your " . zp_format_prices(cheap_zesa_total($data, true), "zig") .
        "  (US$" .
        zp_format_prices(
            zp_to_usd(
                $rates["rates"]["ZiG_Mid"],
                cheap_zesa_total($data, true),
                "usd"
            )
        ) .
        ")" . " will get you " . $discounted . "kWh. This quota is restored on the first day of each month. This means that if you bought up all your " . $discounted . " kWh in " . date_i18n('F') . " from " . get_next_month_first_day() . " you can now buy that " . $discounted . " kWh at " .
        zp_format_prices(cheap_zesa_total($data, true), "zig") .
        " (US" .
        zp_format_prices(
            zp_to_usd(
                $rates["rates"]["ZiG_Mid"],
                cheap_zesa_total($data, true),
                "usd"
            )
        ) .
        ")" .
        " You can buy that amount on " . get_next_month_first_day() . " or " . get_specified_date(20) . ", it doesn’t matter, so long as it’s your first purchase and it’s " . get_specified_date(1, false) . ".</p>
<p>Each new month the quota resets. This has resulted in the myth that electricity is cheaper on the first day of each month. It’s not true, each month your cheap quota of " . $discounted . " kWh is restored.</p>
<h4>What is a stepped tariff?</h4>" . generate_electricity_cost_table($data) .
        "
<p>ZESA does not use a flat tariff like it used to. Instead, they use a stepped tariff. What this means is that the first few units you buy are cheaper. So, for example, the " . strtolower(get_band_description($data, 1)) . " you buy each month attract a charge of " . zp_format_prices(get_band_price($data, 1), "zig") . " per unit. The next " . get_band_description($data, 2) . " units are charged a rate of " . zp_format_prices(get_band_price($data, 2), "zig") . " . The idea is to make sure those who are poor can afford electricity but also make sure that those who use a lot of electricity pay more. So the more you use the more expensive the electricity becomes. Each month you get " . $discounted . " kWh you can buy at a discounted rate.</p>
<h4>I am still confused how many units will I get if I spend this much?</h4>
<p>How much you will get depends on how much electricity you have already bought in that particular month. If this is your first time buying electricity that month you will get electricity at a cheaper rate.<a href='https://zimpricecheck.com/price-updates/zesa-tariff-and-token-calculator/'> You can use our ZESA calculator here to know exactly how many units you will get.</a> As already said above you can always buy your cheap units anytime during the month and not necessarily on the first of each month.</p>
");
}

function zesa_exp_bands(array $data, array $rates)
{
    $band_num = 0;
    $band_totals = null;
    $band_list = "<ul>";

    // Check if prices and bands keys exist before trying to iterate through them
    if (isset($data["prices"]["bands"]) && is_array($data["prices"]["bands"])) {
        // Sort the data by min_units
        usort($data["prices"]["bands"], function ($a, $b) {
            return $a["min_units"] <=> $b["min_units"];
        });
        $band_totals = get_band_totals($data);
        $bands = $data['prices']['bands'];
        $band_count = count($bands);
        $the = null;

        foreach ($data["prices"]["bands"] as $product) {
            $pos = strpos(strtolower($product["description"]), strtolower("units"));
            $units_desc = "";
            if ($pos === false) {
                $units_desc = " Units";
            }
            $the = $band_num === 0 ? " the " : "";
            $band_list .=
            "<li>For " . $the .
            esc_html(strtolower($product["description"])) .
            $units_desc .
            ", you will pay " .
            esc_html(zp_format_prices($product["zig_price_rea"], 'zig')) .
            " per unit (about " .
            esc_html(zp_format_prices(

                $product["usd_price"]
                ,
                "usd"
            )) .
            " per unit), " . zp_show_band_total($band_totals, $band_count, $data, $band_num) . "</li>";
            $band_num++;
        }
    }

    $band_list .= "</ul>";

    return $band_list;
}
/**
 * Calculates the total units and cumulative total for each band, except for the last band with infinite units.
 *
 * @param array $prices An array of prices containing band information.
 *
 * @return array An array containing the total units and cumulative total for each band.
 */

function get_band_totals(array $data)
{
    $bands = $data['prices']['bands'];
    $band_totals = [];

    $cumulative_total = 0;

    // Iterate over all but the last band
    for ($i = 0; $i < count($bands) - 1; $i++) {
        $band = $bands[$i];
        $total_units = $band['max_units'] - $band['min_units'];
        $cumulative_total += $total_units;

        $band_totals[$i] = [
            'total_units' => $total_units,
            'cumulative_total' => $cumulative_total,
            'zig_price_rea' => $band['zig_price_rea'],
        ];
    }

    return $band_totals;
}
function total_discounted_units(array $prices)
{
    $totals = get_band_totals($prices);
    $count = count($totals);
    return $totals[$count - 1]["cumulative_total"];
}
function zp_show_band_total(array $band_totals, int $band_count, array $price_data, int $band_num)
{

    if ($band_num < ($band_count - 1)) {
        return 'for a total of  ' . zp_format_prices($band_totals[$band_num]['total_units'] * $band_totals[$band_num]['zig_price_rea'], "zig") . ". The total discounted units up to this point are " . $band_totals[$band_num]['cumulative_total'] . " units which will cost you a total of " . zp_format_prices(calculate_electricity_cost($price_data, $band_totals[$band_num]['cumulative_total']), "zig");
    } else {return "";}
}
/**
 * Calculates the total discounted cost of electricity given the bands. It ignores the last band.
 *
 * @param array $data An array containing the electricity consumption data.
 * @param bool $round Determines whether or not the total should be rounded to the nearest hundred.
 *
 * @return float The total cost for the given set of electricity consumption data.
 */
function cheap_zesa_total(array $data, bool $round = false): float
{
    $band_total = 0;
    $prices = $data["prices"]["bands"];
    usort($prices, function ($a, $b) {
        return $a['min_units'] - $b['min_units'];
    });

    $band_count = count($prices);
    $count = 0;

    foreach ($prices as $product) {
        if ($count < ($band_count - 1)) {
            $units = $product["max_units"] - $product["min_units"];
            $band_total += $units * $product["zig_price_rea"];
            $count++;
        }
    }
    // error_log("Here is the inside total", $band_total);
    if ($round) {
        return ceil($band_total / 100) * 100;
    } else {
        return $band_total;
    }
}

/**
 * Returns the date string for the 1st of next month in the format "1 July".
 *
 * @return string The date string for the 1st of next month.
 */
function get_next_month_first_day()
{
    $next_month_timestamp = strtotime('first day of next month');
    $next_month_date = date_i18n('j F', $next_month_timestamp);
    return $next_month_date;
}

/**
 * Get the specified day of the next month.
 *
 * @param int $day The day of the month to return.
 * @return string The specified date in the format "j F".
 */
function get_specified_date(int $day, bool $show_day_num = true)
{
    // Get the timestamp for the first day of next month.
    $next_month = strtotime('first day of +1 month', current_time('timestamp'));

    // Get the timestamp for the specified day of next month.
    $specified_date = strtotime(sprintf('%d %s', $day, date('F Y', $next_month)));

    // If the specified day doesn't exist in next month, return the last day of next month.
    if (!checkdate(date('m', $next_month), $day, date('Y', $next_month))) {
        $last_day = date('t', $specified_date);
        $specified_date = strtotime(sprintf('%d %s', $last_day, date('F Y', $next_month)));
    }

    // Return the specified date in the format "j F".
    return $show_day_num ? date_i18n('j F', $specified_date) : date_i18n('F', $specified_date);
}

/**
 * Returns the zig_price_rea of the nth band or 0 if it doesn't exist.
 *
 * @param array $prices The electricity cost price bands.
 * @param int $nth_band The index of the band to get the price from.
 *
 * @return float The zig_price_rea of the nth band, or 0 if it doesn't exist.
 */
function get_band_price($prices, $nth_band)
{
    try {
        $bands = isset($prices['prices']['bands']) ? $prices['prices']['bands'] : (isset($prices['bands']) ? $prices['bands'] : (isset($prices[0]['description']) ? $prices : null));

        usort($bands, function ($a, $b) {
            return $a['min_units'] - $b['min_units'];
        });

        $band = $bands[$nth_band - 1] ?? null;
        return isset($band) ? $band['zig_price_rea'] : 0;
    } catch (Exception $e) {
        error_log("Error in get_nth_band_price: " . $e->getMessage());
        return 0;
    }
}

//Return description instead of band prices
function get_band_description($prices, $n)
{
    try {
        $bands = isset($prices['prices']['bands']) ? $prices['prices']['bands'] : (isset($prices['bands']) ? $prices['bands'] : (isset($prices[0]['description']) ? $prices : null));
        usort($bands, function ($a, $b) {
            return $a['min_units'] - $b['min_units'];
        });
        if (count($bands) < $n) {
            return "";
        }
        return $bands[$n - 1]['description'];
    } catch (Exception $e) {
        error_log("Error getting band description: " . $e->getMessage());
        return "";
    }
}

function generate_electricity_cost_table($prices)
{
    $headings = array("Units", "Cost Excl REA", "REA in ZiG", "Total Charge (ZiG)");

    $table = "    <figure class='wp-block-table'>
    <table><thead><tr>";
    foreach ($headings as $heading) {
        $table .= "<th>{$heading}</th>";
    }
    $table .= "</tr></thead><tbody>";

    $units = array(50, 100);
    for ($i = 1; $i <= 10; $i++) {
        $units[] = ($i + 1) * 100;
    }

    foreach ($units as $unit) {
        $zig_price = calculate_electricity_cost($prices, $unit);
        $cost_excl_rea = $zig_price / zp_get_rea($prices);
        $rea_zwl = $zig_price - $cost_excl_rea;
        $total_charge = $zig_price;
        //Let's now format as we are done with calculations
        $zig_price = zp_format_prices($zig_price, "zig");
        $cost_excl_rea = zp_format_prices($cost_excl_rea, "zig");
        $rea_zwl = zp_format_prices($rea_zwl, "zig");
        $total_charge = $zig_price;

        $table .= "<tr><td>{$unit}</td><td>{$cost_excl_rea}</td><td>{$rea_zwl}</td><td>{$total_charge}</td></tr>";
    }

    $table .= "</tbody></table></figure>";
    return $table;
}
/**
 * Computes the REA factor to two decimal places based on the latest electricity prices.
 *
 * @param array $latest_prices An array of the latest electricity prices.
 *
 * @return float|null The computed REA factor or null if the input is invalid.
 */
function zp_get_rea($latest_prices)
{
    $bands = isset($latest_prices['prices']['bands']) ? $latest_prices['prices']['bands'] : (isset($latest_prices['bands']) ? $latest_prices['bands'] : (isset($latest_prices[0]['description']) ? $latest_prices : null));

    if (empty($bands)) {
        return null;
    }

    $min_rea = PHP_FLOAT_MAX;
    foreach ($bands as $band) {
        $rea = $band['zig_price_rea'] / $band['zig_price'];
        if ($rea < $min_rea) {
            $min_rea = $rea;
        }
    }

    return number_format($min_rea, 2);
}
