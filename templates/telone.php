<?php

require_once plugin_dir_path(__DIR__) . 'includes/usd-equivalent.php';
require_once plugin_dir_path(__DIR__) . 'includes/format-prices.php';
require_once plugin_dir_path(__DIR__) . 'includes/show-price-footer.php';

function buildTelOnePrices(array $data, string $connType, array $rates)
{

    $package_description = [
        "LTE" => "Blaze LTE",
        "ADSL" => "ADSL",
        "Fibre" => "Fibre",
        "WIFI" => 'WIFI',
        "VSAT" => 'VSAT',
        "USD" => "USD Bonus Bundle",

    ];
    $prices = $data['prices']['package_prices'];
    $product_table = "";
    foreach ($prices as $product) {
        if ($product['last_mile'] === $connType) {
            $product_table .= '<tr><td>' . $product['package_name'] . '</td><td>' . teloneISCapped('data', $product) . '</td><td>'
            . teloneISCapped('night_data', $product) . '</td><td>' . (($product['usd_price'] > 0) ? zp_format_prices($product['usd_price'], "usd") : zp_format_prices($product['zig_price'] / $rates['rates']['ZiG_Mid'], "usd")) . '</td><td>' . (($product['zig_price'] ?? 0) > 0 ? zp_format_prices($product['zig_price'], "zig") : zp_format_prices($product['usd_price'] * $rates['rates']['ZiG_Mid'], "zig"))
                . '</td></tr>';
        }
    }

    $header = "<h4>TelOne " . ($package_description[$connType] ?? "Internet") . " package prices on " . date('l, j F Y', strtotime('now', time())) . "</h4>";

    return
    $header .
    "<figure class='wp-block-table'>
  <table>
    <thead>
      <tr>
        <th>Package Name</th>
        <th>Normal Data(GB)</th>
        <th>Night Data(GB)</th>
        <th>" . (($connType !== "USD" && $connType !== "VSAT") ? 'Estimated Price in $USD' : 'Price In USD (Official Price)') . "</th>
        <th>" . (($connType === "USD" || $connType === "VSAT") ? "Estimated Cost in ZiG" : "Price in ZiG") . "</th>
            </tr>
    </thead>
    <tbody>" . $product_table . "
    </tbody>
  </table>
  <figcaption>Latest TelOne " . $connType . " Prices</figcaption>
</figure> <p><strong>Last Updated on " . $rates['rates']['updatedAt'] . "</strong></p>" . zp_show_footer();
}

function teloneISCapped(string $required, array $product)
{
    if (!$product['capped']) {
        return "Uncapped";
    } else {
        return $product[$required];
    }
}
