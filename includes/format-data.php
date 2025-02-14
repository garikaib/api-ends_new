<?php
/**
 * File: format-data.php
 * Purpose: Format data based on the provided key
 *
 * @author: Your name
 */

const ZP_MB_GB = 1000;

function zp_format_data(int $data): string
{
    if ($data >= ZP_MB_GB) {
        return round($data / ZP_MB_GB, 2) . "GB";
    }
    return $data . "MB";
}

function zp_format_hybrid_bundle(int $data, string $key): string
{
    require_once plugin_dir_path(__DIR__) . "includes/get-mobile-data-desc.php";

    switch (zp_get_description($key)) {
        case "data":
            return zp_format_data($data);
        case "voice":
            return "{$data} minutes";
        default:
            return "{$data}";
    }
}
