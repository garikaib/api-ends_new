<?php

require_once plugin_dir_path(__DIR__) . "includes/format-liquids.php";
require_once plugin_dir_path(__DIR__) . "includes/usd-equivalent.php";
require_once plugin_dir_path(__DIR__) . "includes/show-price-footer.php";
require_once plugin_dir_path(__DIR__) . "includes/format-prices.php";
require_once plugin_dir_path(__DIR__) . "includes/dates.php";
/**
 * Builds and displays a table of liquor prices for Delta products.
 *
 * @param array $data  The data containing the Delta product prices.
 * @param array $rates The exchange rates for converting prices to USD.
 *
 * @return string The HTML table of liquor prices for Delta products.
 */
const RETAIL_MARKUP=1.2;

function build_delta_a_table(array $data, array $rates)
{
    $product_table = '';
    $prices = $data['prices']['prices'];

    foreach ($prices as $product) {
        $product_table .= '<tr>
          <td>' . esc_html($product['item']) . '</td>
          <td>' . $product['quantity'] . "x" . zp_convert_to_volume_units($product['unit']) . '</td>
          <td>' . zp_format_prices(RETAIL_MARKUP * $product['usd_price']) . '</td>
          <td>' . zp_format_prices(RETAIL_MARKUP * $rates['rates']['ZiG_BMBuy']  * $product['usd_price']) . '</td>
      </tr>';
    }

    $output = '<h4>Latest Liquor Prices ' . esc_html(date_i18n('l, d F Y')) . '</h4>
  <figure class="wp-block-table">
      <table>
          <thead>
              <tr>
                  <th>Product</th>
                  <th>Quantity</th>
                  <th>Price in USD</th>
                  <th>Price in ZiG (Estimated)</th>
              </tr>
          </thead>
          <tbody>' . $product_table . '
          </tbody>
      </table>
      <figcaption>The latest Beer,Alcohol, Liquor Prices</figcaption>
  </figure>
  <p><strong>Last Updated on ' . esc_html($rates['rates']['updatedAt']) . '</strong></p>
  ' . zp_show_footer();

    return $output;
}

/**
 * Formats the table of liquor prices for Delta products.
 *
 * @param array $data  The data containing the Delta product prices.
 * @param array $rates The exchange rates for converting prices to USD.
 *
 * @return string The formatted HTML table of liquor prices for Delta products.
 */
function show_delta_a_table(array $data, array $rates)
{
    return build_delta_a_table($data, $rates);
}
