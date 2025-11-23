<?php
/**
 * Template part for displaying USD to ZiG conversion table.
 *
 * @var array $rates The rates data.
 */

$date = new DateTime('now', new DateTimeZone('Africa/Harare'));
$formatted_date = $date->format('l, j F Y');

$zig_notes = [
    1 => 'US$1 Note',
    2 => 'US$2 Note',
    5 => 'US$5 Note',
    10 => 'US$10 Note',
    20 => 'US$20 Note',
    50 => 'US$50 Note',
    100 => 'US$100 Note',
];

$table_rows = '';
foreach ($zig_notes as $value => $note) {
    // Calculate equivalent value in USD (Official)
    $official_value = number_format($value * $rates['rates']['ZiG_Mid'], 2, '.', ' ');
    
    // Calculate street value (Selling USD - what you get)
    $street_sell_value = number_format($value * $rates['rates']['ZiG_BMSell'], 2, '.', ' ');

    // Calculate street cost (Buying USD - what you pay)
    $street_buy_cost = number_format($value * $rates['rates']['ZiG_BMBuy'], 2, '.', ' ');

    // Add row to the table
    $table_rows .= '
        <tr>
            <td>' . wp_kses_post($note) . '</td>
            <td>' . wp_kses_post($official_value) . ' ZiG</td>
            <td>' . wp_kses_post($street_sell_value) . ' ZiG</td>
            <td>' . wp_kses_post($street_buy_cost) . ' ZiG</td>
        </tr>
    ';
}
?>

<div class="fuel-prices-table">
    <h4>Convert US dollar notes to Zimbabwe Gold (ZiG) on <?php echo wp_kses_post($formatted_date); ?></h4>
    <figure class="wp-block-table">
        <table>
            <thead>
                <tr>
                    <th scope="col">USD Note <i class="fas fa-money-bill-1-wave"></i></th>
                    <th scope="col">Official Value <i class="fas fa-building-columns"></i></th>
                    <th scope="col">Street Value (Sell USD) <i class="fas fa-hand-holding-dollar"></i></th>
                    <th scope="col">Street Cost (Buy USD) <i class="fas fa-money-bill-transfer"></i></th>
                </tr>
            </thead>
            <tbody>
                <?php echo $table_rows; ?>
            </tbody>
        </table>
    </figure>
    
    <h3 class="wp-block-heading">NB</h3>
    <ul>
        <li><strong>Official Value:</strong> This is the formal banking rate.</li>
        <li><strong>Street Value (Sell USD):</strong> This is the amount of ZiG you get when you sell your USD note on the parallel market.</li>
        <li><strong>Street Cost (Buy USD):</strong> This is the amount of ZiG you need to pay to purchase this USD note on the parallel market.</li>
    </ul>

    <p><strong><em>Last updated <?php echo wp_kses_post($rates['rates']['updatedAt']); ?></em></strong></p>
</div>
