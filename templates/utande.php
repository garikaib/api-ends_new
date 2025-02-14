<?php
require_once plugin_dir_path(__DIR__) . 'includes/format-prices.php';
require_once plugin_dir_path(__DIR__) . 'includes/show-price-footer.php';

function buildUtandePrices(array $data, string $connType, array $rates = [])
{
    $package_description = [
        "LTE" => "LTE",
        "Fibre" => "Fibre",
        "WIFI" => 'WIFI',
        "VSAT" => 'VSAT',
    ];
    $header = "<h4>Utande " . ($package_description[$connType] ?? "Internet") . " package prices on " . date('l, j F Y', strtotime('now', time())) . "</h4>";

    $prices = $data['prices']['package_prices'];
    $product_table = "";
    foreach ($prices as $product) {
        if ($product['last_mile'] === $connType) {
            $product_table .= '<tr><td>' . $product['package_name'] . '</td><td>' . utandeISCapped('data', $product) . '</td><td>'
            . utandeISCapped('night_data', $product) . '</td><td>' . zp_format_prices($product['usd_price'], "usd") . '</td><td>' . zp_format_prices($product['usd_price'] * $rates['rates']['ZiG_Mid'], "zig")
                . '</td></tr>';
        }
    }
    // error_log(print_r($data, true));
    return
        $header . "
    <figure class='wp-block-table'>
  <table>
    <thead>
      <tr>
        <th>Package Name</th>
        <th>Normal Data(GB)</th>
        <th>Bonus Time Data(GB)</th>
        <th>Price In \$USD</th>
        <th>Estimated Price In ZiG</th>
      </tr>
    </thead>
    <tbody>" . $product_table . "
    </tbody>
  </table>
  <figcaption>Latest Utande " . $connType . " Prices</figcaption>
</figure> <p><strong>Last Updated on " . $data['prices']['updatedAt'] . "</strong></p>";
}

function utandeFormatPrices($usd_amount)
{
    return '$' . number_format($usd_amount, 2, '.', ' ');
}

function utandeISCapped(string $required, array $product)
{
    if (!$product['capped']) {
        return "Uncapped";
    } else {
        return $product[$required];
    }
}
