<?php
require_once plugin_dir_path(__FILE__) . '../includes/format-prices.php';

/**
 * Builds the ZBC license fee table.
 *
 * @param array $data  The license fee data from the API.
 * @param array $rates The exchange rate data from the API.
 *
 * @return string The HTML output of the table.
 */
function build_zbc_licence_fee_table(array $data, array $rates): string
{
    // Initialize output with the table heading and current date
    $output = '<h4>ZBC License Fees on ' . zp_today_full_date() . '</h4>';
    $output .= '<figure class="wp-block-table"><table>';
    $output .= '<thead><tr><th>Licence Type</th><th>USD Fees Per Quarter</th><th>USD Fees Per Year</th><th>ZiG Fees Per Quarter</th><th>ZiG Fees Per Year</th></tr></thead><tbody>';

    // Check if API call was successful and data exists
    if (isset($data['success']) && $data['success'] && isset($data['prices']['fees'])) {
        // Iterate through each fee structure
        foreach ($data['prices']['fees'] as $fee) {
            // Start building table row
            $output .= '<tr>';
            $output .= '<td>' . esc_html($fee['licence_type']) . '</td>';

            // Format USD fees, use N/A if fee is 0
            $usdFeeQuarterly = $fee['usd_fee'] == 0 ? 'N/A' : zp_format_prices($fee['usd_fee'], 'usd');
            $usdFeeYearly = $fee['usd_fees_year'] == 0 ? 'N/A' : zp_format_prices($fee['usd_fees_year'], 'usd');
            $output .= '<td>' . esc_html($usdFeeQuarterly) . '</td>';
            $output .= '<td>' . esc_html($usdFeeYearly) . '</td>';

            // Calculate and format ZiG fees, use N/A if USD fee is 0
            $zigFeeQuarterly = $fee['usd_fee'] == 0 ? 'N/A' : zp_format_prices($fee['usd_fee'] * $rates['rates']['ZiG_Mid'], 'zig');
            $zigFeeYearly = $fee['usd_fees_year'] == 0 ? 'N/A' : zp_format_prices($fee['usd_fees_year'] * $rates['rates']['ZiG_Mid'], 'zig');
            $output .= '<td>' . esc_html($zigFeeQuarterly) . '</td>';
            $output .= '<td>' . esc_html($zigFeeYearly) . '</td>';

            // End table row
            $output .= '</tr>';
        }
    }

    // Close table structure
    $output .= '</tbody></table></figure>';

    // Add last updated timestamp from the rates API
    $output .= '<p><strong>Last Updated on ' . esc_html($rates['rates']['updatedAt']) . '</strong></p>';

    return $output;
}

/**
 * Shortcode to display the latest ZBC fees.
 *
 * @return string The HTML output of the table or an error message.
 */
add_shortcode('zbc-licences-fees', 'show_latest_zbc_fees');
function show_latest_zbc_fees(): string
{
    // Only execute on singular pages where the shortcode is present
    if (is_singular() && has_shortcode(get_post()->post_content, 'zbc-licences-fees')) {
        try {
            // Initialize ZIMAPI instances for fees and rates
            $zbc_fees = new ZIMAPI(ZIMAPI_BASE);
            $zim_rates = new ZIMAPI(ZIMAPI_BASE);

            // Define API endpoints
            $endPointFees = "/prices/zbc-fees";
            $endPointRates = '/rates/fx-rates';

            // Fetch data from APIs
            $latest_zbc_fees = $zbc_fees->callApi($endPointFees, zp_get_remote_ip());
            $zim_latest_rates = $zim_rates->callApi($endPointRates, zp_get_remote_ip());

            // Build and return the table
            return build_zbc_licence_fee_table($latest_zbc_fees, $zim_latest_rates);
        } catch (Exception $e) {
            // Log the error for debugging
            error_log('Error retrieving ZBC Licence Fees: ' . $e->getMessage());

            // Display a user-friendly error message
            require_once API_END_BASE . 'includes/class-show-notice.php';
            return ZP_SHOW_NOTICE::showError("We couldn't retrieve the latest ZBC Licence Fees at the moment. Please try again later.");
        }
    }
    return ''; // Return an empty string if the shortcode is not present or not on a singular page
}
