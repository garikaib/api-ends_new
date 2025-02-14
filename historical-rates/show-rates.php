<?php

// Register the shortcode
add_shortcode('show-historical-rate', 'show_historical_rate_shortcode');

function show_historical_rate_shortcode($atts)
{
    // Extract date from shortcode attributes
    $atts = shortcode_atts(['date' => null], $atts);
    $date = $atts['date'];

    // Instance of ZIMAPI
    $api = new ZIMAPI(ZIMAPI_BASE);

    if ($date) {
        try {
            // Call the API with the date parameter
            $url = '/rates/fx-rates?day=' . $date;
            $result = $api->callApi($url);

            // Retrieve cutoff dates from theme options
            $zigCutoffDate = carbon_get_theme_option('zig_cutoff_date');
            $auctionCutoffDate = carbon_get_theme_option('auction_cutoff_date');
            $omirBanDate = carbon_get_theme_option('omir_ban_date');

            //Enqueue the stylesheet
            wp_enqueue_style(
                'zpc_historical_rates_styles',
                API_END_URL . 'templates/css/historical_rates_nav_buttons.css'
            );

            // Display the historical rates table
            return display_historical_rates_table($result, $zigCutoffDate, $auctionCutoffDate, $omirBanDate);
        } catch (Exception $e) {
            // Log the error and return an error message
            error_log('Error fetching rates: ' . $e->getMessage());
            return "Error retrieving data. Please try again later.";
        }
    } else {
        return "No date provided in shortcode.";
    }
}

function display_historical_rates_table($rates, $zigCutoffDate, $auctionCutoffDate, $omirBanDate)
{
    // Define rate descriptions
    $rateDescriptions = [
        'BTC_USD' => 'Price for 1 USD Worth of Bitcoin',
        'Gold' => 'Moasi oa Tunya Gold Rate',
        'eGold' => 'Moasi oa Tunya Gold Token Rate',
        'ZiG_Mid' => '1 USD to ZiG Official Rate',
        'ZiG_ZWL' => '1 ZiG to ZWL',
        'ZiG_Cash' => '1 USD to ZiG when giving cash',
        'ZiG_BMBuy' => '1 USD to ZiG Highest Informal Sector Rate',
        'ZiG_BMSell' => '1 USD to ZiG Lowest Informal Sector Rate',
        'ZiG_Ask' => '1 USD to ZiG The Maximum Official Rate',
        'WBWS_Buy' => 'World Remit to USD Rate',
        'Skrill_USD' => 'Skrill to USD Rate',
        // Add other rate descriptions here as needed
    ];

    // Keys to exclude from the table
    $keysToExclude = ['WBWS_Buy', 'BTC_USD', 'Skrill_USD', 'ZiG_Ask', 'ZiG_Bid', 'Skrill', 'BTC'];

    // Currency formatting based on key
    $currencyFormats = [
        'BTC_USD' => 'US$',
        'Gold' => '$',
        'eGold' => '$',
        'ZiG_Mid' => '$',
        'ZiG_ZWL' => '$',
        'ZiG_Cash' => '$',
        'ZiG_BMBuy' => '$',
        'ZiG_BMSell' => '$',
        'ZiG_Ask' => '$',
    ];

    // Check if rates data is available
    if (isset($rates['rates']) && is_array($rates['rates'])) {
        // Determine currency code based on date
        $rateDate = new DateTime($rates['rates']['Date']);
        $currencyCode = ($rateDate >= new DateTime('2020-05-01')) ? 'ZWG' : 'ZWL';

        // Create and format the header with the date
        $formattedDate = $rateDate->format('l, j F Y');

        // Access 'updatedAt' correctly from within 'rates'
        $updatedAt = isset($rates['rates']['updatedAt']) ? esc_html($rates['rates']['updatedAt']) : 'N/A';

        // Create the table header
        $header = '<h3 class="wp-block-heading">' . esc_html__('Official and Black Market Exchange Rates On ' . $formattedDate, 'your-text-domain') . '</h3>';
        $output = '<figure class="wp-block-table alignfull is-style-regular">';
        $output .= '<table class="has-fixed-layout">';
        $output .= '<thead><tr><th>' . esc_html__('Rates Description', 'your-text-domain') . '</th><th>' . esc_html__('Rate', 'your-text-domain') . '</th></tr></thead>';
        $output .= '<tbody>';

        // Add rows for keys from the data, excluding those specified
        foreach ($rates['rates'] as $key => $value) {
            // Skip excluded rates based on cutoff dates and other conditions
            if (
                ($key === 'Ecocash' || $key === 'Swipe' || $key === 'ZIPIT' || $key === 'OneMoney' || $key === 'Auction' || $key === 'Cash' || $key === 'WBWS_Sell' || $key === 'WBWS_Mid' || $key === 'BMBuy' || $key === 'BMSell' && $rates['rates']['Date'] > $zigCutoffDate) ||
                ($key === 'bulkAuction' || $key === 'Auction' && $rates['rates']['Date'] > $auctionCutoffDate) ||
                ($key === 'OMIR' && $rates['rates']['Date'] > $omirBanDate) ||
                $key === 'Date' ||
                $key === 'createdAt' ||
                $key === 'updatedAt' ||
                in_array($key, $keysToExclude)
            ) {
                continue;
            }

            // Get the rate description
            $description = isset($rateDescriptions[$key]) ? $rateDescriptions[$key] : $key;

            // Format the value based on the key and locale
            $formattedValue = $value;
            if (isset($currencyFormats[$key])) {
                $currencySymbol = $currencyFormats[$key];
                $formattedValue = number_format_i18n($value, 2);
                $formattedValue = $currencySymbol . $formattedValue;

                // Add currency code for specific keys
                if (in_array($key, ['Gold', 'eGold', 'ZiG_Mid', 'ZiG_ZWL', 'ZiG_Cash', 'ZiG_BMBuy', 'ZiG_BMSell', 'ZiG_Ask'])) {
                    $formattedValue .= ' ' . $currencyCode;
                }
            }

            // Add the rate to the table row
            $output .= '<tr><td>' . esc_html($description) . '</td><td>' . esc_html($formattedValue) . '</td></tr>';
        }

        // Close the table body and the table
        $output .= '</tbody>';
        $output .= '</table>';

        // Add the table caption
        $output .= '<figcaption class="wp-element-caption">' . esc_html__('These rates were last updated on ' . $updatedAt, 'your-text-domain') . '</figcaption>';
        $output .= '</figure>';

        // **Add Rates Notes Section**
        $ratesNotesTitle = carbon_get_theme_option('rates_notes_title');
        $ratesNotes = carbon_get_theme_option('rates_notes');

        // If rates notes exist, add a heading and the notes content
        if ($ratesNotes) {
            $output .= '<h3 class="wp-block-heading">' . esc_html($ratesNotesTitle) . '</h3>';
            $output .= wpautop($ratesNotes); // Apply WordPress formatting to the notes
        }
        // Generate the navigation buttons
        $buttons = generate_zimpricecheck_historical_rates_navigation();
        return $header . $output . $buttons; // Return header, table, buttons and notes
    } else {
        return "No rates data available.";
    }
}

function generate_zimpricecheck_historical_rates_navigation()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'exchange_rate_tracking';

    $current_post_date = get_the_date('Y-m-d');
    $sanitized_date = $wpdb->_real_escape($current_post_date);

    $previous_post = $wpdb->get_row($wpdb->prepare(
        "SELECT post_id, iso_date FROM $table_name WHERE iso_date < %s ORDER BY iso_date DESC LIMIT 1",
        $sanitized_date
    ));
    $next_post = $wpdb->get_row($wpdb->prepare(
        "SELECT post_id, iso_date FROM $table_name WHERE iso_date > %s ORDER BY iso_date ASC LIMIT 1",
        $sanitized_date
    ));

    $output = '<div class="zimpricecheck-rates-navigation">';

    if ($previous_post) {
        $previous_post_date = new DateTime($previous_post->iso_date);
        $formatted_previous_date = $previous_post_date->format('j F Y');
        $output .= '<a href="' . esc_url(get_permalink($previous_post->post_id)) . '" class="zimpricecheck-material-button zimpricecheck-material-button--previous"><span class="dashicons dashicons-arrow-left-alt"></span> ' . esc_html__('Rates For ' . $formatted_previous_date, 'your-text-domain') . '</a>';
    }

    $output .= '<a href="/price-updates/official-and-black-market-exchange-rates/" class="zimpricecheck-material-button">' . esc_html__('See Rates Today', 'your-text-domain') . '</a>';

    if ($next_post) {
        $next_post_date = new DateTime($next_post->iso_date);
        $formatted_next_date = $next_post_date->format('j F Y');
        $output .= '<a href="' . esc_url(get_permalink($next_post->post_id)) . '" class="zimpricecheck-material-button zimpricecheck-material-button--next">' . esc_html__('Rates on ' . $formatted_next_date, 'your-text-domain') . ' <span class="dashicons dashicons-arrow-right-alt"></span></a>';
    }

    $output .= '</div>';

    return $output;
}
