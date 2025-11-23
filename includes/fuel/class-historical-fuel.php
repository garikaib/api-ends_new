<?php

/**
 * Class ZP_Historical_Fuel
 *
 * Handles the logic for the [historical-fuel-prices-table] shortcode.
 */
class ZP_Historical_Fuel
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
     * Retrieves historical fuel prices using multiCallApi.
     *
     * @return array|WP_Error The data or WP_Error on failure.
     */
    public function get_data()
    {
        $from_date = date('Y-m-d', strtotime('-6 months'));
        $to_date   = date('Y-m-d');

        $endpoints = [
            'fuel' => [
                'endpoint' => '/fuel/',
                'method'   => 'GET',
                'payload'  => [
                    'from' => $from_date,
                    'to'   => $to_date,
                ],
            ],
        ];

        try {
            return $this->zim_api->multiCallApi($endpoints, zp_get_remote_ip());
        } catch (Exception $e) {
            error_log('Error retrieving historical fuel prices: ' . $e->getMessage());
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
            return '<p><strong>Unable to retrieve historical fuel prices at this time.</strong></p>';
        }

        // Check for success in multiCallApi results
        if (empty($data['fuel']['success'])) {
            return '<p><strong>Unable to retrieve historical fuel prices at this time.</strong></p>';
        }

        $fuel_data = $data['fuel']['data'];
        $from_date = date('Y-m-d', strtotime('-6 months'));
        $to_date   = date('Y-m-d');

        ob_start();
        include plugin_dir_path(dirname(dirname(__FILE__))) . 'templates/parts/historical-fuel-table.php';
        return ob_get_clean();
    }
}
