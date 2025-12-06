<?php

/**
 * Class ZP_Tollgates
 *
 * Handles the retrieval and display of ZINARA Tollgate prices.
 *
 * @package    Api_End
 * @subpackage Api_End/includes/transport
 * @author     Garikai Dzoma <garikaib@gmail.com>
 */
class ZP_Tollgates
{
    /**
     * The API endpoint for tollgates.
     *
     * @var string
     */
    private $endpoint = '/prices/zinara/tollgates';

    /**
     * Initialize the class and register the shortcode.
     */
    public function __construct()
    {
        add_shortcode('tollgates', array($this, 'render_shortcode'));
    }

    /**
     * Render the shortcode.
     *
     * @param array $atts Shortcode attributes.
     * @return string HTML output.
     */
    public function render_shortcode($atts)
    {
        $atts = shortcode_atts(
            array(
                'type' => 'standard', // 'standard' or 'premium'
            ),
            $atts,
            'tollgates'
        );

        // Normalize type
        $type = strtolower($atts['type']);
        if (!in_array($type, ['standard', 'premium'])) {
            $type = 'standard';
        }

        try {
            $api = new ZIMAPI(ZIMAPI_BASE);
            $prices = $api->callApi($this->endpoint, zp_get_remote_ip());

            // We might need exchange rates if the prices are dynamic or need conversion, 
            // but usually tollgates are fixed. However, existing code fetched rates.
            // Let's fetch them just in case the template needs them or for consistency.
            $rates_endpoint = "/rates/fx-rates";
            $rates = $api->callApi($rates_endpoint, zp_get_remote_ip());

            ob_start();
            require API_END_BASE . 'templates/parts/tollgates-table.php';
            return ob_get_clean();

        } catch (Exception $e) {
            error_log('Error retrieving Tollgate prices: ' . $e->getMessage());
            if (class_exists('ZP_SHOW_NOTICE')) {
                return ZP_SHOW_NOTICE::showError("We couldn't retrieve the latest Tollgate prices at the moment. Please try again later.");
            }
            return '<p>Error retrieving data.</p>';
        }
    }
}
