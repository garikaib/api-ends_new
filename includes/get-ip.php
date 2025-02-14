<?php

/**
 * Get the client's IP address.
 *
 * @return string The client's IP address.
 */
function zp_get_remote_ip()
{
    $ip = '';

    // Use the appropriate server variable to get the IP address
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        // Use the first IP address if multiple are provided
        $ip = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];
    } else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }

    // Validate and sanitize the IP address
    return filter_var($ip, FILTER_VALIDATE_IP) ? $ip : '';
}
