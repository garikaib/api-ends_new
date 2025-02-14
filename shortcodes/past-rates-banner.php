<?php

function zim_price_check_cta_shortcode()
{
    wp_enqueue_style('zim-price-check-cta', API_END_URL . 'templates/css/zpc_cta.css'); // enqueue the style.css file

    // Fetch the latest exchange rate post
    $latest_post = get_latest_exchange_rate_post();

    // Generate the View Past Rates link
    $view_past_rates_link = $latest_post ? get_permalink($latest_post) : '#';

    ob_start(); // Start output buffering
    ?>
    <div class="zpc-cta-container">
        <div class="zpc-cta-content">
            <h2 class="zpc-cta-title">Explore Zimbabwe Gold Exchange Rates</h2>
            <p class="zpc-cta-description">Dive deeper into ZiG exchange rates against USD, ZAR, NGN, and more. Access historical data and visualize trends with our interactive tools.</p>
            <div class="zpc-cta-buttons">
                <a href="<?php echo esc_url($view_past_rates_link); ?>" class="zpc-cta-button zpc-cta-button-past">View Past Rates</a>
                <a href="/track/official-and-unofficial-exchange-rate-charts/" class="zpc-cta-button zpc-cta-button-graphs">Historical Rate Graphs</a>
            </div>
        </div>
    </div>
    <?php
return ob_get_clean(); // Return and clean the output buffer
}
add_shortcode('zimpricecheck_cta', 'zim_price_check_cta_shortcode');

// Function to get the latest exchange rate post
function get_latest_exchange_rate_post()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'exchange_rate_tracking';

    // Get the latest post ID from the tracking table
    $latest_post_id = $wpdb->get_var("SELECT post_id FROM $table_name ORDER BY iso_date DESC LIMIT 1");

    // Return the post object if found
    return $latest_post_id ? get_post($latest_post_id) : null;
}