<?php

namespace ZimPriceCheck\ApiEnds\Admin;

use ZimPriceCheck\ApiEnds\Admin\CarbonFields\Settings;
use ZimPriceCheck\ApiEnds\Admin\CarbonFields\AdsSettings;
use ZimPriceCheck\ApiEnds\Admin\CarbonFields\CacheSettings;
use ZimPriceCheck\ApiEnds\Admin\CarbonFields\DateSettings;

class AdminManager
{
    public function __construct()
    {
        add_action('after_setup_theme', [$this, 'boot_carbon_fields']);
        add_action('carbon_fields_register_fields', [$this, 'register_fields']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_assets']);
        add_action('wp_ajax_zpc_flush_cache', [$this, 'ajax_flush_cache']);
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
        new CacheSettings();
        new DateSettings();
    }

    public function enqueue_assets()
    {
        wp_enqueue_style(
            'zpc_api_ends_admin_style',
            API_END_URL . 'templates/css/carbon_admin.css',
            array(),
            API_END_VERSION
        );

        wp_enqueue_script(
            'zpc_api_ends_admin_js',
            API_END_URL . 'assets/js/zpc-admin.js',
            array('jquery'),
            API_END_VERSION,
            true
        );

        wp_localize_script('zpc_api_ends_admin_js', 'zpc_admin_vars', array(
            'nonce' => wp_create_nonce('zpc_flush_cache_nonce')
        ));
    }

    public function ajax_flush_cache()
    {
        check_ajax_referer('zpc_flush_cache_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error('Unauthorized');
        }

        // Increment cache version to invalidate old keys
        $current_version = get_option('zimapi_cache_version', 1);
        update_option('zimapi_cache_version', $current_version + 1);

        wp_send_json_success();
    }
}
