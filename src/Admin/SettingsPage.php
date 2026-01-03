<?php

namespace ZPC\ApiEnds\Admin;

/**
 * V2 Admin Settings Page
 * 
 * Provides a modern settings page for the API Ends plugin.
 */
class SettingsPage
{
    private const OPTION_GROUP = 'zpc_api_ends_v2_settings';
    private const PAGE_SLUG = 'zpc-api-ends-v2';

    public function __construct()
    {
        add_action('admin_menu', [$this, 'addMenuPage']);
        add_action('admin_init', [$this, 'registerSettings']);
    }

    public function addMenuPage(): void
    {
        add_menu_page(
            'API Ends V2 Settings',
            'API Ends V2',
            'manage_options',
            self::PAGE_SLUG,
            [$this, 'renderPage'],
            'dashicons-rest-api',
            80
        );
    }

    public function registerSettings(): void
    {
        // API Configuration Section
        add_settings_section(
            'zpc_api_config_section',
            'API Configuration',
            [$this, 'renderApiConfigSection'],
            self::PAGE_SLUG
        );

        // API Base URL
        register_setting(self::OPTION_GROUP, 'zpc_api_base_url', [
            'type' => 'string',
            'sanitize_callback' => 'esc_url_raw',
            'default' => 'https://api.clientemails.xyz/api',
        ]);

        add_settings_field(
            'zpc_api_base_url',
            'API Base URL',
            [$this, 'renderApiBaseUrlField'],
            self::PAGE_SLUG,
            'zpc_api_config_section'
        );

        // Cache Duration
        register_setting(self::OPTION_GROUP, 'zpc_cache_duration', [
            'type' => 'integer',
            'sanitize_callback' => 'absint',
            'default' => 3600,
        ]);

        add_settings_field(
            'zpc_cache_duration',
            'Cache Duration (seconds)',
            [$this, 'renderCacheDurationField'],
            self::PAGE_SLUG,
            'zpc_api_config_section'
        );
    }

    public function renderApiConfigSection(): void
    {
        echo '<p>Configure the external API connection settings for the V2 architecture.</p>';
    }

    public function renderApiBaseUrlField(): void
    {
        $value = get_option('zpc_api_base_url', 'https://api.clientemails.xyz/api');
        echo '<input type="url" name="zpc_api_base_url" value="' . esc_attr($value) . '" class="regular-text" />';
        echo '<p class="description">The base URL for the ZPC API (e.g., https://api.clientemails.xyz/api)</p>';
    }

    public function renderCacheDurationField(): void
    {
        $value = get_option('zpc_cache_duration', 3600);
        echo '<input type="number" name="zpc_cache_duration" value="' . esc_attr($value) . '" min="0" step="60" />';
        echo '<p class="description">How long to cache API responses (in seconds). Default: 3600 (1 hour)</p>';
    }

    public function renderPage(): void
    {
        if (!current_user_can('manage_options')) {
            return;
        }

        // Check if settings were saved
        if (isset($_GET['settings-updated'])) {
            add_settings_error('zpc_messages', 'zpc_message', 'Settings Saved', 'updated');
        }

        ?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            
            <div style="background: #fff; border: 1px solid #c3c4c7; border-left: 4px solid #2271b1; padding: 12px; margin: 20px 0;">
                <strong>V2 Architecture</strong> - This is the new modern settings panel for API Ends.
            </div>

            <?php settings_errors('zpc_messages'); ?>

            <form action="options.php" method="post">
                <?php
                settings_fields(self::OPTION_GROUP);
                do_settings_sections(self::PAGE_SLUG);
                submit_button('Save V2 Settings');
                ?>
            </form>

            <hr />
            <h2>Current Configuration Status</h2>
            <table class="widefat" style="max-width: 600px;">
                <tbody>
                    <tr>
                        <th>API Base URL</th>
                        <td><code><?php echo esc_html(get_option('zpc_api_base_url', 'Not Set')); ?></code></td>
                    </tr>
                    <tr>
                        <th>Cache Duration</th>
                        <td><?php echo esc_html(get_option('zpc_cache_duration', 3600)); ?> seconds</td>
                    </tr>
                    <tr>
                        <th>Legacy ZIMAPI_BASE</th>
                        <td><code><?php echo defined('ZIMAPI_BASE') ? esc_html(ZIMAPI_BASE) : 'Not Defined'; ?></code></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <?php
    }
}
