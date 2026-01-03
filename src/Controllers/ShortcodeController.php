<?php

namespace ZPC\ApiEnds\Controllers;

use ZPC\ApiEnds\Services\ApiService;
use ZPC\ApiEnds\Services\RatesService;

use ZPC\ApiEnds\Services\FuelService;
use ZPC\ApiEnds\Services\IspService;

/**
 * Shortcode Controller
 * 
 * Centralized registry for all plugin shortcodes.
 */
final readonly class ShortcodeController
{
    public function __construct(
        private ApiService $apiService,
        private RatesService $ratesService,
        private FuelService $fuelService,
        private IspService $ispService,
        private \ZPC\ApiEnds\Services\GovtService $govtService,
        private \ZPC\ApiEnds\Services\ZesaService $zesaService,
        private \ZPC\ApiEnds\Services\ConsumerGoodsService $consumerGoodsService
    ) {
        $this->registerShortcodes();
    }

    private function registerShortcodes(): void
    {
        add_shortcode('zpc-latest-rates', [$this, 'renderLatestRates']);
        // Legacy support
        add_shortcode('show-latest-rates', [$this, 'renderLatestRates']);

        add_shortcode('zpc-latest-fuel', [$this, 'renderLatestFuel']);
        add_shortcode('show-latest-fuel-prices', [$this, 'renderLatestFuel']);

        // ISP Shortcodes
        add_shortcode('liquid-home', [$this, 'renderLiquidHome']);
        add_shortcode('telone', [$this, 'renderTelOne']);
        add_shortcode('utande', [$this, 'renderUtande']);

        // Rates Converters
        add_shortcode('zig-usd', [$this, 'renderZiGToUSD']);
        add_shortcode('usd-zig', [$this, 'renderUSDToZiG']);

        // Govt Fees
        add_shortcode('passport-fees', [$this, 'renderPassportFees']);
        add_shortcode('births-deaths', [$this, 'renderBirthDeathFees']);
        add_shortcode('citizen-status', [$this, 'renderCitizenStatusFees']);

        // Utility
        add_shortcode('zesa-tariffs', [$this, 'renderZesaTariffs']);

        // Consumer Goods
        add_shortcode('drink-prices', [$this, 'renderDrinkPrices']);
    }

    public function renderZiGToUSD(array $att = []): string
    {
        $rates = $this->ratesService->getLatestRates();
        $oe_rates = $this->ratesService->getOfficialRates();

        if (empty($rates) || empty($oe_rates)) {
            return '<p>' . __('Unable to retrieve conversion rates at this time.', 'api-end') . '</p>';
        }

        ob_start();
        $oe_array = $oe_rates; // Template expects $oe_array
        include plugin_dir_path(dirname(__DIR__)) . 'templates/parts/zig-usd-table.php';
        return ob_get_clean();
    }

    public function renderUSDToZiG(array $att = []): string
    {
        $rates = $this->ratesService->getLatestRates();

        if (empty($rates)) {
            return '<p>' . __('Unable to retrieve conversion rates at this time.', 'api-end') . '</p>';
        }

        ob_start();
        include plugin_dir_path(dirname(__DIR__)) . 'templates/parts/usd-zig-table.php';
        return ob_get_clean();
    }

    public function renderPassportFees(array $attr = []): string
    {
        $data = $this->govtService->getPassportFees();
        if (empty($data['fees']) || empty($data['rates'])) {
            return '<p>' . __('Unable to retrieve passport fees.', 'api-end') . '</p>';
        }

        $fees_data = $data['fees']['prices']['fees'] ?? [];
        $rates = $data['rates'] ?? [];
        $updated_at = $data['fees']['prices']['updatedAt'] ?? 'N/A';
        $title = 'Current Passport Application Fees';
        $caption = 'Passport Application Fees';

        ob_start();
        include plugin_dir_path(dirname(__DIR__)) . 'templates/parts/govt-fees-table.php';
        return ob_get_clean();
    }

    public function renderBirthDeathFees(array $attr = []): string
    {
        $data = $this->govtService->getBirthDeathFees();
        if (empty($data['fees']) || empty($data['rates'])) {
            return '<p>' . __('Unable to retrieve birth/death fees.', 'api-end') . '</p>';
        }

        $fees_data = $data['fees']['prices']['fees'] ?? [];
        $rates = $data['rates'] ?? [];
        $updated_at = $data['fees']['prices']['updatedAt'] ?? 'N/A';
        $title = 'National, Birth and Death Registration Fees';
        $caption = 'Birth and Death Certificate Fees';

        ob_start();
        include plugin_dir_path(dirname(__DIR__)) . 'templates/parts/govt-fees-table.php';
        return ob_get_clean();
    }

    public function renderCitizenStatusFees(array $attr = []): string
    {
        $data = $this->govtService->getCitizenStatusFees();
        if (empty($data['fees']) || empty($data['rates'])) {
            return '<p>' . __('Unable to retrieve citizen status fees.', 'api-end') . '</p>';
        }

        $fees_data = $data['fees']['prices']['fees'] ?? [];
        $rates = $data['rates'] ?? [];
        $updated_at = $data['fees']['prices']['updatedAt'] ?? 'N/A';
        $title = 'Citizen Status Fees';
        $caption = 'Citizen Status Fees';

        ob_start();
        include plugin_dir_path(dirname(__DIR__)) . 'templates/parts/govt-fees-table.php';
        return ob_get_clean();
    }

    public function renderZesaTariffs(array $att = []): string
    {
        $atts = shortcode_atts(['type' => 'tariffs'], $att, 'zesa-tariffs');
        $data = $this->zesaService->getTariffs();

        if (empty($data['prices']) || empty($data['rates'])) {
            return '<p>' . __('Unable to retrieve ZESA tariffs.', 'api-end') . '</p>';
        }

        ob_start();
        // Variables for templates are extracted from $data inside the template or passed here.
        // My templates expects $data variable available.
        
        $type = strtolower($atts['type']);
        if ($type === 'exp') {
            $zesaService = $this->zesaService; // Pass service instance for calculations in template
            include plugin_dir_path(dirname(__DIR__)) . 'templates/parts/zesa-explanation.php';
        } else {
            include plugin_dir_path(dirname(__DIR__)) . 'templates/parts/zesa-tariffs-table.php';
        }
        return ob_get_clean();
    }

    public function renderDrinkPrices(array $att = []): string
    {
        // Currently only supports 'deltaa' type
        $data = $this->consumerGoodsService->getDeltaAlcohol();

        if (empty($data['prices']) || empty($data['rates'])) {
            return '<p>' . __('Unable to retrieve drink prices.', 'api-end') . '</p>';
        }

        ob_start();
        include plugin_dir_path(dirname(__DIR__)) . 'templates/parts/drinks-table.php';
        return ob_get_clean();
    }

    public function renderLatestFuel(array $attr = []): string
    {
        $data = $this->fuelService->getLatestFuel();

        if (empty($data) || empty($data['fuel']) || empty($data['rates'])) {
            return '<p>' . __('Unable to retrieve fuel prices at this time.', 'api-end') . '</p>';
        }

        ob_start();
        $fuel_data = $data['fuel']['data'];
        $rates_data = $data['rates']['data'];
        include plugin_dir_path(dirname(__DIR__)) . 'templates/parts/latest-fuel-table.php';
        return ob_get_clean();
    }

    public function renderLatestRates(array $attr = []): string
    {
        $data = $this->ratesService->getLatestRates();

        if (empty($data)) {
            return '<p>' . __('Unable to retrieve rates at this time.', 'api-end') . '</p>';
        }

        // Output buffering for template includes
        ob_start();
        $processed_data = $data; 
        include plugin_dir_path(dirname(__DIR__)) . 'templates/parts/latest-rates-table.php';
        return ob_get_clean();
    }

    public function renderLiquidHome(array $att = []): string
    {
        $atts = shortcode_atts(['type' => 'All'], $att, 'liquid-home');
        $data = $this->ispService->getLiquidHome();

        if (empty($data)) {
            return '<p>' . __('Unable to retrieve Liquid Home prices.', 'api-end') . '</p>';
        }

        ob_start();
        $prices_data = $data['prices'];
        $rates_data = $data['rates'];
        
        $type = strtolower($atts['type']);
        $headers = [
            'fibre' => 'FibroniX packages',
            'vsat' => 'VSAT Packages',
            'lte' => 'WibroniX',
            'lte_usd' => 'WibroniX SpeeD USD',
            'fibre_usd' => 'FibroniX SpeeD USD',
        ];
        $header_text = $headers[$type] ?? '';
        $date = \ZPC\ApiEnds\Utils\DateUtil::todayFull();
        $prices = $prices_data['prices']['package_prices'] ?? [];
        $rates = $rates_data ?? [];

        include plugin_dir_path(dirname(__DIR__)) . 'templates/parts/liquid-home-table.php';
        return ob_get_clean();
    }

    public function renderTelOne(array $att = []): string
    {
        $atts = shortcode_atts(['type' => 'All'], $att, 'telone');
        $data = $this->ispService->getTelOne();

        if (empty($data)) {
            return '<p>' . __('Unable to retrieve TelOne prices.', 'api-end') . '</p>';
        }

        ob_start();
        $prices_data = $data['prices'];
        $rates_data = $data['rates'];

        $type = strtolower($atts['type']);
        $package_description = [
            "lte" => "Blaze LTE (ZiG)",
            "lte_usd" => "Blaze LTE (USD)",
            "adsl" => "ADSL (ZiG)",
            "adsl_usd" => "ADSL (USD)",
            "fibre" => "Fibre",
            "wifi" => 'WIFI',
            "vsat" => 'VSAT/LEO',
            "usd" => "USD Bonus Bundle",
        ];
        $header_text = $package_description[$type] ?? "Internet";
        $date = \ZPC\ApiEnds\Utils\DateUtil::todayFull();
        $prices = $prices_data['prices']['package_prices'] ?? [];
        $rates = $rates_data ?? [];

        include plugin_dir_path(dirname(__DIR__)) . 'templates/parts/telone-table.php';
        return ob_get_clean();
    }

    public function renderUtande(array $att = []): string
    {
        $atts = shortcode_atts(['type' => 'All'], $att, 'utande');
        $data = $this->ispService->getUtande();

        if (empty($data)) {
            return '<p>' . __('Unable to retrieve Utande prices.', 'api-end') . '</p>';
        }

        ob_start();
        $prices_data = $data['prices'];
        $rates_data = $data['rates'];

        $type = $atts['type']; // Utande seems to use case-sensitive keys in legacy array? 
        // Legacy: $type = "All"; if... $type = $attr["type"];
        // Logic: $package_description = ["LTE" => "LTE", ...]; $product['last_mile'] === $connType
        // Shortcode attribute is usually lowercase in usage but let's trust exact pass for now or normalize.
        // Legacy code: $connType IS the $type. 
        // If user passes `type="Fixed"`, legacy uses it directly.
        // I will pass "LTE" etc as is, default "All" might not match?
        // Legacy loop: if ($product['last_mile'] === $connType)
        // If type is "All", and last_mile is "LTE", it won't match.
        // Legacy implementation seems to expect a specific type to show anything. 
        // If type is "All", $product_table is empty? 
        // Looking at legacy `buildUtandePrices`: `if ($product['last_mile'] === $connType)`
        // So yes, "All" yields nothing unless there is a "All" last_mile.
        // I should probably preserve this behavior or fix it if "All" is meant to show all. 
        // But user asked for parity.
        
        $date = \ZPC\ApiEnds\Utils\DateUtil::todayFull();
        $prices = $prices_data['prices']['package_prices'] ?? [];
        $rates = $rates_data ?? [];

        include plugin_dir_path(dirname(__DIR__)) . 'templates/parts/utande-table.php';
        return ob_get_clean();
    }
}
