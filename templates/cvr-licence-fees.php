<?php

function zp_cvr_fees($attr)
{
    require_once plugin_dir_path(__DIR__) . "includes/format-prices.php";
    try {

        $fees = new ZIMAPI(ZIMAPI_BASE);
        $latest_fees = "";
        $endPoint = "/prices/cvr-fees";
        $ratesEndpoint = "/rates/fx-rates";
        $latest_fees = $fees->callApi($endPoint, zp_get_remote_ip());
        $latest_rates = $fees->callApi($ratesEndpoint, zp_get_remote_ip());
        $latest_prices = $fees->callApi($endPoint, zp_get_remote_ip());
        return zp_build_cvr_fees_table($latest_fees, $latest_rates);
    } catch (Exception $e) {
        // Log the error
        error_log('Error retrieving CVR fees: ' . $e->getMessage());
        // Return an error message to the user
        require_once plugin_dir_path(__DIR__) . 'includes/class-show-notice.php';

        return ZP_SHOW_NOTICE::showError("We couldn't retrieve the latest CVR Fees at the moment. Please try again later.");
    }
}
add_shortcode('cvr-fees', 'zp_cvr_fees');
function zp_build_cvr_fees_table(array $data = [], array $rates = []): string
{
    $output = '<h4>CVR Service and License Fees on ' . zp_today_full_date() . '</h4>';
    $output .= '<figure class="wp-block-table"><table>';
    $output .= '<thead><tr><th>Service</th><th>Fee in US$</th><th>ZiG Equivalent</th></tr></thead><tbody>';

    if (isset($data['success']) && $data['success'] && isset($data['prices']['fees'])) {
        foreach ($data['prices']['fees'] as $fee) {
            $output .= '<tr>';
            $output .= '<td>' . $fee['description'] . '</td>';
            $output .= '<td>' . ($fee['usd_fees'] == 0 ? 'N/A' : zp_format_prices($fee['usd_fees'], 'usd')) . '</td>';
            $output .= '<td>' . ($fee['usd_fees'] == 0 ? 'N/A' : zp_format_prices($fee['usd_fees'] * $rates['rates']['ZiG_Mid'], 'zig')) . '</td>';
            $output .= '</tr>';
        }
    }

    $output .= '</tbody></table></figure>';
    $output .= '<p><strong>Last Updated on ' . $data['prices']['updatedAt'] . '</strong></p>';

    return $output;
}
