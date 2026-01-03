<?php

namespace ZPC\ApiEnds\Utils;

use DateTime;
use Exception;

/**
 * Date Utility
 * 
 * Centralizes date formatting and manipulation.
 */
final readonly class DateUtil
{
    /**
     * Format a date string for display.
     */
    public static function formatDisplayDate(string $dateStr, string $format = 'l, d F Y'): string
    {
        try {
            $date = new DateTime($dateStr);
            return $date->format($format);
        } catch (Exception) {
            return $dateStr;
        }
    }

    /**
     * Get current date in ISO format.
     */
    public static function nowIso(): string
    {
        return date('Y-m-d');
    }

    /**
     * Validate ISO date format.
     */
    public static function isValidIso(string $date): bool
    {
        $d = DateTime::createFromFormat('Y-m-d', $date);
        return $d && $d->format('Y-m-d') === $date;
    }

    /**
     * Get today's full date (Legacy Parity).
     * e.g., "Friday, 03 January 2025"
     */
    public static function todayFull(): string
    {
        return date_i18n('l, d F Y', current_time('timestamp'));
    }
}
