<?php

/**
 * Class ZP_USD_ZiG_Table
 *
 * Handles the logic for the [usd-zig] shortcode.
 */
class ZP_USD_ZiG_Table
{
    /**
     * @var ZIMAPI The ZIMAPI instance.
     */
    private $zim_api;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->zim_api = new ZIMAPI(ZIMAPI_BASE);
    }

    /**
     * Retrieves the latest rates using multiCallApi.
     *
     * @return array|WP_Error The rates data or WP_Error on failure.
     */
    public function get_rates()
    {
        // Using multiCallApi even for a single endpoint as requested for consistency
        $endpoints = [
            'rates' => [
                'endpoint' => '/rates/fx-rates',
            ],
        ];

        try {
            return $this->zim_api->multiCallApi($endpoints, zp_get_remote_ip());
        } catch (Exception $e) {
            error_log('Error retrieving rates for USD-ZiG table: ' . $e->getMessage());
            return new WP_Error('api_error', $e->getMessage());
        }
    }

    /**
     * Renders the table.
     *
     * @return string The HTML output.
     */
    public function render()
    {
        $data = $this->get_rates();

        if (is_wp_error($data)) {
            require_once plugin_dir_path(dirname(dirname(__FILE__))) . 'includes/class-show-notice.php';
            return ZP_SHOW_NOTICE::showError("We couldn't retrieve the latest rates at the moment. Please try again later.");
        }

        // Check for success in multiCallApi results
        if (empty($data['rates']['success'])) {
            error_log('ZP_USD_ZiG_Table: API call failed.');
            require_once plugin_dir_path(dirname(dirname(__FILE__))) . 'includes/class-show-notice.php';
            return ZP_SHOW_NOTICE::showError("We couldn't retrieve the latest rates at the moment. Please try again later.");
        }

        $rates = $data['rates']['data'];

        // Validate data structure
        if (!isset($rates['rates']['ZiG_Mid'])) {
            error_log('ZP_USD_ZiG_Table: Invalid data structure in API response (missing ZiG_Mid).');
            require_once plugin_dir_path(dirname(dirname(__FILE__))) . 'includes/class-show-notice.php';
            return ZP_SHOW_NOTICE::showError("We couldn't retrieve the latest rates at the moment. Please try again later.");
        }

        ob_start();
        include plugin_dir_path(dirname(dirname(__FILE__))) . 'templates/parts/usd-zig-table.php';
        return ob_get_clean();
    }
}
