<?php

namespace ZPC\ApiEnds\Services;

class TransportService {
    public function __construct(private ApiService $api) {}

    /**
     * Get Tollgate Prices.
     *
     * @return array
     */
    public function getTollgates(): array {
        $prices = $this->api->get('/prices/zinara/tollgates');
        $rates = $this->api->get('/rates/fx-rates');

        return [
            'prices' => $prices,
            'rates' => $rates
        ];
    }

    /**
     * Get Zinara License Fees.
     *
     * @return array
     */
    public function getZinaraLicense(): array {
        $prices = $this->api->get('/prices/zinara/fees');
        $rates = $this->api->get('/rates/fx-rates');

        return [
            'prices' => $prices,
            'rates' => $rates
        ];
    }

    /**
     * Get ZUPCO Fares.
     *
     * @return array
     */
    public function getZupcoFares(): array {
        $prices = $this->api->get('/prices/fares/zupco');
        error_log('[TransportService] ZUPCO raw: ' . print_r($prices, true));
        $rates = $this->api->get('/rates/fx-rates');

        return [
            'prices' => $prices,
            'rates' => $rates
        ];
    }

    /**
     * Get Bus Fares.
     *
     * @return array
     */
    public function getBusFares(): array {
        $prices = $this->api->get('/prices/fares/busfares');
        error_log('[TransportService] Bus Fares raw: ' . print_r($prices, true));
        $rates = $this->api->get('/rates/fx-rates');

        return [
            'prices' => $prices,
            'rates' => $rates
        ];
    }
}
