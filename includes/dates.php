<?php
/**
 * Returns the current date and time in the specified timezone.
 *
 * @return string The current date and time in the specified timezone.
 */
function zp_today_full_date()
{
    $date_time = current_time('timestamp');
    $date = date_i18n('l, d F Y', $date_time);
    return $date;
}

/**
 * Returns the current month and year in the specified timezone.
 *
 * @return string The current month and year in the specified timezone.
 */
function zp_month_year()
{
    $date_time = current_time('timestamp');
    $date = date_i18n('F Y', $date_time);
    return $date;
}
