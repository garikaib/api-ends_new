<?php

class ZP_Liquid_Home
{
    public function render($atts)
    {
        $atts = shortcode_atts(
            array(
                'type' => 'All',
            ),
            $atts,
            'liquid-home'
        );

        $type = $atts['type'];

        try {
            // Require necessary files
            require_once API_END_BASE . 'includes/show-price-footer.php';
            require_once API_END_BASE . 'includes/format-prices.php';
            require_once API_END_BASE . 'includes/dates.php';

            $api = new CachedZIMAPI(ZIMAPI_BASE);
            
            // Fetch data using multiCallApi for optimization
            $results = $api->multiCallApi([
                'prices' => ['endpoint' => '/prices/isp/liquid-home'],
                'rates' => ['endpoint' => '/rates/fx-rates']
            ], zp_get_remote_ip());

            $prices_data = $results['prices'] ?? ['success' => false];
            $rates_data = $results['rates'] ?? ['success' => false];

            if (!$prices_data['success'] || !$rates_data['success']) {
                 // Log specific errors if needed
                 if (!$prices_data['success']) error_log('Liquid Home Prices fetch failed: ' . ($prices_data['error'] ?? 'Unknown error'));
                 if (!$rates_data['success']) error_log('Rates fetch failed: ' . ($rates_data['error'] ?? 'Unknown error'));
            }

            $prices = $prices_data['data']['prices']['package_prices'] ?? [];
            $rates = $rates_data['data'] ?? [];

            // Prepare data for template
            $header_text = $this->gen_table_header($type);
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

    private function gen_table_header(string $conn_type): string
    {
        $headers = [
            'Fibre' => 'FibroniX packages',
            'VSAT' => 'VSAT Packages',
            'LTE' => 'WibroniX',
            'LTE_USD' => 'WibroniX SpeeD USD',
            'Fibre_USD' => 'FibroniX SpeeD USD',
        ];

        return isset($headers[$conn_type]) ? $headers[$conn_type] : '';
    }

    public static function liquid_is_limited(string $required, array $product)
    {
        if (!$product['capped']) {
            return 'Uncapped';
        } else {
            return esc_html($product[$required]);
        }
    }
}
