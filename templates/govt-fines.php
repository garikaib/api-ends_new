<?php
/**
 * Builds a fines table.
 *
 * @param array $data The array containing the fine data.
 *
 * @return string The generated table HTML.
 */
require_once plugin_dir_path(__DIR__) . 'includes/format-prices.php';
require_once plugin_dir_path(__DIR__) . 'includes/dates.php';

function zp_build_fines_table(array $data)
{
    $output = '<h4>Fines in Zimbabwe on ' . esc_html(zp_today_full_date()) . '</h4>';
    $output .= ' <figure class="wp-block-table">
    <table><thead><tr><th>Fine Level</th>';
    $usd_heading = '<th>Amount (USD)</th></tr></thead>';
    $zwl_heading = '<th>Amount (ZWL$)</th></tr></thead>';
    $closing = '<tbody>';
    $output .= (key_exists('usd_amount', $data['prices']['fines'][0])) ? $usd_heading : $zwl_heading;

    foreach ($data['prices']['fines'] as $fine) {
        $output .= '<tr>';
        $output .= '<td>' . esc_html($fine['level']) . '</td>';
        $output .= '<td>' . zp_format_prices(isset($fine['zwl_amount']) ? $fine['zwl_amount'] : $fine['usd_amount']) . '</td>';
        $output .= '</tr>';
    }

    $output .= '</tbody>';
    $output .= '</table></figure><p><strong>Last Updated on ' . $data['prices']['updatedAt'] . '</strong></p>';

    return $output;
}
