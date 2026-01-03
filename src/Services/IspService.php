<?php

namespace ZPC\ApiEnds\Services;

/**
 * ISP Service
 * 
 * Fetches internet package prices for various providers (Liquid Home, TelOne, Utande).
 */
final readonly class IspService
{
    public function __construct(
        private ApiService $apiService
    ) {}

    /**
     * Get data for Liquid Home display.
     */
    public function getLiquidHome(): array
    {
        $prices = $this->apiService->get('prices/isp/liquid-home');
        $rates = $this->apiService->get('rates/fx-rates');

        if (empty($prices) || empty($rates)) {
            return [];
        }

        return [
            'prices' => $prices, // Legacy expects $prices_data
            'rates' => $rates,
        ];
    }

    /**
     * Get data for TelOne display.
     */
    public function getTelOne(): array
    {
        $prices = $this->apiService->get('prices/isp/telone');
        $rates = $this->apiService->get('rates/fx-rates');

        if (empty($prices) || empty($rates)) {
            return [];
        }

        return [
            'prices' => $prices,
            'rates' => $rates,
        ];
    }

    /**
     * Get data for Utande display.
     */
    public function getUtande(): array
    {
        $prices = $this->apiService->get('prices/isp/utande');
        $rates = $this->apiService->get('rates/fx-rates');

        if (empty($prices) || empty($rates)) {
            return [];
        }

        return [
            'prices' => $prices,
            'rates' => $rates,
        ];
    }
}
