<?php
/**
 * Convert an amount from Zimbabwean dollars to US dollars based on a given exchange rate.
 *
 * @param float $exchange_rate The exchange rate to use for the conversion.
 * @param float $zwl_amount The amount in Zimbabwean dollars to convert.
 * @return float The converted amount in US dollars.
 */
function zp_to_usd(float $exchange_rate, float $zwl_amount): float
{
    if ($exchange_rate <= 0.0) {
        return 0.0;
    }

    return round($zwl_amount / $exchange_rate, 2);
}
