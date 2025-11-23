<?php

/**
 * Class ZP_Fine_Levels
 *
 * Handles the logic for the [govt-fines] and [fine-levels] shortcodes.
 */
class ZP_Fine_Levels
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
     * Retrieves the fine levels and rates data using multiCallApi.
     *
     * @return array|WP_Error The data or WP_Error on failure.
     */
    public function get_data()
    {
        $endpoints = [
            'fines' => [
                'endpoint' => '/stats/fines/all',
            ],
            'rates' => [
                'endpoint' => '/rates/fx-rates',
            ],
        ];

        try {
            return $this->zim_api->multiCallApi($endpoints, zp_get_remote_ip());
        } catch (Exception $e) {
            error_log('Error retrieving fine levels and rates: ' . $e->getMessage());
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
        $data = $this->get_data();

        if (is_wp_error($data)) {
            require_once plugin_dir_path(dirname(dirname(__FILE__))) . 'includes/class-show-notice.php';
            return ZP_SHOW_NOTICE::showError("We couldn't retrieve the fine levels at the moment. Please try again later.");
        }

        // Check for success in multiCallApi results
        if (empty($data['fines']['success']) || empty($data['rates']['success'])) {
            require_once plugin_dir_path(dirname(dirname(__FILE__))) . 'includes/class-show-notice.php';
            return ZP_SHOW_NOTICE::showError("We couldn't retrieve the fine levels at the moment. Please try again later.");
        }

        $fines_data = $data['fines']['data'];
        $rates_data = $data['rates']['data'];

        ob_start();
        include plugin_dir_path(dirname(dirname(__FILE__))) . 'templates/parts/fine-levels-table.php';
        return ob_get_clean();
    }
}
