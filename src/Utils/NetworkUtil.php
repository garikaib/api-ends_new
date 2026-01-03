<?php

namespace ZPC\ApiEnds\Utils;

/**
 * Network Utility
 * 
 * Handles IP detection and common networking helpers.
 */
final readonly class NetworkUtil
{
    /**
     * Get the client's IP address securely.
     */
    public static function getRemoteIp(): string
    {
        $ip = $_SERVER['HTTP_CLIENT_IP'] 
            ?? (explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'] ?? '')[0])
            ?? $_SERVER['REMOTE_ADDR'] 
            ?? '0.0.0.0';

        $ip = trim($ip);

        return filter_var($ip, FILTER_VALIDATE_IP) ? $ip : '0.0.0.0';
    }
}
