<?php
// File: includes/shortcodes/groceries_new_shortcode.php

// Ensure direct access is not allowed
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Registers the [groceries_new] shortcode.
 */
function register_groceries_new_shortcode() {
    add_shortcode('groceries_new', 'zp_grocery_prices_new');
}
add_action('init', 'register_groceries_new_shortcode');

/**
 * Shortcode handler for [groceries_new].
 *
 * @param array $attr Shortcode attributes.
 * @return string HTML output for the shortcode.
 */
function zp_grocery_prices_new($attr) {
    try {
        // Set default type
        $type = 'detailed';

        // Override type if provided
        if (is_array($attr) && array_key_exists('type', $attr)) {
            $type = sanitize_text_field($attr['type']);
        }

        // Initialize the API handler
        $groceries = new ZIMAPI(ZIMAPI_BASE);


        if ($type === 'detailed') {
            // Define API endpoints
            $endpoints = [
                'prices' => [
                    'endpoint' => '/prices/groceries-new', // Update as per your backend
                ],
            ];

            // Make the API call
            $data = $groceries->multiCallApi($endpoints, zp_get_remote_ip());
        
            //  error_log('API Response: ' . print_r($data, true));

            // Navigate through the nested arrays based on the data structure
            if (
                isset($data['prices']) &&
                isset($data['prices']['data']) &&
                isset($data['prices']['data']['prices']) &&
                is_array($data['prices']['data']['prices'])
            ) {
                // Assuming 'prices' is an array of price datasets, select the latest one
                $latest_price = $data['prices']['data']['prices'];

                if ($latest_price) {
                    return build_groceries_new_table($latest_price);
                } else {
                    // Handle case where 'prices' array is empty
                    require_once API_END_BASE . 'includes/class-show-notice.php';
                    return ZP_SHOW_NOTICE::showError("No price data available.");
                }
            } else {
                // Handle missing or unexpected data structure
                require_once API_END_BASE . 'includes/class-show-notice.php';
                return ZP_SHOW_NOTICE::showError("We couldn't retrieve the latest Grocery prices at the moment. Please try again later.");
            }
        }
    } catch (Exception $e) {
        // Log the error for debugging
        error_log('Error retrieving Grocery prices (groceries_new shortcode): ' . $e->getMessage());

        // Display an error notice to the user
        require_once API_END_BASE . 'includes/class-show-notice.php';
        return ZP_SHOW_NOTICE::showError("We couldn't retrieve the latest Grocery prices at the moment. Please try again later.");
    }
}

/**
 * Builds the HTML table for the [groceries_new] shortcode.
 *
 * @param array $data The grocery prices data.
 * @return string The HTML for the grocery prices table.
 */
function build_groceries_new_table(array $data) {
 
    // Extract 'updatedAt' and 'Date' from the data
    $updated_at_raw = isset($data['updatedAt']) ? $data['updatedAt'] : '';
    $survey_date_raw = isset($data['Date']) ? $data['Date'] : '';

    // Format dates
    $updated_at = $updated_at_raw ? date_i18n('l, d F Y', strtotime($updated_at_raw)) : 'N/A';
    $survey_date = $survey_date_raw ? date_i18n('l, d F Y', strtotime($survey_date_raw)) : 'N/A';
    $today_date = date_i18n('l, d F Y', current_time('timestamp'));

    // Extract products
    $products = isset($data['products']) ? $data['products'] : [];

    // Collect unique shops and their primary currencies
    $shops_info = [];
    foreach ($products as $product) {
        if (isset($product['shops']) && is_array($product['shops'])) {
            foreach ($product['shops'] as $shop) {
                $shop_name = isset($shop['shop_name']) ? $shop['shop_name'] : '';

                // Determine primary currency
                if (isset($shop['primary_currency']) && in_array($shop['primary_currency'], ['USD', 'ZiG'])) {
                    $primary_currency = $shop['primary_currency'];
                } else {
                    // Derive primary currency based on available price
                    if (isset($shop['price_usd']) && !is_null($shop['price_usd'])) {
                        $primary_currency = 'USD';
                    } elseif (isset($shop['price_zig']) && !is_null($shop['price_zig'])) {
                        $primary_currency = 'ZiG';
                    } else {
                        $primary_currency = 'Unknown';
                    }
                }

                // Add to shops_info if not already added
                if ($shop_name && $primary_currency && !isset($shops_info[$shop_name])) {
                    $shops_info[$shop_name] = $primary_currency;
                }
            }
        }
    }

    // Start building the table HTML
    $table_html = '<h4>Latest Grocery Prices on ' . esc_html($today_date) . '</h4>
        <figure class="wp-block-table">
            <table>
                <thead>
                    <tr>
                        <th>Product</th>';

    // Add shop columns with primary currency in headers
    foreach ($shops_info as $shop_name => $currency) {
        $currency_label = ($currency === 'USD') ? 'USD' : 'ZiG';
        $table_html .= '<th>' . esc_html($shop_name) . ' (' . esc_html($currency_label) . ')</th>';
    }

    $table_html .= '</tr>
                </thead>
                <tbody>';

    // Populate table rows
    $table_html .= show_groceries_new_table($products, $shops_info);

    $table_html .= '</tbody>
            </table>
            <figcaption>The latest Supermarket Prices</figcaption>
        </figure>
        <p><strong>Last Updated on ' . esc_html($updated_at) . '</strong></p>
        ' . zp_show_footer();

    return $table_html;
}

/**
 * Generates the table rows for the grocery prices.
 *
 * @param array $products The list of products.
 * @param array $shops_info The shops information with primary currencies.
 * @return string The HTML for the table rows.
 */
function show_groceries_new_table(array $products, array $shops_info) {
    $product_table = '';

    foreach ($products as $product) {
        $item = isset($product['item']) ? $product['item'] : 'Unknown Item';
        $unit = isset($product['unit']) ? $product['unit'] : 'N/A';

        $product_table .= '<tr>
            <td>' . esc_html($item) . ' (' . esc_html($unit) . ')</td>';

        foreach ($shops_info as $shop_name => $currency) {
            // Find the shop's price data for the current product
            $shop_price_data = null;
            if (isset($product['shops']) && is_array($product['shops'])) {
                foreach ($product['shops'] as $shop) {
                    if (isset($shop['shop_name']) && $shop['shop_name'] === $shop_name) {
                        $shop_price_data = $shop;
                        break;
                    }
                }
            }

            // Determine the price based on primary currency
            if ($shop_price_data) {
                if ($currency === 'USD' && isset($shop_price_data['price_usd']) && !is_null($shop_price_data['price_usd'])) {
                    $price = $shop_price_data['price_usd'];
                    $formatted_price = zp_format_price_new($price, 'USD');
                } elseif ($currency === 'ZiG' && isset($shop_price_data['price_zig']) && !is_null($shop_price_data['price_zig'])) {
                    $price = $shop_price_data['price_zig'];
                    $formatted_price = zp_format_price_new($price, 'ZiG');
                } else {
                    $formatted_price = 'N/A';
                }
            } else {
                $formatted_price = 'N/A';
            }

            $product_table .= '<td>' . esc_html($formatted_price) . '</td>';
        }

        $product_table .= '</tr>';
    }

    return $product_table;
}

/**
 * Formats the price according to the specified requirements.
 *
 * @param float|null $price The price to format.
 * @param string $currency The currency type ('USD' or 'ZiG').
 * @return string The formatted price.
 */
function zp_format_price_new($price, $currency) {
    if (is_null($price)) {
        return 'N/A';
    }

    // Ensure price is a float
    $price = floatval($price);

    // Format number with two decimal places and space as thousand separator
    $formatted_number = number_format($price, 2, '.', ' ');

    // Append/prepend currency symbols as required
    if ($currency === 'USD') {
        return 'US$' . $formatted_number;
    } elseif ($currency === 'ZiG') {
        return '$' . $formatted_number . ' ZWG';
    } else {
        // Fallback for unknown currencies
        return esc_html($formatted_number);
    }
}


