<?php

require_once plugin_dir_path(__DIR__) . "includes/relative-time.php";
require_once plugin_dir_path(__DIR__) . "includes/format-data.php";
require_once plugin_dir_path(__DIR__) . "includes/usd-equivalent.php";
require_once plugin_dir_path(__DIR__) . "includes/show-price-footer.php";
require_once plugin_dir_path(__DIR__) . "includes/format-prices.php";
require_once plugin_dir_path(__DIR__) . "includes/show-mobile-data.php";

function buildEconetPrices(array $data, array $rates, string $bundleType, string $filter)
{
    //Captions to use
    $captions = [
        'wifi' => 'Private WiFi Bundles',
        'usd_voice' => 'USD Bundles of Joy',
        'usd_whatsapp' => 'Econet USD WhatsApp bundles',
        'usd_hybrid' => 'Econet Combo Bundles',
        'usd_voice' => 'Econet USD Bundles of Joy',
        'usd_whatsapp' => 'Econet USD WhatsApp bundles',
        'usd_wifi' => 'Econet USD Private WiFi bundles',
    ];
//USD bundle tags
    $econ_usd = array(
        "usd_hybrid",
        "usd_voice",
        "usd_whatsapp",
        "usd_wifi",
    );
// error_log(print_r($data, true));
    $header = "<figure class='wp-block-table'>
<table>";
    $is_usd = false;
    $is_usd = in_array($bundleType, $econ_usd);
    if ($is_usd) {
        $header .= "<thead>
    <tr>
      <th>Package Name</th>
      <th>Price In \$USD</th>
      <th>Price In ZWL
      (Equivalent)</th>
      <th>What you get</th>
      <th>Validity</th>
    </tr>
  </thead>
  <tbody>";
    } else {
        $header .= "<thead>
    <tr>
      <th>Package Name</th>
      <th>Price In ZWL</th>
      <th>Price In \$USD</th>
      <th>What you get</th>
      <th>Validity</th>
    </tr>
  </thead>
  <tbody>";
    }

    return $header . showData($data, $rates, $bundleType, $filter) . "
    </tbody>
  </table>
  <figcaption>Latest " . showCaption($bundleType, $captions) . " Prices</figcaption>
</figure> <p><strong>Last Updated on " . $rates['rates']['updatedAt'] . "</strong></p>
" . zp_show_footer($is_usd);
}
