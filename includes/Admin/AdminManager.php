<?php

namespace ZimPriceCheck\ApiEnds\Admin;

use ZimPriceCheck\ApiEnds\Admin\CarbonFields\Settings;
use ZimPriceCheck\ApiEnds\Admin\CarbonFields\AdsSettings;

class AdminManager
{
    public function __construct()
    {
        add_action('after_setup_theme', [$this, 'boot_carbon_fields']);
        add_action('carbon_fields_register_fields', [$this, 'register_fields']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_styles']);
    }

    public function boot_carbon_fields()
    {
        require_once API_END_BASE . '/vendor/autoload.php';
        \Carbon_Fields\Carbon_Fields::boot();
    }

    public function register_fields()
    {
        new Settings();
        new AdsSettings();
    }

    public function enqueue_styles()
    {
        wp_enqueue_style(
            'zpc_api_ends_admin_style',
            API_END_URL . 'templates/css/carbon_admin.css',
            array(),
            API_END_VERSION
        );
    }
}
