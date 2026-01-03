<?php

namespace ZPC\ApiEnds\Services;

class TransportService {
    public function __construct(private ApiService $api) {}

    /**
     * Get Tollgate Prices.
     *
     * @return array
     */
    /**
     * Get Tollgate Prices.
     *
     * @return array
     */
    public function getTollgates(): array {
        // V2: /api/v2/prices/transport/tollgate
        $prices = $this->api->get('v2/prices/transport/tollgate');
        $rates = $this->api->get('v2/rates/fx-rates');

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
        // V2: /api/v2/prices/transport/zinara
        $prices = $this->api->get('v2/prices/transport/zinara');
        $rates = $this->api->get('v2/rates/fx-rates');

        // Map V2 'prices' to Legacy 'fees' and map fields
        if (!empty($prices['prices']['prices'])) {
            $mapped_fees = [];
            foreach ($prices['prices']['prices'] as $item) {
                $mapped_item = $item;
                $mapped_item['usd_fees'] = $item['usd_price'] ?? 0;
                $mapped_item['zig_fees'] = $item['zig_price'] ?? 0;
                $mapped_fees[] = $mapped_item;
            }
            $prices['prices']['fees'] = $mapped_fees;
        }

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
        // V2: /api/v2/prices/transport/zupco
        $prices = $this->api->get('v2/prices/transport/zupco');
        $rates = $this->api->get('v2/rates/fx-rates');

        // Map V2 'prices' to Legacy 'fares' and map fields
        if (!empty($prices['prices']['prices'])) {
            $mapped_fares = [];
            foreach ($prices['prices']['prices'] as $item) {
                $mapped_item = $item;
                $mapped_item['bus_usd_price'] = $item['usd_price'] ?? 0;
                $mapped_fares[] = $mapped_item;
            }
            $prices['prices']['fares'] = $mapped_fares;
        }

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
        // V2: /api/v2/prices/transport/bus
        $prices = $this->api->get('v2/prices/transport/bus');
        $rates = $this->api->get('v2/rates/fx-rates');

        // Map V2 'prices' to Legacy 'fares' and map fields
        if (!empty($prices['prices']['prices'])) {
            $mapped_fares = [];
            foreach ($prices['prices']['prices'] as $item) {
                $mapped_item = $item;
                $mapped_item['usd_fare'] = $item['usd_price'] ?? 0;
                $mapped_fares[] = $mapped_item;
            }
            $prices['prices']['fares'] = $mapped_fares;
        }

        return [
            'prices' => $prices,
            'rates' => $rates
        ];
    }
}
