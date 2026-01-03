<?php

namespace ZPC\ApiEnds\Admin;

/**
 * Admin Dashboard
 * 
 * Centralized settings hub using native WordPress Settings API.
 */
final readonly class Dashboard
{
    private const MENU_SLUG = 'zpc-api-dashboard';
    private const OPTION_GROUP = 'zpc_api_settings';

    public function __construct()
    {
        add_action('admin_menu', [$this, 'registerMenu']);
        add_action('admin_init', [$this, 'registerSettings']);
    }

    public function registerMenu(): void
    {
        add_menu_page(
            __('ZPC API', 'api-end'),
            __('ZPC API', 'api-end'),
            'manage_options',
            self::MENU_SLUG,
            [$this, 'renderDashboard'],
            'dashicons-chart-line',
            30
        );

        add_submenu_page(
            self::MENU_SLUG,
            __('General Settings', 'api-end'),
            __('General', 'api-end'),
            'manage_options',
            self::MENU_SLUG,
            [$this, 'renderDashboard']
        );
    }

    public function registerSettings(): void
    {
        register_setting(self::OPTION_GROUP, 'zimapi_base_url', [
            'type' => 'string',
            'sanitize_callback' => 'esc_url_raw',
            'default' => '',
        ]);

        register_setting(self::OPTION_GROUP, 'zimapi_cache_duration', [
            'type' => 'integer',
            'sanitize_callback' => 'absint',
            'default' => 3600,
        ]);

        add_settings_section(
            'zpc_api_core_section',
            __('Core API Configuration', 'api-end'),
            null,
            self::MENU_SLUG
        );

        add_settings_field(
            'zimapi_base_url',
            __('API Base URL', 'api-end'),
            [$this, 'renderUrlField'],
            self::MENU_SLUG,
            'zpc_api_core_section'
        );

        add_settings_field(
            'zimapi_cache_duration',
            __('Cache Duration (seconds)', 'api-end'),
            [$this, 'renderCacheField'],
            self::MENU_SLUG,
            'zpc_api_core_section'
        );
    }

    public function renderDashboard(): void
    {
        ?>
        <div class="wrap zpc-container">
            <h1><?php _e('ZimPriceCheck API Dashboard', 'api-end'); ?></h1>
            <form action="options.php" method="post">
                <?php
                settings_fields(self::OPTION_GROUP);
                do_settings_sections(self::MENU_SLUG);
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }

    public function renderUrlField(): void
    {
        $value = get_option('zimapi_base_url');
        echo '<input type="url" name="zimapi_base_url" value="' . esc_attr($value) . '" class="regular-text">';
    }

    public function renderCacheField(): void
    {
        $value = get_option('zimapi_cache_duration');
        echo '<input type="number" name="zimapi_cache_duration" value="' . esc_attr($value) . '" class="small-text"> ' . __('seconds', 'api-end');
    }
}
