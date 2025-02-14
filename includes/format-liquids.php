<?php
/**
 * Converts a volume measurement in milliliters to either liters or milliliters.
 *
 * @param int $volume The volume measurement in milliliters.
 * @return string The converted volume measurement in liters or milliliters.
 */
const MILLILITERS_PER_LITER = 1000;

function zp_convert_to_volume_units(int $volume): string
{

    if ($volume >= MILLILITERS_PER_LITER) {
        $result = $volume / MILLILITERS_PER_LITER;
        return $result . 'L';
    } else {
        return $volume . 'ml';
    }
}
