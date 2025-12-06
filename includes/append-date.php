<?php

require_once plugin_dir_path(__DIR__) . "includes/dates.php";

// Define the list of auto tracked pages
// Define the list of auto tracked pages
function get_auto_tracked_pages()
{
    $ids = [];
    $associations = carbon_get_theme_option('short_date_posts');
    if ($associations) {
        foreach ($associations as $assoc) {
            $ids[] = $assoc['id'];
        }
    }
    return $ids;
}

function get_inflation_config($post_id)
{
    $config = carbon_get_theme_option('inflation_date_posts');
    if ($config) {
        foreach ($config as $item) {
            if (!empty($item['post']) && $item['post'][0]['id'] == $post_id) {
                return $item;
            }
        }
    }
    return null;
}

// Helper to get the date suffix
function zp_get_date_suffix($post_id) {
    // Check Short Dates
    $thelist = get_auto_tracked_pages();
    if (in_array($post_id, $thelist)) {
        return "-" . zp_month_year();
    } 
    
    // Check Inflation Dates
    $inflation_config = get_inflation_config($post_id);
    if ($inflation_config) {
        $start_date_str = $inflation_config['start_date'];
        $start_ts = strtotime($start_date_str);
        $start_formatted = date('F Y', $start_ts);
        $current = zp_month_year(); 
        
        // Construct label default: " (June 2018-December 2025)"
        return " (" . $start_formatted . "-" . $current . ")";
    }

    return '';
}

// Add date to certain posts
add_filter("the_title", "wpshout_filter_example");
function wpshout_filter_example($title)
{
    if (!in_the_loop()) {
        return $title;
    }
    return $title . zp_get_date_suffix(get_the_ID());
}

// Append the date to the page title bar
add_filter('avada_page_title_bar_contents', 'custom_page_title_bar_contents');
function custom_page_title_bar_contents($contents)
{
    // Avada specific: contents is an array
    $suffix = zp_get_date_suffix(get_the_ID());
    if ($suffix) {
        $contents[0] .= $suffix;
    }
    return $contents;
}

// Append the date to the browser tab title
add_filter('wp_title', 'custom_browser_tab_title', 10, 2);
add_filter('wpseo_title', 'custom_browser_tab_title', 10, 1);
function custom_browser_tab_title($title, $sep = '')
{
    return $title . zp_get_date_suffix(get_the_ID());
}
