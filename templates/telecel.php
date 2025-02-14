<?php

require_once plugin_dir_path(__DIR__) . "includes/relative-time.php";
require_once plugin_dir_path(__DIR__) . "includes/format-data.php";
require_once plugin_dir_path(__DIR__) . "includes/usd-equivalent.php";
require_once plugin_dir_path(__DIR__) . "includes/show-price-footer.php";
require_once plugin_dir_path(__DIR__) . "includes/format-prices.php";
require_once plugin_dir_path(__DIR__) . "includes/show-mobile-data.php";

function buildTelecelPrices(array $data, array $rates, string $bundleType, string $filter)
{
    //Captions to use
    $captions = [
        'data' => 'Data Bundle',
        'elearning' => 'eLearning Bundle',
        'hybrid' => "Megaboost Bundles",
        'wifi' => 'Home WiFi Bundles',
        'voice' => 'Voice Bundle',
        'whatsapp' => 'WhatsApp Bundles',

    ];
    $walker = false;
    if ($bundleType === "data") {
        $walker = true;
    }
    // error_log(print_r($data, true));
    return "
    <figure class='wp-block-table'>
  <table>
    <thead>
      <tr>
        <th>Package Name</th>
        <th>Price In ZWL</th>
        <th>Price In \$USD</th>
        <th>What you get</th>
        <th>Validity</th>
      </tr>
    </thead>
    <tbody>" . showData($data, $rates, $bundleType, $filter, $walker) . "
    </tbody>
  </table>
  <figcaption>Latest " . showCaption($bundleType, $captions) . " Prices</figcaption>
</figure> <p><strong>Last Updated on " . $rates['rates']['updatedAt'] . "</strong></p>
" . zp_show_footer();
}
