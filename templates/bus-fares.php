<?php

require_once plugin_dir_path(__DIR__) . "includes/usd-equivalent.php";
require_once plugin_dir_path(__DIR__) . "includes/format-prices.php";
require_once plugin_dir_path(__DIR__) . "includes/dates.php"; /**
 * Builds the intercity bus fare table.
 *
 * @param array $data The data for the bus fares.
 * @param array $rates The rates for the bus fares.
 *
 * @return string The HTML for the intercity bus fare table.
 */
function build_b_fare_table(array $data, array $rates)
{
    //Captions to use
    $captions = [];
    // error_log(print_r($data, true));
    $output = "<h4>Zimbabwe Intercity Bus Fares on " . zp_today_full_date() . "</h4>
   <figure class='wp-block-table'>
       <table>
           <thead>
               <tr>
                   <th>From</th>
                   <th>To</th>
                   <th>Distance in KM</th>
                   <th>Bus Fare in US$</th>
               </tr>
           </thead>
           <tbody>" . show_b_fare_table($data, $rates) . "
           </tbody>
       </table>
       <figcaption>The Latest Intercity Bus Fares</figcaption>
   </figure>
   <p><strong>Last Updated on " . $rates['rates']['updatedAt'] . "</strong></p>
   ";

    return $output;
}

/**
 * Shows the intercity bus fare table.
 *
 * @param array $data The data for the bus fares.
 * @param array $rates The rates for the bus fares.
 *
 * @return string The HTML for the intercity bus fare table.
 */
function show_b_fare_table(array $data, array $rates)
{
    $product_table = "";
    $prices = $data['prices']['fares'];

    foreach ($prices as $product) {
        $product_table .= '<tr>
           <td>' . $product['from'] . '</td>
           <td>' . $product['to'] . '</td>
           <td>' . $product['distance'] . ' km</td>
           <td>' . zp_format_prices($product['usd_fare']) . '</td>
       </tr>';
    }

    return $product_table;
}
