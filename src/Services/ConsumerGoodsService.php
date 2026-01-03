<?php

namespace ZPC\ApiEnds\Services;

class ConsumerGoodsService {
    public function __construct(private ApiService $api) {}

    /**
     * Get Delta Alcohol Prices.
     *
     * @return array
     */
    public function getDeltaAlcohol(): array {
        $prices = $this->api->get('/prices/drinks/deltaa');
        $rates = $this->api->get('/rates/fx-rates');

        return [
            'prices' => $prices,
            'rates' => $rates
        ];
    }

    // Future methods: getMeatPrices(), etc.
}
