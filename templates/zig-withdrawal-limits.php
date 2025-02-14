<?php
// Including necessary file for price formatting
require_once plugin_dir_path(__DIR__) . 'includes/format-prices.php';

/**
 * Retrieves the current withdrawal limits.
 *
 * @return array The array of current withdrawal limits.
 */
function get_current_limits()
{
    return [
        "individual_weekly" => 3000,
        "individual_monthly" => 30000,
        "corporate_weekly" => 30000,
        "corporate_monthly" => 250000,
        "school_weekly" => 30000,
        "school_monthly" => 250000,
        "hospital_weekly" => 3000,
        "hospital_monthly" => 250000,
        "government_weekly" => 70000,
        "government_monthly" => 300000,
    ];
}

/**
 * Generates table rows for the withdrawal limits table.
 *
 * @param array $categories     The categories and their limits.
 * @param array $current_limits The current withdrawal limits.
 * @param float $zig_mid        The ZiG exchange rate.
 * @return string               The HTML table rows.
 */
function generate_table_rows(array $categories, array $current_limits, float $zig_mid)
{
    $table_rows = '';

    foreach ($categories as $category => $limits) {
        $table_rows .= '<tr>';
        $table_rows .= '<td>' . esc_html($category) . '</td>';
        $table_rows .= '<td>' . zp_format_prices($current_limits[$limits["weekly"]], 'zig') . '</td>';
        $table_rows .= '<td>' . zp_format_prices($current_limits[$limits["weekly"]] / $zig_mid, 'usd') . '</td>';
        $table_rows .= '<td>' . zp_format_prices($current_limits[$limits["monthly"]], 'zig') . '</td>';
        $table_rows .= '<td>' . zp_format_prices($current_limits[$limits["monthly"]] / $zig_mid, 'usd') . '</td>';
        $table_rows .= '</tr>';
    }

    return $table_rows;
}

/**
 * Builds the ZiG withdrawal limits table.
 *
 * @param array $data The data containing exchange rates.
 */
function build_zig_withdrawal_limits_table(array $data)
{
    // Get the current limits
    $current_limits = get_current_limits();

    // ZiG exchange rate
    $zig_mid = $data['rates']['ZiG_Mid'];

    // Get the current date in Africa/Harare timezone
    $current_date = current_time('l, j F Y', true); // Format: Day, Day Number Month Year

    // Generating HTML for the table
    $html = '<h4>' . sprintf(
        esc_html__('RBZ ZiG withdrawal limits and their USD equivalent on %s', 'text-domain'),
        $current_date
    ) . '</h4>';
    $html .= '<figure class="wp-block-table">';
    $html .= '<table class="has-fixed-layout">';
    $html .= '<tbody>';
    $html .= '<tr><th>' . esc_html__('Account Category', 'text-domain') . '</th><th>' . esc_html__('Weekly Limit', 'text-domain') . '</th><th>' . esc_html__('ZiG Weekly Limit in USD', 'text-domain') . '</th><th>' . esc_html__('Monthly Limit', 'text-domain') . '</th><th>' . esc_html__('ZiG Monthly Limit in USD', 'text-domain') . '</th></tr>';

    // Loop through each category and add its limits to the table
    $categories = [
        esc_html__('Individuals', 'text-domain') => ["weekly" => "individual_weekly", "monthly" => "individual_monthly"],
        esc_html__('Corporates', 'text-domain') => ["weekly" => "corporate_weekly", "monthly" => "corporate_monthly"],
        esc_html__('Schools, Hospitals, Clinics, Local Authorities', 'text-domain') => ["weekly" => "school_weekly", "monthly" => "school_monthly"],
        esc_html__('Government Ministries and Departments', 'text-domain') => ["weekly" => "government_weekly", "monthly" => "government_monthly"],
    ];

    // Generate table rows
    $html .= generate_table_rows($categories, $current_limits, $zig_mid);

    $html .= '</tbody>';
    $html .= '</table>';
    $html .= '<figcaption class="wp-element-caption">' . esc_html__('The latest ZiG Withdrawal limits', 'text-domain') . '</figcaption>';
    $html .= '</figure>';

    // Output the generated HTML
    return $html;
}
