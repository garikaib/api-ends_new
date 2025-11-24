<?php

require_once plugin_dir_path(__DIR__) . "includes/dates.php";

// Define the list of auto tracked pages
function get_auto_tracked_pages()
{
    return [
        4887, // Groceries
        9506, // BancABC
        9729, // Nostro Account Opening
        10051, // Harare Parking Fees
        10534, // COVID test centres
        5828, // agric_inputs:
        9518, // deltaa:
        8965, // econet:
        15760, // Econet Smart USD bundles
        9861, // Ecocash Fees
        10456, // OneMoney Fees
        10385, // Telecash Fees
        10075, // telecel:
        5325, // exchange_rates:
        5321, // fuel_gas:
        9709, // liquid:
        13874, // Lamasat
        5281, // mbare:
        10131, // telone:
        10696, // tollgates:
        10078, // utande:
        11445, // zbc_radio_tv:
        9870, // zesa:
        10895, // zupco:
        1235, // Mukuru
        6341, // Passports
        11374, // Traffic safety Council of Zimbabwe
        12965, // Vehicle ownership fees
        9417, // Ways to send money to Zimbabwe
        12316, // Western Union
        12319, // World Remit
        12711, // ZESA calculator
        9344, // NetOne bundles
        16649, // Fines
        17167, // Births and Deaths new
        18785, // LP GAs Prices
    ];
}

// Add date to certain posts
add_filter("the_title", "wpshout_filter_example");
function wpshout_filter_example($title)
{
    $thelist = get_auto_tracked_pages();

    if (in_array(get_the_ID(), $thelist, true) && in_the_loop()) {
        return $title . "-" . zp_month_year();
    } elseif (get_the_ID() === 7647 && in_the_loop()) {
        return $title . " (June 2018-" . zp_month_year() . ")";
    } else {
        return $title;
    }
}

// Append the date to the page title bar
add_filter('avada_page_title_bar_contents', 'custom_page_title_bar_contents');
function custom_page_title_bar_contents($contents)
{
    $thelist = get_auto_tracked_pages();

    if (in_array(get_the_ID(), $thelist, true)) {
        $contents[0] .= "-" . zp_month_year();
    } elseif (get_the_ID() === 7647) {
        $contents[0] .= " (June 2018-" . zp_month_year() . ")";
    }
    return $contents;
}

// Append the date to the browser tab title
add_filter('wp_title', 'custom_browser_tab_title', 10, 2);
add_filter('wpseo_title', 'custom_browser_tab_title', 10, 1);
function custom_browser_tab_title($title, $sep = '')
{
    $thelist = get_auto_tracked_pages();

    if (in_array(get_the_ID(), $thelist, true)) {
        $title .= "-" . zp_month_year();
    } elseif (get_the_ID() === 7647) {
        $title .= " (June 2018-" . zp_month_year() . ")";
    }
    return $title;
}
