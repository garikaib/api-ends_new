<?php
/**
 * Converts a Unix timestamp to a relative time string.
 *
 * @param int $timestamp The Unix timestamp to convert.
 *
 * @return string The relative time string.
 */
function to_relative_time(int $timestamp): string
{
    $output = [];
    $days = floor($timestamp / (24 * 60 * 60));
    $hours = floor(($timestamp - ($days * 24 * 60 * 60)) / (60 * 60));
    $minutes = floor(($timestamp - ($days * 24 * 60 * 60) - ($hours * 60 * 60)) / 60);
    $seconds = ($timestamp - ($days * 24 * 60 * 60) - ($hours * 60 * 60) - ($minutes * 60)) % 60;
    $has_days = false;
    $has_hours = false;
    $has_minutes = false;
    $day_string = null;
    $hour_string = null;
    $minute_string = null;
    $second_string = null;

    if ($days > 0) {
        $has_days = true;
        $day_string = sprintf(_n('%d day', '%d days', $days), $days);
        $output[] = $day_string;
    }

    if ($hours > 0) {
        $has_hours = true;
        $hour_string = sprintf(_n('%d hour', '%d hours', $hours), $hours);
        if ($has_days) {
            $hour_string = sprintf(__('and %s', 'text-domain'), $hour_string);
        }
        $output[] = $hour_string;
    }

    if ($minutes > 0) {
        $has_minutes = true;
        $minute_string = sprintf(_n('%d minute', '%d minutes', $minutes), $minutes);
        if ($has_hours) {
            $minute_string = sprintf(__('and %s', 'text-domain'), $minute_string);
        }
        $output[] = $minute_string;
    }

    if ($seconds > 0) {
        $second_string = sprintf(_n('%d second', '%d seconds', $seconds), $seconds);
        if ($has_minutes) {
            $second_string = sprintf(__('and %s', 'text-domain'), $second_string);
        }
        $output[] = $second_string;
    }

    return implode(' ', $output);
}
