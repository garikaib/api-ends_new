<?php

/**
 * Get the highest zig_price_rea value from the provided data.
 *
 * @param array $data The data array containing price bands.
 *
 * @return float The highest zig_price_rea value.
 */
function get_highest_zig_price_rea(array $data): float
{
    $highest_zig_price_rea = 0;

    if (isset($data['success']) && isset($data['prices']['bands'])) {
        foreach ($data['prices']['bands'] as $band) {
            if (isset($band['zig_price_rea']) && $band['zig_price_rea'] > $highest_zig_price_rea) {
                $highest_zig_price_rea = $band['zig_price_rea'];
            }
        }
    }
    return $highest_zig_price_rea;
}
