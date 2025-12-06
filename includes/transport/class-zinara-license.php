<?php

/**
 * Class ZP_Zinara_License
 *
 * Handles the retrieval and display of ZINARA Vehicle Licensing fees.
 *
 * @package    Api_End
 * @subpackage Api_End/includes/transport
 * @author     Garikai Dzoma <garikaib@gmail.com>
 */
class ZP_Zinara_License
{
    /**
     * The API endpoint for Zinara fees.
     *
     * @var string
     */
    private $endpoint = '/prices/zinara/fees';

    /**
     * Initialize the class and register the shortcode.
     */
    public function __construct()
    {
        add_shortcode('zinara-license', array($this, 'render_shortcode'));
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
                'currency' => 'zig', // 'zig' or 'usd'
            ),
            $atts,
            'zinara-license'
        );

        // Normalize currency
        $currency = strtolower($atts['currency']);
        if (!in_array($currency, ['zig', 'usd'])) {
            $currency = 'zig';
        }

        try {
            $api = new ZIMAPI(ZIMAPI_BASE);
            $prices = $api->callApi($this->endpoint, zp_get_remote_ip());
            
            // Fetch rates as well, often needed for conversions or display context
            $rates_endpoint = "/rates/fx-rates";
            $rates = $api->callApi($rates_endpoint, zp_get_remote_ip());

            ob_start();
            require API_END_BASE . 'templates/parts/zinara-license-table.php';
            return ob_get_clean();

        } catch (Exception $e) {
            error_log('Error retrieving Zinara License fees: ' . $e->getMessage());
            if (class_exists('ZP_SHOW_NOTICE')) {
                return ZP_SHOW_NOTICE::showError("We couldn't retrieve the latest Zinara License fees at the moment. Please try again later.");
            }
            return '<p>Error retrieving data.</p>';
        }
    }
}
