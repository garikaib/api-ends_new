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

/**
 * Normalize a fine category string: unify unicode/ASCII apostrophes and
 * collapse repeated whitespace, so category names from the API compare
 * reliably regardless of quote style.
 *
 * @param string $cat Raw category string.
 *
 * @return string Normalized category string.
 */
if ( ! function_exists( 'zp_normalize_fine_category' ) ) {
	function zp_normalize_fine_category($cat)
	{
	    $cleaned = preg_replace('/[\x{2019}\x{2018}\']/u', "'", $cat);
	    $cleaned = preg_replace('/\s+/', ' ', $cleaned);
	    return trim($cleaned);
	}
}
