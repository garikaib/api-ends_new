<?php

/**
 * Class ZP_Traffic_Fines
 *
 * Handles the logic for the [traffic-fines] shortcode.
 */
class ZP_Traffic_Fines
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
        $this->zim_api = new CachedZIMAPI(ZIMAPI_BASE);
    }

    /**
     * Retrieves the traffic fines data.
     *
     * @return array|WP_Error The data or WP_Error on failure.
     */
    public function get_data()
    {
        $endpoint = '/prices/govt/traffic-fines';

        try {
            return $this->zim_api->callApi($endpoint, zp_get_remote_ip());
        } catch (Exception $e) {
            error_log('Error retrieving traffic fines: ' . $e->getMessage());
            return new WP_Error('api_error', $e->getMessage());
        }
    }

    /**
     * Renders the table.
     *
     * @param string|null $type The category filter type.
     * @return string The HTML output.
     */
    public function render($type = null)
    {
        $data = $this->get_data();

        if (is_wp_error($data)) {
            require_once plugin_dir_path(dirname(dirname(__FILE__))) . 'includes/class-show-notice.php';
            return ZP_SHOW_NOTICE::showError("We couldn't retrieve the traffic fines at the moment. Please try again later.");
        }

        // Check for success in API result
        if (empty($data['success'])) {
            require_once plugin_dir_path(dirname(dirname(__FILE__))) . 'includes/class-show-notice.php';
            return ZP_SHOW_NOTICE::showError("We couldn't retrieve the traffic fines at the moment. Please try again later.");
        }

        ob_start();
        include plugin_dir_path(dirname(dirname(__FILE__))) . 'templates/parts/traffic-fines-table.php';
        return ob_get_clean();
    }
}
