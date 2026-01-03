<?php

namespace ZPC\ApiEnds\Services;

class TelecomService {
    public function __construct(private ApiService $api) {}

    /**
     * Get NetOne Bundles.
     *
     * @return array
     */
    public function getNetOneBundles(): array {
        $prices = $this->api->get('/prices/mnos/bundles/netone');
        $rates = $this->api->get('/rates/fx-rates');

        return [
            'prices' => $prices,
            'rates' => $rates
        ];
    }

    /**
     * Get Econet Bundles.
     *
     * @return array
     */
    public function getEconetBundles(): array {
        $prices = $this->api->get('/prices/mnos/bundles/econet');
        $rates = $this->api->get('/rates/fx-rates');

        return [
            'prices' => $prices,
            'rates' => $rates
        ];
    }

    /**
     * Get Telecel Bundles.
     *
     * @return array
     */
    public function getTelecelBundles(): array {
        $prices = $this->api->get('/prices/mnos/bundles/telecel');
        $rates = $this->api->get('/rates/fx-rates');

        return [
            'prices' => $prices,
            'rates' => $rates
        ];
    }
}
