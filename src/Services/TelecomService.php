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
        // V2: /api/v2/prices/mnos/bundles/netone
        $response = $this->api->get('v2/prices/mnos/bundles/netone');
        $rates = $this->api->get('v2/rates/fx-rates');

        $prices = $this->mapV2BundlesToLegacy($response);

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
        // V2: /api/v2/prices/mnos/bundles/econet
        $response = $this->api->get('v2/prices/mnos/bundles/econet');
        $rates = $this->api->get('v2/rates/fx-rates');

        $prices = $this->mapV2BundlesToLegacy($response);

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
        // V2: /api/v2/prices/mnos/bundles/telecel
        $response = $this->api->get('v2/prices/mnos/bundles/telecel');
        $rates = $this->api->get('v2/rates/fx-rates');

        $prices = $this->mapV2BundlesToLegacy($response);

        return [
            'prices' => $prices,
            'rates' => $rates
        ];
    }

    private function mapV2BundlesToLegacy(array $response): array {
        // Map V2 'data.bundles' to Legacy 'prices.bundles'
        // V2 structure: { success: true, data: { mno: '...', bundles: [ ... ] } }
        
        $bundles = [];
        if (!empty($response['data']['bundles'])) {
            $bundles = $response['data']['bundles'];
        } elseif (!empty($response['prices']['bundles'])) {
             // Fallback if V2 structure varies
             $bundles = $response['prices']['bundles'];
        }

        // Map fields
        $legacy_bundles = [];
        foreach ($bundles as $item) {
            $mapped_item = $item;
            $mapped_item['package_name'] = $item['name'] ?? $item['package_name'] ?? '';
            $mapped_item['zwl_price'] = $item['price_zig'] ?? $item['zwl_price'] ?? 0;
            $mapped_item['usd_price'] = $item['price_usd'] ?? 0;
            // Legacy expects 'description' for filtering (e.g. 'data', 'voice')
            // V2 might not map this 1:1, assume 'type' or similar?
            // If V2 item has 'description', fine. If not, we might need logic.
            // For now preserve whatever is there.
            $legacy_bundles[] = $mapped_item;
        }

        return ['prices' => ['bundles' => $legacy_bundles]];
    }
}
