<?php

class ZP_Liquid_Home
{
    public function render(array $atts): string
    {
        $atts = shortcode_atts(
            array(
                'type' => 'All',
            ),
            $atts,
            'liquid-home'
        );

        $type = strtolower($atts['type']);

        try {
            // Require necessary files
            require_once API_END_BASE . 'includes/show-price-footer.php';
            require_once API_END_BASE . 'includes/format-prices.php';
            require_once API_END_BASE . 'includes/dates.php';

            // Fetch Prices from new endpoint
            $api_prices = new CachedZIMAPI('https://a.clientemails.xyz/api');
            $prices_data = $api_prices->callApi('/prices/isp/liquid-home', zp_get_remote_ip());

            // Fetch Rates from default base
            $api_rates = new CachedZIMAPI(ZIMAPI_BASE);
            $rates_data = $api_rates->callApi('/rates/fx-rates', zp_get_remote_ip());

            if (!$prices_data['success'] || !$rates_data['success']) {
                 // Log specific errors if needed
                 if (!$prices_data['success']) error_log('Liquid Home Prices fetch failed: ' . ($prices_data['error'] ?? 'Unknown error'));
                 if (!$rates_data['success']) error_log('Rates fetch failed: ' . ($rates_data['error'] ?? 'Unknown error'));
            }

            $prices = $prices_data['prices']['package_prices'] ?? [];
            $rates = $rates_data ?? [];

            // Prepare data for template
            $header_text = $this->get_package_description($type);
            $date = esc_html(zp_today_full_date());
            
            // Load template
            ob_start();
            include API_END_BASE . 'templates/parts/liquid-home-table.php';
            return ob_get_clean();

        } catch (Exception $e) {
            error_log('Error in ZP_Liquid_Home: ' . $e->getMessage());
            require_once API_END_BASE . 'includes/class-show-notice.php';
            return ZP_SHOW_NOTICE::showError("We couldn't retrieve the latest Liquid Home prices at the moment. Please try again later.");
        }
    }

    private function get_package_description(string $conn_type): string
    {
        $headers = [
            'fibre' => 'FibroniX packages',
            'vsat' => 'VSAT Packages',
            'lte' => 'WibroniX',
            'lte_usd' => 'WibroniX SpeeD USD',
            'fibre_usd' => 'FibroniX SpeeD USD',
        ];

        return isset($headers[$conn_type]) ? $headers[$conn_type] : '';
    }

    public static function liquid_is_limited(string $required, array $product): string
    {
        if (!$product['capped']) {
            return 'Uncapped';
        } else {
            return esc_html($product[$required]);
        }
    }
}
