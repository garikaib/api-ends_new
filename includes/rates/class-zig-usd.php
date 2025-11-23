<?php

/**
 * Class ZP_ZiG_USD_Table
 *
 * Handles the logic for the [zig-usd] shortcode.
 */
class ZP_ZiG_USD_Table
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
    public function get_data()
    {
        $endpoints = [
            'rates' => [
                'endpoint' => '/rates/fx-rates',
            ],
            'oe_rates' => [
                'endpoint' => '/rates/oe-rates/raw',
            ],
        ];

        try {
            return $this->zim_api->multiCallApi($endpoints, zp_get_remote_ip());
        } catch (Exception $e) {
            error_log('Error retrieving rates for ZiG-USD table: ' . $e->getMessage());
            return new WP_Error('api_error', $e->getMessage());
        }
    }

    /**
     * Builds the Official Exchange (OE) array.
     *
     * @param array $oe_rates The OE rates data.
     * @return array The filtered OE array.
     */
    private function build_oe_array(array $oe_rates)
    {
        $wanted_currencies = [
            'ZAR', 'BWP', 'ZMW', 'GBP', 'EUR', 'JPY', 'AUD', 'TZS', 'CNY', 'NZD', 'NGN',
        ];

        $oe_array = [];

        if (isset($oe_rates['rates']) && is_array($oe_rates['rates'])) {
            foreach ($oe_rates['rates'] as $rate) {
                if (is_array($rate)) {
                    foreach ($rate as $i => $value) {
                        $symbol = key($rate[$i]);
                        if (in_array($symbol, $wanted_currencies)) {
                            $oe_array[$symbol] = $rate[$i][$symbol];
                        }
                    }
                }
            }
        }

        return $oe_array;
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
            return ZP_SHOW_NOTICE::showError("We couldn't retrieve the latest rates at the moment. Please try again later.");
        }

        // Check for success in multiCallApi results
        if (empty($data['rates']['success']) || empty($data['oe_rates']['success'])) {
            error_log('ZP_ZiG_USD_Table: API call failed or partial failure.');
            require_once plugin_dir_path(dirname(dirname(__FILE__))) . 'includes/class-show-notice.php';
            return ZP_SHOW_NOTICE::showError("We couldn't retrieve the latest rates at the moment. Please try again later.");
        }

        $rates = $data['rates']['data'];
        $oe_rates = $data['oe_rates']['data'];

        // Validate data structure
        if (!isset($rates['rates']) || !isset($oe_rates['rates'])) {
            error_log('ZP_ZiG_USD_Table: Invalid data structure in API response.');
            require_once plugin_dir_path(dirname(dirname(__FILE__))) . 'includes/class-show-notice.php';
            return ZP_SHOW_NOTICE::showError("We couldn't retrieve the latest rates at the moment. Please try again later.");
        }

        $oe_array = $this->build_oe_array($oe_rates);

        ob_start();
        include plugin_dir_path(dirname(dirname(__FILE__))) . 'templates/parts/zig-usd-table.php';
        return ob_get_clean();
    }
}
