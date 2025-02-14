<?php

require_once plugin_dir_path(__DIR__) . 'includes/usd-equivalent.php';
require_once plugin_dir_path(__DIR__) . 'includes/format-prices.php';
require_once plugin_dir_path(__DIR__) . 'includes/show-price-footer.php';
require_once plugin_dir_path(__DIR__) . "includes/dates.php";

function build_liquid_prices(array $data, string $conn_type, array $rates)
{
    $date = esc_html(zp_today_full_date());
    $package = genTableHeader($conn_type);
    $typ_descr = genTableHeader($conn_type);
    $prices = $data['prices']['package_prices'];
    $product_table = '';

    foreach ($prices as $product) {
        if ($product['last_mile'] === $conn_type) {
            $zig_price = isset($product['zig_price']) && $product['zig_price'] != 0 ? $product['zig_price'] : $rates['rates']['ZiG_Mid'] * $product['usd_price'];
            $usd_price = isset($product['usd_price']) && $product['usd_price'] != 0 ? $product['usd_price'] : $product['zig_price'] / $rates['rates']['ZiG_Mid'];
            $product_table .= '<tr><td>' . esc_html($product['package_name']) . '</td><td>' . liquid_is_limited('data', $product) . '</td><td>'
            . liquid_is_limited('night_data', $product) . '</td><td>' . zp_format_prices($zig_price, "zig")
            . '</td><td>' . zp_format_prices($usd_price, 'usd') . '</td></tr>';
        }
    }

    return "
    <h4> Liquid Home (ZOL) {$package} Internet Packages Prices on {$date}</h4>
  <figure class='wp-block-table'>
      <table>
          <thead>
              <tr>
                  <th>Package Name</th>
                  <th>Normal Data(GB)</th>
                  <th>Night Data(GB)</th>
                  " . ($conn_type === 'LTE_USD' || $conn_type === 'Fibre_USD' ? '<th>Estimated Price in ZiG</th>' : '<th>Price in ZiG</th>') . "
                  <th>Price in USD</th>
              </tr>
          </thead>
          <tbody>" . $product_table . "
          </tbody>
      </table>
      <figcaption>Latest Liquid Home " . esc_html(genTableHeader($conn_type)) . " Prices</figcaption>
  </figure> <p><strong>Last Updated on " . esc_html($rates['rates']['updatedAt']) . "</strong></p>" . zp_show_footer();
}

function liquid_is_limited(string $required, array $product)
{
    if (!$product['capped']) {
        return 'Uncapped';
    } else {
        return esc_html($product[$required]);
    }
}
function genTableHeader(string $conn_type): string
{
    $headers = [
        'Fibre' => 'FibroniX packages',
        'VSAT' => 'VSAT Packages',
        'LTE' => 'WibroniX',
        'LTE_USD' => 'WibroniX SpeeD USD',
        'Fibre' => 'FibroniX Pakages',
        'Fibre' => 'FibroniX',
        'Fibre_USD' => 'FibroniX SpeeD USD',
    ];

    return isset($headers[$conn_type]) ? $headers[$conn_type] : '';
}
