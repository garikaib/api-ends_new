<?php

namespace ZPC\ApiEnds\Services;

class FinesService {
    public function __construct(private ApiService $api) {}

    /**
     * Get Fine Levels (all fines and rates).
     *
     * @return array
     */
    public function getFineLevels(): array {
        $fines = $this->api->get('/stats/fines/all');
        $rates = $this->api->get('/rates/fx-rates');

        return [
            'fines' => $fines,
            'rates' => $rates
        ];
    }

    /**
     * Get Traffic Fines.
     *
     * @return array
     */
    public function getTrafficFines(): array {
        return $this->api->get('/prices/govt/traffic-fines');
    }
}
