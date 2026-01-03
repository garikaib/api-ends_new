<?php

namespace ZPC\ApiEnds\Services;

class GovtService {
    public function __construct(private ApiService $api) {}

    /**
     * Get Passport Fees.
     *
     * @return array
     */
    public function getPassportFees(): array {
        return $this->fetchFees('/fees/passport');
    }

    /**
     * Get Birth and Death Registration Fees.
     *
     * @return array
     */
    public function getBirthDeathFees(): array {
        return $this->fetchFees('/fees/births-deaths');
    }

    /**
     * Get Citizen Status Fees.
     *
     * @return array
     */
    public function getCitizenStatusFees(): array {
        return $this->fetchFees('/fees/citizen-status');
    }

    /**
     * Helper to fetch fees and rates in parallel (simulated or sequential relies on ApiService cache).
     * Actually, we need both fees endpoint and rates endpoint.
     * 
     * @param string $endpoint
     * @return array
     */
    private function fetchFees(string $endpoint): array {
        // We can use the ApiService to get data. 
        // We also need rates.
        // Let's do a multi-call if possible, or just sequential since we have caching.
        // ApiService doesn't expose multiCall directly yet but uses ZIMAPI internally?
        // Actually ApiService wrapper is simple. Let's just use it twice.
        // Wait, the legacy code uses `multiCallApi` implicitly or explicitly?
        // Legacy: $fees->callApi($endPoint...); $fees->callApi('/rates/fx-rates'...)
        // It calls them sequentially.
        
        $fees = $this->api->get($endpoint);
        $rates = $this->api->get('/rates/fx-rates');

        return [
            'fees' => $fees,
            'rates' => $rates
        ];
    }
}
