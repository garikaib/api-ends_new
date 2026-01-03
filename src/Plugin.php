<?php

namespace ZPC\ApiEnds;

use ZPC\ApiEnds\Services\ApiService;
use ZPC\ApiEnds\Services\RatesService;
use ZPC\ApiEnds\Services\HistoricalRatesService;
use ZPC\ApiEnds\Admin\Dashboard;
use ZPC\ApiEnds\Controllers\ShortcodeController;

/**
 * Main Plugin Class
 * 
 * PHP 8.2+ Architecture
 */
final class Plugin
{
    private ApiService $apiService;
    private RatesService $ratesService;
    private Services\FuelService $fuelService;
    private Services\IspService $ispService;
    private Services\GovtService $govtService;
    private Services\ZesaService $zesaService;
    private Services\ConsumerGoodsService $consumerGoodsService;
    private Services\TransportService $transportService;
    private Services\TelecomService $telecomService;
    private HistoricalRatesService $historicalRatesService;

    public function __construct(
        private string $version,
        private string $path,
        private string $url
    ) {
        $this->apiService = new Services\ApiService();
        $this->ratesService = new Services\RatesService($this->apiService);
        $this->fuelService = new Services\FuelService($this->apiService);
        $this->ispService = new Services\IspService($this->apiService); // New Service
        $this->govtService = new Services\GovtService($this->apiService);
        $this->zesaService = new Services\ZesaService($this->apiService);
        $this->consumerGoodsService = new Services\ConsumerGoodsService($this->apiService);
        $this->transportService = new Services\TransportService($this->apiService);
        $this->telecomService = new Services\TelecomService($this->apiService);
        $this->historicalRatesService = new Services\HistoricalRatesService($this->apiService);
        
        $this->init();
    }

    private function init(): void
    {
        // Enqueue Styles
        add_action('wp_enqueue_scripts', [$this, 'enqueueAssets']);

        // Initialize Admin Dashboard
        if (is_admin()) {
            new Admin\Dashboard();
        }

        // Initialize Controllers
        new Controllers\ShortcodeController($this->apiService, $this->ratesService, $this->fuelService, $this->ispService, $this->govtService, $this->zesaService, $this->consumerGoodsService, $this->transportService, $this->telecomService);

        add_action('init', [$this, 'onInit']);
    }

    public function enqueueAssets(): void
    {
        wp_enqueue_style(
            'zpc-api-styles',
            $this->url . 'src/UI/Styles.css',
            [],
            $this->version
        );
    }

    public function onInit(): void
    {
        // Register WP-CLI commands if running in CLI
        if (defined('WP_CLI') && WP_CLI) {
            $this->registerCliCommands();
        }
    }

    private function registerCliCommands(): void
    {
        \WP_CLI::add_command('zpc-rates', function ($args) {
            $date = $args[0] ?? date('Y-m-d');
            $id = $this->historicalRatesService->publishDate($date);
            
            if ($id) {
                \WP_CLI::success("Published rates post for $date. ID: $id");
            } else {
                \WP_CLI::error("Failed to publish rates post for $date.");
            }
        });
    }

    public function getVersion(): string
    {
        return $this->version;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getUrl(): string
    {
        return $this->url;
    }
}
