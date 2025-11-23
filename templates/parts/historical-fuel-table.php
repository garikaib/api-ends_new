<?php
/**
 * Template part for displaying historical fuel prices.
 *
 * @var array $fuel_data The historical fuel data.
 * @var string $from_date The start date.
 * @var string $to_date The end date.
 */

require_once API_END_BASE . "includes/format-prices.php";

// Extract the prices array from the API data.
$prices = isset($fuel_data['prices']) ? $fuel_data['prices'] : array();

// Sort the prices from latest to oldest using ISO date strings.
usort($prices, function ($a, $b) {
    return strcmp($b['Date'], $a['Date']);
});

// Convert the from and to dates into human-friendly formats (month and year only).
$human_from = date('F Y', strtotime($from_date));
$human_to   = date('F Y', strtotime($to_date));
?>

<div class="historical-fuel-prices-table">
    <h4>Historical Fuel Prices in Zimbabwe Past Six Months (<?php echo esc_html($human_from); ?> to <?php echo esc_html($human_to); ?>)</h4>
    <figure class="wp-block-table">
        <table>
            <thead>
                <tr>
                    <th>Month</th>
                    <th>Blend Petrol (USD)</th>
                    <th>Diesel (USD)</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($prices as $row) :
                    // Convert the ISO date string to a timestamp.
                    $timestamp = strtotime($row['Date']);
                    // Format the date as "Month Year" (e.g., "May 2024").
                    $month_year = date('F Y', $timestamp);

                    // Format the USD prices.
                    $petrol_usd = isset($row['Petrol_USD']) ? zp_format_prices($row['Petrol_USD'], 'usd') : '';
                    $diesel_usd = isset($row['Diesel_USD']) ? zp_format_prices($row['Diesel_USD'], 'usd') : '';
                ?>
                    <tr>
                        <td><?php echo esc_html($month_year); ?></td>
                        <td><?php echo esc_html($petrol_usd); ?></td>
                        <td><?php echo esc_html($diesel_usd); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </figure>
</div>
