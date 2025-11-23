<?php
/**
 * Template part for displaying latest LP Gas prices.
 *
 * @var array $fuel_data The fuel data (LP Gas).
 * @var array $rates_data The rates data.
 */

require_once API_END_BASE . "includes/format-prices.php";
require_once API_END_BASE . "includes/dates.php";

$date = zp_today_full_date();

// Define the table rows and their corresponding keys and formatting options
$rows = [
    [
        'label' => 'ZERA Maximum Price in USD',
        'price_key' => 'LPGas_USD',
        'price_format' => 'usd',
    ],
    [
        'label' => 'ZERA Maximum Price in ZiG',
        'price_key' => 'LPGas_USD',
        'price_format' => 'zig',
        'price_multiplier' => isset($fuel_data['prices']['LPGas_ZiG']) && $fuel_data['prices']['LPGas_ZiG'] != 0 ? $fuel_data['prices']['LPGas_ZiG'] : $rates_data['rates']['ZiG_Mid'],
    ],
    [
        'label' => 'LP Gas Black Market Price in USD',
        'price_key' => 'BM_USD',
        'price_format' => 'usd',
    ],
    [
        'label' => 'LP Gas Black Market Price in ZiG',
        'price_key' => 'LPGas_ZiG', // Use the 'LPGas_ZiG' value from the JSON data
        'price_format' => 'zig',
    ],
];
?>

<div class="lp-gas-prices-table">
    <h4>Liquid Petroleum (LP) Gas Prices on <?php echo esc_html($date); ?></h4>
    <figure class='wp-block-table'>
        <table>
            <thead>
                <tr>
                    <th scope="col">Item</th>
                    <th scope="col">Price Per KG</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($rows as $row) : ?>
                    <?php if (isset($fuel_data['prices'][$row['price_key']])) : ?>
                        <?php
                        $price = $fuel_data['prices'][$row['price_key']];
                        if (isset($row['price_multiplier']) && !isset($fuel_data['prices']['LPGas_ZiG'])) {
                            $price *= $row['price_multiplier'];
                        }
                        $formatted_price = zp_format_prices($price, $row['price_format']);
                        ?>
                        <tr>
                            <td scope="row"><?php echo esc_html($row['label']); ?></td>
                            <td><?php echo esc_html($formatted_price); ?></td>
                        </tr>
                    <?php endif; ?>
                <?php endforeach; ?>
            </tbody>
        </table>
    </figure>

    <?php if (!empty($fuel_data['prices']['retail_prices']) && is_array($fuel_data['prices']['retail_prices'])) : ?>
        <h5 class="wp-block-heading">Retail LP Gas Prices from Selected Sellers</h5>
        <figure class="wp-block-table">
            <table>
                <thead>
                    <tr>
                        <th scope="col">Shop Name</th>
                        <th scope="col">Price Per KG (USD)</th>
                        <th scope="col">Address/Location</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($fuel_data['prices']['retail_prices'] as $retail) : ?>
                        <tr>
                            <td scope="row"><?php echo esc_html($retail['shop']); ?></td>
                            <td><?php echo zp_format_prices($retail['price_usd'], 'usd'); ?></td>
                            <td><?php echo !empty($retail['address']) ? esc_html($retail['address']) : 'Multiple Sites'; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </figure>
        <p class="has-small-font-size"><em>Note: Retail prices are provided by independent sellers and may vary. Please confirm with the seller before purchase.</em></p>
    <?php endif; ?>

    <p>
        <strong><em>Last updated <?php echo esc_html($fuel_data['prices']['updatedAt']); ?></em></strong>
    </p>
</div>
