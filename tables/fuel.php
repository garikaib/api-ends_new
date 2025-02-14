<?php
/**
 * Retrieve historical fuel prices and build an HTML table.
 *
 * This function calculates the default date range (six months ago to today),
 * calls the API using multiCallApi(), and then builds and returns an HTML table
 * displaying the historical diesel and petrol prices.
 *
 * @return string HTML table for historical fuel prices or an error message.
 */
function zim_historical_fuel_prices_table() {
    try {
        // Initialize the API client.
        $fuel_api = new ZIMAPI( ZIMAPI_BASE );
        $remote_ip = zp_get_remote_ip();

        // Calculate the default date range.
        $from_date = date( 'Y-m-d', strtotime( '-6 months' ) );
        $to_date   = date( 'Y-m-d' );

        // Prepare the endpoint payload.
        $endpoints = array(
            'fuel' => array(
                'endpoint' => '/fuel/',
                'method'   => 'GET',
                'payload'  => array(
                    'from' => $from_date,
                    'to'   => $to_date,
                ),
            ),
        );

        // Call the API using multiCallApi.
        $results = $fuel_api->multiCallApi( $endpoints, $remote_ip );
        $fuel_data_response = isset( $results['fuel'] ) ? $results['fuel'] : null;

        if ( $fuel_data_response && isset( $fuel_data_response['success'] ) && $fuel_data_response['success'] === true ) {
            $fuel_data = $fuel_data_response['data'];

            // Retrieve FX rates for conversion fallback (not used in this table but included for API structure).
            $rates_api   = new ZIMAPI( ZIMAPI_BASE );
            $fx_endpoint = '/rates/fx-rates';
            $rates_response = $rates_api->callApi( $fx_endpoint, $remote_ip );
            $rates = isset( $rates_response['rates'] ) ? $rates_response : array();

            return build_historical_fuel_table( $fuel_data, $rates, $from_date, $to_date );
        } else {
            throw new Exception( 'API call unsuccessful.' );
        }
    } catch ( Exception $e ) {
        return '<p><strong>Unable to retrieve historical fuel prices at this time.</strong></p>';
    }
}
add_shortcode( 'historical-fuel-prices-table', 'zim_historical_fuel_prices_table' );


/**
 * Builds an HTML table displaying historical diesel and petrol prices.
 *
 * Expects the API response $data to include a 'prices' key containing an array
 * of historical records. Each record should have:
 *   - Date
 *   - Petrol_USD
 *   - Diesel_USD
 *
 * @param array  $data      The historical fuel data.
 * @param array  $rates     The exchange rate data.
 * @param string $from_date The starting date (YYYY-MM-DD) of the historical range.
 * @param string $to_date   The ending date (YYYY-MM-DD) of the historical range.
 * @return string           HTML table string.
 */
function build_historical_fuel_table( array $data, array $rates, $from_date, $to_date ) {
    // Extract the prices array from the API data.
    $prices = isset( $data['prices'] ) ? $data['prices'] : array();

    // Sort the prices from latest to oldest using ISO date strings.
    usort( $prices, function( $a, $b ) {
        return strcmp( $b['Date'], $a['Date'] );
    });

    // Convert the from and to dates into human-friendly formats (month and year only).
    $human_from = date( 'F Y', strtotime( $from_date ) );
    $human_to   = date( 'F Y', strtotime( $to_date ) );

    // Build the table header. "Past Six Months" is now primary and the dates are in brackets.
    $output  = '<div class="historical-fuel-prices-table">';
    $output .= '<h4>Historical Fuel Prices in Zimbabwe Past Six Months (' . esc_html( $human_from ) . ' to ' . esc_html( $human_to ) . ')</h4>';
    $output .= '<figure class="wp-block-table"><table>';
    $output .= '<thead>
                    <tr>
                        <th>Month</th>
                        <th>Blend Petrol (USD)</th>
                        <th>Diesel (USD)</th>
                    </tr>
                </thead>
                <tbody>';

    // Loop through each historical record.
    foreach ( $prices as $row ) {
        // Convert the ISO date string to a timestamp.
        $timestamp = strtotime( $row['Date'] );
        // Format the date as "Month Year" (e.g., "May 2024").
        $month_year = date( 'F Y', $timestamp );

        // Format the USD prices.
        $petrol_usd = isset( $row['Petrol_USD'] ) ? zp_format_prices( $row['Petrol_USD'], 'usd' ) : '';
        $diesel_usd = isset( $row['Diesel_USD'] ) ? zp_format_prices( $row['Diesel_USD'], 'usd' ) : '';

        $output .= '<tr>
                        <td>' . esc_html( $month_year ) . '</td>
                        <td>' . esc_html( $petrol_usd ) . '</td>
                        <td>' . esc_html( $diesel_usd ) . '</td>
                    </tr>';
    }

    $output .= '</tbody></table></figure>';
    $output .= '</div>';

    return $output;
}
