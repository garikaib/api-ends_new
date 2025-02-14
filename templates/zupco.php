<?php

require_once plugin_dir_path(__DIR__) . "includes/usd-equivalent.php";
require_once plugin_dir_path(__DIR__) . "includes/show-price-footer.php";
require_once plugin_dir_path(__DIR__) . "includes/format-prices.php";
require_once plugin_dir_path(__DIR__) . "includes/dates.php";

function buildFareTable(array $data, array $rates)
{
    //Captions to use
    $captions = [
    ];
    // error_log(print_r($data, true));
    return "<h4> ZUPCO Fares Table on " . zp_today_full_date() . "</h4>
    <figure class='wp-block-table'>
  <table>
    <thead>
      <tr>
        <th>Distance</th>
        <th>ZUPCO Bus Fare ZiG</th>
        <th>ZUPCO Bus USD</th>
      </tr>
    </thead>
    <tbody>" . showFareTable($data, $rates) . "
    </tbody>
  </table>
  <figcaption>The Latest ZUPCO Kombi and Bus Fares</figcaption>
</figure> <p><strong>Last Updated on " . $rates['rates']['updatedAt'] . "</strong></p>
" . zp_show_footer();
}
function showFareTable(array $data, array $rates)
{
    $product_table = "";
    $prices = $data['prices']['fares'];

    foreach ($prices as $product) {
        $product_table .= '<tr><td>' . $product['route'] . '</td><td>' . zp_format_prices($product['bus_usd_price'] * $rates["rates"]["ZiG_Mid"], "zig") . '</td><td>'
        . zp_format_prices($product['bus_usd_price'], "usd") . '</td><td>';
    }

    return $product_table;
}
