<?php

namespace ZPC\ApiEnds\Services;

/**
 * Fuel Service
 * 
 * Fetches fuel prices and related exchange rates.
 */
final readonly class FuelService
{
    public function __construct(
        private ApiService $apiService
    ) {}

    /**
     * Get data for fuel prices display.
     * Returns structure matching legacy requirements: ['fuel' => ..., 'rates' => ...]
     */
    public function getLatestFuel(): array
    {
        // Use legacy-style concurrent fetching simulation (or sequential since ApiService handles it)
        $fuel = $this->apiService->get('fuel/prices');
        $rates = $this->apiService->get('rates/fx-rates');

        if (empty($fuel) || empty($rates)) {
            return [];
        }

        return [
            'fuel' => ['success' => true, 'data' => $fuel], // Shim to match legacy structure which expected ['data' => ...] wrapper sometimes or direct?
            // Wait, legacy multiCallApi returns ['fuel' => ['success'=>true, 'data'=>...]]
            // My ApiService::get returns the body directly (which is the data).
            // So if ApiService returns the JSON body, and the endpoints return { success: true, prices: ... }
            // Then I need to wrap it if I want to match exactly what the legacy controller expected?
            // Legacy controller: $fuel_data = $data['fuel']['data'];
            // So this method should return ['fuel' => ['data' => $fuelBody], 'rates' => ['data' => $ratesBody]]
            // to minimize controller change? Or just return clean data and let controller adapt?
            // The template expects $fuel_data directly (step 326: $fuel_data = $data['fuel']['data'] in the class).
            // So if I return clean data here, the Controller can pass it directly.
            
            // Let's return clean data here.
            'fuel' => $fuel,
            'rates' => $rates,
        ];
    }
}
