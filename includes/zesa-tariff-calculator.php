<?php
/**
 * Calculate the cost of electricity for the given number of units using stepped pricing bands.
 *
 * @param array $prices The pricing data.
 * @param int $units The number of units for which to calculate the cost.
 * @return float The total cost of electricity for the given number of units.
 */
function calculate_electricity_cost($prices, $units)
{
    $bands = isset($prices['prices']['bands']) ? $prices['prices']['bands'] : (isset($prices['bands']) ? $prices['bands'] : (isset($prices[0]['description']) ? $prices : null));

    usort($bands, function ($a, $b) {
        return $a['min_units'] - $b['min_units'];
    });
    $total_cost = 0;
    foreach ($bands as $band) {
        $min_units = $band['min_units'];
        $max_units = $band['max_units'];
        $price = $band['zig_price_rea'];

        if ($units <= $max_units - $min_units) {
            $band_units = $units;
        } else {
            $band_units = $max_units - $min_units;
        }

        $band_cost = $band_units * $price;
        $total_cost += $band_cost;
        $units -= $band_units;

        if ($units <= 0) {
            break;
        }
    }
    return $total_cost;
}
