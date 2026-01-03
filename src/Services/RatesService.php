<?php

namespace ZPC\ApiEnds\Services;

/**
 * Rates Service
 * 
 * Logic for processing and calculating currency exchange rates.
 * Ported from legacy ZP_Exchange_Rates with PHP 8.2 enhancements.
 */
final readonly class RatesService
{
    public function __construct(
        private ApiService $apiService
    ) {}

    /**
     * Get and process latest rates.
     */
    public function getLatestRates(): array
    {
        $rates = $this->apiService->get('rates/fx-rates');
        $oeRates = $this->apiService->get('rates/oe-rates/raw');

        if (empty($rates) || empty($oeRates)) {
            return [];
        }

        return $this->processRates($rates, $oeRates);
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
