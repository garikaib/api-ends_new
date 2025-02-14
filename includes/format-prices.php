<?php
/**
 * Format prices based on currency.
 *
 * @param float  $amount   The amount to be formatted.
 * @param string $currency The currency code. Default is "zwl".
 *
 * @return string The formatted price.
 */
function zp_format_prices(float $amount, string $currency = 'zwl')
{
    $final = '';
    switch ($currency) {
        case 'usd':
            $final = 'US$' . number_format($amount, 2, '.', ' ');
            break;
        case 'zig':
            $final = number_format($amount, 2, '.', ' ') . ' ZIG';
            break;
        default:
            $final = '$' . number_format($amount, 2, '.', ' ');
    }
    return $final;
}
function zp_getCellValue($array, $key)
{
    $value = isset($array[$key]) ? $array[$key] : '';
    if (is_numeric($value)) {
        if ($key === 'usd_price' || $key === 'usd_price_prem') {
            $formattedValue = 'US$' . number_format($value, 2);
        } elseif ($key === 'zig_price' || $key === 'zig_price_prem') {
            $formattedValue = number_format($value, 2, '.', ' ') . ' ZIG';
        } elseif ($key === 'bwp_price' || $key === 'bwp_price_prem') {
            $formattedValue = 'P' . number_format($value, 2);
        } elseif ($key === 'zar_price' || $key === 'zar_price_prem') {
            $formattedValue = 'R' . number_format($value, 2);
        } else {
            $formattedValue = $value;
        }
    } else {
        $formattedValue = $value;
    }
    return $formattedValue;
}
