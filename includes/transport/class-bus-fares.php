<?php

/**
 * Class ZP_Bus_Fares
 *
 * Handles the retrieval and display of Intercity Bus Fares.
 *
 * @package    Api_End
 * @subpackage Api_End/includes/transport
 * @author     Garikai Dzoma <garikaib@gmail.com>
 */
class ZP_Bus_Fares
{
    /**
     * The API endpoint for Bus Fares.
     *
     * @var string
     */
    private $endpoint = '/prices/fares/busfares';

    /**
     * Initialize the class and register the shortcode.
     */
    public function __construct()
    {
        add_shortcode('bus-fares', array($this, 'render_shortcode'));
    }

    /**
     * Render the shortcode.
     *
     * @param array $atts Shortcode attributes.
     * @return string HTML output.
     */
    public function render_shortcode($atts)
    {
        try {
            $api = new ZIMAPI(ZIMAPI_BASE);
            $prices = $api->callApi($this->endpoint, zp_get_remote_ip());
            
            $rates_endpoint = "/rates/fx-rates";
            $rates = $api->callApi($rates_endpoint, zp_get_remote_ip());

            ob_start();
            require API_END_BASE . 'templates/parts/bus-fares-table.php';
            return ob_get_clean();

        } catch (Exception $e) {
            error_log('Error retrieving Bus Fares: ' . $e->getMessage());
            if (class_exists('ZP_SHOW_NOTICE')) {
                return ZP_SHOW_NOTICE::showError("We couldn't retrieve the latest Bus Fares at the moment. Please try again later.");
            }
            return '<p>Error retrieving data.</p>';
        }
    }
}
