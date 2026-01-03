<?php
/**
 * Template part for displaying latest fuel prices.
 *
 * @var array $fuel_data The fuel data.
 * @var array $rates_data The rates data.
 */

use ZPC\ApiEnds\Utils\DateUtil;
use ZPC\ApiEnds\Utils\PriceUtil;

$date = DateUtil::todayFull();

// Check if the keys exist and are not zero or undefined
$petrol_price_zig = isset($fuel_data['prices']['Petrol_ZiG']) && $fuel_data['prices']['Petrol_ZiG'] !== 0 ? $fuel_data['prices']['Petrol_ZiG'] : $fuel_data['prices']['Petrol_USD'] * $rates_data['rates']['ZiG_Mid'];
$diesel_price_zig = isset($fuel_data['prices']['Diesel_ZiG']) && $fuel_data['prices']['Diesel_ZiG'] !== 0 ? $fuel_data['prices']['Diesel_ZiG'] : $fuel_data['prices']['Diesel_USD'] * $rates_data['rates']['ZiG_Mid'];

$table_data = [
    [
        'type' => 'Blend Petrol (E10)',
        'usd' => PriceUtil::format($fuel_data['prices']['Petrol_USD'], 'usd'),
        'zig' => PriceUtil::format($petrol_price_zig, 'zig'),
    ],
    [
        'type' => 'Diesel 50',
        'usd' => PriceUtil::format($fuel_data['prices']['Diesel_USD'], 'usd'),
        'zig' => PriceUtil::format($diesel_price_zig, 'zig'),
    ],
];
?>

<div class="fuel-prices-table">
    <h4>Diesel and Petrol Prices in Zimbabwe on <?php echo wp_kses_post($date); ?></h4>
    <figure class="wp-block-table">
        <table>
            <thead>
                <tr>
                    <th scope="col">Fuel Type</th>
                    <th scope="col">Price (USD)</th>
                    <th scope="col">Price (ZiG)</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($table_data as $row) : ?>
                    <tr>
                        <td><?php echo wp_kses_post($row['type']); ?></td>
                        <td><?php echo wp_kses_post($row['usd']); ?></td>
                        <td><?php echo wp_kses_post($row['zig']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </figure>

    <?php if (!empty($fuel_data['prices']['retail_fuel_prices']) && is_array($fuel_data['prices']['retail_fuel_prices'])) : ?>
        <h5 class="wp-block-heading">Retail Fuel Prices (Service Stations)</h5>
        <figure class="wp-block-table">
            <table>
                <thead>
                    <tr>
                        <th scope="col">Service Station</th>
                        <th scope="col">Petrol Price (USD)</th>
                        <th scope="col">Diesel Price (USD)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($fuel_data['prices']['retail_fuel_prices'] as $station) :
                        $petrol_low = $station['petrol']['low'];
                        $petrol_high = $station['petrol']['high'];
                        $petrol_display = ($petrol_low == $petrol_high) ? PriceUtil::format($petrol_low, 'usd') : PriceUtil::format($petrol_low, 'usd') . ' - ' . PriceUtil::format($petrol_high, 'usd');

                        $diesel_low = $station['diesel']['low'];
                        $diesel_high = $station['diesel']['high'];
                        $diesel_display = ($diesel_low == $diesel_high) ? PriceUtil::format($diesel_low, 'usd') : PriceUtil::format($diesel_low, 'usd') . ' - ' . PriceUtil::format($diesel_high, 'usd');
                    ?>
                        <tr>
                            <td><?php echo esc_html($station['station']); ?></td>
                            <td><?php echo esc_html($petrol_display); ?></td>
                            <td><?php echo esc_html($diesel_display); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </figure>
    <?php endif; ?>

    <p><strong><em>Last updated <?php echo wp_kses_post($fuel_data['prices']['updatedAt']); ?></em></strong></p>
</div>
