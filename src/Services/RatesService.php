<?php

namespace ZPC\ApiEnds\Services;

/**
 * Rates Service
 * 
 * Logic for processing and calculating currency exchange rates.
 * Ported from legacy ZP_Exchange_Rates with PHP 8.2 enhancements.
 */
final class RatesService
{
    public function __construct(
        private ApiService $apiService
    ) {}

    /**
     * Get and process latest rates.
     */
    public function getLatestRates(): array
    {
        error_log('[RatesService::getLatestRates] Starting...');
        
        // V2 API endpoints
        $rates = $this->apiService->get('v2/rates/fx-rates');
        error_log('[RatesService::getLatestRates] fx-rates response: ' . print_r($rates, true));
        
        $oeRates = $this->apiService->get('v2/rates/oe-rates/raw');
        error_log('[RatesService::getLatestRates] oe-rates response: ' . print_r($oeRates, true));

        if (empty($rates) || empty($oeRates)) {
            error_log('[RatesService::getLatestRates] Empty data - returning empty array');
            return [];
        }

        return $this->processRates($rates, $oeRates);
    }

    /**
     * Get official rates for OE (Open Exchange) currencies.
     * Returns processed array for ZiG/USD converters.
     */
    public function getOfficialRates(): array
    {
        error_log('[RatesService::getOfficialRates] Starting...');
        
        // V2 API endpoint
        $oeRates = $this->apiService->get('v2/rates/oe-rates/raw');
        error_log('[RatesService::getOfficialRates] oe-rates response: ' . print_r($oeRates, true));

        if (empty($oeRates)) {
            error_log('[RatesService::getOfficialRates] Empty data - returning empty array');
            return [];
        }

        $oeRaw = $oeRates['rates'] ?? [];
        return $this->buildOeArray($oeRaw);
    }

    /**
     * Legacy logic for building OE array and ZIG cross rates.
     */
    private function processRates(array $fxData, array $oeData): array
    {
        $oeRaw = $oeData['rates'] ?? [];
        $fxRates = $fxData['rates'] ?? [];
        $mid = (float) ($fxRates['ZiG_Mid'] ?? 0);

        $oeArray = $this->buildOeArray($oeRaw);
        $zigZag = $this->calculateZigZag($oeArray, $mid);

        $fxRates = array_merge($fxRates, $zigZag);
        $fxData['rates'] = $fxRates;

        return $fxData;
    }

    private function buildOeArray(array $raw): array
    {
        $wanted = ['ZAR', 'BWP', 'ZMW', 'GBP', 'EUR', 'JPY', 'AUD', 'TZS', 'CNY', 'NZD', 'NGN'];
        $oe = [];

        foreach ($raw as $group) {
            if (!is_array($group)) continue;
            foreach ($group as $item) {
                $symbol = (string) key($item);
                if (in_array($symbol, $wanted)) {
                    $oe[$symbol] = $item[$symbol];
                }
            }
        }

        return $oe;
    }

    private function calculateZigZag(array $oe, float $mid): array
    {
        $zigzag = [];
        if ($mid <= 0) return $zigzag;

        foreach ($oe as $currency => $val) {
            if ($val <= 0) continue;
            
            $key = strtolower($currency) . '_to_zig';
            $rev = 'zig_to_' . strtolower($currency);
            
            $rate = $mid / $val;
            $zigzag[$key] = number_format($rate, 4, '.', '');
            $zigzag[$rev] = number_format(1 / $rate, 4, '.', '');
        }

        return $zigzag;
    }
}
