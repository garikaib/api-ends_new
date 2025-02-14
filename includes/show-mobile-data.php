<?php
function showData(array $data, array $rates, string $req, string $filter = "none", $walker = false)
{
    $stack = array(
        "hybrid",
        "voice",
        "sms",
        "night_data",
        "twitter",
        "instagram",
    );
    $econ_usd = array(
        "usd_hybrid",
        "usd_voice",
        "usd_whatsapp",
        "usd_wifi",
    );

    if (in_array($req, $stack) || $walker === true) {
        return showHyBridBundle($data, $rates, $req, $filter);
    } elseif (in_array($req, $econ_usd)) {
        return show_econ_usd_bundle($data, $rates, $req, $filter);
    } else {
        return showNormalBundle($data, $rates, $req, $filter);
    }
}
function showNormalBundle(array $bundles, array $rates, string $bundleType, string $filter = "none")
{
    $product_table = "";
    $prices = $bundles['prices']['bundles'];

    foreach ($prices as $product) {
        if ($product['description'] === $bundleType) {
            $data_all = $product['data'];
            if ($bundleType === "whatsapp") {
                $data_all = $product['wa'];
            } elseif ($bundleType === "facebook") {
                $data_all = $product["fb"];
            }
            $product_table .= '<tr><td>' . $product['package_name'] . '</td><td>' . zp_format_prices($product['zwl_price']) . '</td><td>' . zp_format_prices(zp_to_usd($rates['rates']['Ecocash'] * 0.95, $product['zwl_price']), "usd") . '</td><td>' . zp_format_data($data_all) . '</td><td>' . to_relative_time($product['validity']) . '</td></tr>';
        }
    }
    return $product_table;
}
function showHyBridBundle(array $bundles, array $rates, string $bundleType, string $filter = "none")
{
    $product_table = "";
    $prices = $bundles['prices']['bundles'];

    foreach ($prices as $product) {
        //Is the filter in there
        $pos = strpos(strtolower($product["package_name"]), strtolower($filter));

        if ($product['description'] === $bundleType && ($pos !== false || $filter === "none")) {
            $product_table .= '<tr><td>' . $product['package_name'] . '</td><td>' . zp_format_prices($product['zwl_price']) . '</td><td>' . zp_format_prices(zp_to_usd($rates['rates']['Ecocash'] * 0.95, $product['zwl_price']), "usd") . '</td><td>' . zp_what_you_get($product) . '</td><td>' . to_relative_time($product['validity']) . '</td></tr>';
        }
    }
    return $product_table;
}
/**
 * Outputs a table of product bundles with prices in USD and ZWL equivalent.
 *
 * @param array $bundles An array of product bundles.
 * @param array $rates   An array of currency exchange rates.
 * @param string $bundle_type The type of bundle to display.
 * @param string $filter  Optional. A filter to apply to the product names.
 *                        Defaults to "none".
 * @return string HTML markup for the product table.
 */
function show_econ_usd_bundle(array $bundles, array $rates, string $bundle_type, string $filter = 'none'): string
{
    $product_table = '';

    $prices = $bundles['prices']['bundles'];

    foreach ($prices as $product) {
        $product_name = strtolower($product['package_name']);
        $product_desc = $product['description'];

        // Apply the filter
        if ($filter !== 'none' && strpos(strtolower($product_name), strtolower($filter)) === false) {
            continue;
        }

        // Check if the bundle type matches
        if ($product_desc !== $bundle_type) {
            continue;
        }

        $usd_price = $product['usd_price'];
        $ecocash_price = $rates['rates']['Ecocash'] * $usd_price;
        $what_you_get = zp_what_you_get($product);
        $validity = to_relative_time($product['validity']);

        $product_table .= '<tr>';
        $product_table .= '<td>' . $product['package_name'] . '</td>';
        $product_table .= '<td>' . zp_format_prices($usd_price, 'usd') . '</td>';
        $product_table .= '<td>' . zp_format_prices($ecocash_price) . '</td>';
        $product_table .= '<td>' . $what_you_get . '</td>';
        $product_table .= '<td>' . $validity . '</td>';
        $product_table .= '</tr>';
    }

    return $product_table;
}

/**
 * Returns a HTML unordered list of product benefits excluding certain keys.
 *
 * @param array $product The product to get the benefits for.
 * @return string The HTML unordered list of product benefits.
 */
function zp_what_you_get(array $product)
{
    $exclude = array(
        'validity',
        'zwl_price',
        'description',
        'package_name',
        'usd_price',
    );
    $benefit_list = '<ul>';
    foreach ($product as $key => $value) {
        if (!in_array($key, $exclude, true)) {
            $description = zp_get_description($key);
            if ($value > 0) {
                $benefit_list .= '<li>' . zp_format_hybrid_bundle($value, $key) . ' ' . esc_html($description) . '</li>';
            }
        }
    }

    return $benefit_list . '</ul>';
}
