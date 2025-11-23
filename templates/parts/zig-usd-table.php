<?php
/**
 * Template part for displaying ZiG to USD conversion table.
 *
 * @var array $rates The rates data.
 * @var array $oe_array The official exchange rates array.
 */

$date = new DateTime('now', new DateTimeZone('Africa/Harare'));
$formatted_date = $date->format('l, j F Y');

$zig_notes = [
    1 => '1 ZiG',
    2 => '2 ZiG',
    5 => '5 ZiG',
    10 => '10 ZiG',
    20 => '20 ZiG',
    50 => '50 ZiG',
    100 => '100 ZiG',
    200 => '200 ZiG',
];

$table_rows = '';
foreach ($zig_notes as $value => $note) {
    // Calculate equivalent value in USD
    $usd_equivalent = number_format($value / $rates['rates']['ZiG_Mid'], 2, '.', ' ');

    // Calculate equivalent value in Rand (ZAR)
    $zar_equivalent = number_format(($usd_equivalent) * $oe_array['ZAR'], 2, '.', ' ');

    // Calculate equivalent value in GBP
    $gbp_equivalent = number_format(($usd_equivalent) * $oe_array['GBP'], 2, '.', ' ');

    // Add row to the table
    $table_rows .= '
        <tr>
            <td>' . wp_kses_post($note) . '</td>
            <td>US$' . wp_kses_post($usd_equivalent) . '</td>
            <td>R' . wp_kses_post($zar_equivalent) . '</td>
            <td>£' . wp_kses_post($gbp_equivalent) . '</td>
        </tr>
    ';
}
?>

<div class="fuel-prices-table">
    <h4>Convert Zimbabwe Gold (ZiG) notes to USD on <?php echo wp_kses_post($formatted_date); ?></h4>
    <figure class="wp-block-table">
        <table>
            <thead>
                <tr>
                    <th scope="col">ZiG Note <img src="https://zimpricecheck.com/wp-content/uploads/2024/04/tobias-Flag-of-Zimbabwe-scaled.webp" alt="Zimbabwe Flag" class="flag-icon"></th>
                    <th scope="col">Equivalent Value in USD <img src="https://zimpricecheck.com/wp-content/uploads/2024/04/jp-draws-US-Flag.webp" alt="USA Flag" class="flag-icon"></th>
                    <th scope="col">Equivalent Value in Rand (ZAR) <img src="https://zimpricecheck.com/wp-content/uploads/2024/04/Anonymous-Flag-of-South-Africa.webp" alt="South Africa Flag" class="flag-icon"></th>
                    <th scope="col">Equivalent Value in GBP <img src="https://zimpricecheck.com/wp-content/uploads/2024/04/Anonymous-Flag-of-Britain.webp" alt="UK Flag" class="flag-icon"></th>
                </tr>
            </thead>
            <tbody>
                <?php echo $table_rows; ?>
            </tbody>
        </table>
    </figure>
    <p><strong><em>Last updated <?php echo wp_kses_post($rates['rates']['updatedAt']); ?></em></strong></p>
</div>
