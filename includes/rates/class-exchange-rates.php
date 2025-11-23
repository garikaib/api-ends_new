<?php

/**
 * Class ZP_Exchange_Rates
 *
 * Handles the retrieval and processing of exchange rates.
 */
class ZP_Exchange_Rates
{
    /**
     * @var ZIMAPI The ZIMAPI instance.
     */
    private $zim_rates;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->zim_rates = new ZIMAPI(ZIMAPI_BASE);
    }

    /**
     * Retrieves the latest exchange rates.
     *
     * @return array|WP_Error The rates data or WP_Error on failure.
     */
    public function get_rates()
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
            return $this->zim_rates->multiCallApi($endpoints, zp_get_remote_ip());
        } catch (Exception $e) {
            error_log('Error retrieving latest Exchange Rates: ' . $e->getMessage());
            return new WP_Error('api_error', $e->getMessage());
        }
    }

    /**
     * Processes the raw rates data for display.
     *
     * @param array $data The raw API data.
     * @return array The processed data ready for the view.
     */
    public function process_rates($data)
    {
        // Check for success flags
        if (empty($data['oe_rates']['success']) || empty($data['rates']['success'])) {
            return new WP_Error('api_error', 'API call failed or partial failure');
        }

        if (!isset($data['oe_rates']['data']) || !is_array($data['oe_rates']['data'])) {
            return new WP_Error('api_error', 'Invalid OE rates data');
        }
        if (!isset($data['rates']['data']) || !is_array($data['rates']['data'])) {
            return new WP_Error('api_error', 'Invalid rates data');
        }

        $oe_rates = $data['oe_rates']['data'];
        $rates_data = $data['rates']['data'];

        $oe_array = $this->build_oe_array($oe_rates);
        $zig_zag_array = $this->zig_zag($oe_array, $rates_data['rates']['ZiG_Mid']);
        $rates_data['rates'] = array_merge($rates_data["rates"], $zig_zag_array);

        return $rates_data;
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
     * Calculates ZiG cross rates.
     *
     * @param array $oe_array The OE array.
     * @param float $mid The ZiG mid rate.
     * @return array The calculated cross rates.
     */
    private function zig_zag(array $oe_array, float $mid)
    {
        $oe_zig_zag = array();
        foreach ($oe_array as $key => $value) {
            $the_key = strtolower($key) . "_to_zig";
            $reverse_key = "zig_to_" . strtolower($key);

            if ($value != 0) {
                $oe_zig_zag[$the_key] = $mid / $value;
                $oe_zig_zag[$reverse_key] = 1 / $oe_zig_zag[$the_key];
                $oe_zig_zag[$the_key] = number_format($oe_zig_zag[$the_key], 4);
                $oe_zig_zag[$reverse_key] = number_format($oe_zig_zag[$reverse_key], 4);
            }
        }

        return $oe_zig_zag;
    }

    /**
     * Renders the rates table.
     *
     * @return string The HTML output.
     */
    public function render()
    {
        $data = $this->get_rates();

        if (is_wp_error($data)) {
            require_once plugin_dir_path(dirname(dirname(__FILE__))) . 'includes/class-show-notice.php';
            return ZP_SHOW_NOTICE::showError("We couldn't retrieve the latest Exchange rates at the moment. Please try again later.");
        }

        $processed_data = $this->process_rates($data);

        if (is_wp_error($processed_data)) {
            require_once plugin_dir_path(dirname(dirname(__FILE__))) . 'includes/class-show-notice.php';
            return ZP_SHOW_NOTICE::showError("We couldn't retrieve the latest Exchange rates at the moment. Please try again later.");
        }

        ob_start();
        include plugin_dir_path(dirname(dirname(__FILE__))) . 'templates/parts/latest-rates-table.php';
        return ob_get_clean();
    }
}
