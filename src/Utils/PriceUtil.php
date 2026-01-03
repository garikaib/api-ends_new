<?php

namespace ZPC\ApiEnds\Utils;

/**
 * Price Utility
 * 
 * Handles all currency and price formatting with DRY principles and strict types.
 */
final readonly class PriceUtil
{
    /**
     * Format a price with currency symbol and decimal places.
     */
    public static function format(float|int|string $amount, string $currency = 'USD', int $decimals = 2): string
    {
        $value = (float) $amount;
        // Match legacy formatting: Space for thousands separator
        $formatted = number_format($value, $decimals, '.', ' ');

        return match (strtoupper($currency)) {
            'USD' => 'US$' . $formatted,
            'ZWL' => 'ZWL$' . $formatted,
            'ZIG' => $formatted . ' ZiG', // Legacy puts currency after
            default => $formatted . ' ' . $currency,
        };
    }

    /**
     * Safely calculate a conversion between two rates.
     */
    public static function convert(float $amount, float $rate): float
    {
        if ($rate <= 0) {
            return 0.0;
        }

        return $amount * $rate;
    }
}
