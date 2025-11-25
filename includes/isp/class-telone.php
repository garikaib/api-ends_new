<?php

class ZP_TelOne
{
    public function render($atts)
    {
        $atts = shortcode_atts(
            array(
                'type' => 'All',
            ),
            $atts,
            'telone'
        );

        $type = strtolower($atts['type']);

        try {
            // Require necessary files
            require_once API_END_BASE . 'includes/show-price-footer.php';
            require_once API_END_BASE . 'includes/format-prices.php';
            require_once API_END_BASE . 'includes/dates.php';
            require_once API_END_BASE . 'includes/usd-equivalent.php';

            $api = new CachedZIMAPI(ZIMAPI_BASE);
            
            // Fetch data using multiCallApi for optimization
            $results = $api->multiCallApi([
                'prices' => ['endpoint' => '/prices/isp/telone'],
                'rates' => ['endpoint' => '/rates/fx-rates']
            ], zp_get_remote_ip());

            $prices_data = $results['prices'] ?? ['success' => false];
            $rates_data = $results['rates'] ?? ['success' => false];

            if (!$prices_data['success'] || !$rates_data['success']) {
                 // Log specific errors if needed
                 if (!$prices_data['success']) error_log('TelOne Prices fetch failed: ' . ($prices_data['error'] ?? 'Unknown error'));
                 if (!$rates_data['success']) error_log('Rates fetch failed: ' . ($rates_data['error'] ?? 'Unknown error'));
            }

            $prices = $prices_data['data']['prices']['package_prices'] ?? [];
            $rates = $rates_data['data'] ?? [];

            // Prepare data for template
            $header_text = $this->get_package_description($type);
            $date = esc_html(zp_today_full_date());
            
            // Load template
            ob_start();
            include API_END_BASE . 'templates/parts/telone-table.php';
            return ob_get_clean();

        } catch (Exception $e) {
            error_log('Error in ZP_TelOne: ' . $e->getMessage());
            require_once API_END_BASE . 'includes/class-show-notice.php';
            return ZP_SHOW_NOTICE::showError("We couldn't retrieve the latest TelOne prices at the moment. Please try again later.</strong></p>");
        }
    }

    private function get_package_description(string $conn_type): string
    {
        $package_description = [
            "lte" => "Blaze LTE",
            "adsl" => "ADSL",
            "fibre" => "Fibre",
            "wifi" => 'WIFI',
            "vsat" => 'VSAT',
            "usd" => "USD Bonus Bundle",
        ];

        return isset($package_description[$conn_type]) ? $package_description[$conn_type] : "Internet";
    }

    public static function telone_is_capped(string $required, array $product)
    {
        if (!$product['capped']) {
            return "Uncapped";
        } else {
            return esc_html($product[$required]);
        }
    }
}
