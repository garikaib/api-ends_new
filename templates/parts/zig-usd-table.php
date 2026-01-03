<?php
use ZPC\ApiEnds\Utils\DateUtil;
use ZPC\ApiEnds\Utils\PriceUtil;

$formatted_date = DateUtil::todayFull();

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
    $usd_equivalent = $value / ($rates['rates']['ZiG_Mid'] ?? 1); // Avoid div by zero if missing

    // Calculate equivalent value in Rand (ZAR)
    $zar_equivalent = $usd_equivalent * ($oe_array['ZAR'] ?? 0);

    // Calculate equivalent value in GBP
    $gbp_equivalent = $usd_equivalent * ($oe_array['GBP'] ?? 0);

    // Add row to the table
    $table_rows .= '
        <tr>
            <td>' . esc_html($note) . '</td>
            <td>' . PriceUtil::format($usd_equivalent, 'usd') . '</td>
            <td>R' . number_format($zar_equivalent, 2, '.', ' ') . '</td>
            <td>£' . number_format($gbp_equivalent, 2, '.', ' ') . '</td>
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
