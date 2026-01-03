<?php

namespace ZPC\ApiEnds\Services;

class ZesaService {
    public function __construct(private ApiService $api) {}

    /**
     * Get Latest ZESA Tariffs.
     *
     * @return array
     */
    public function getTariffs(): array {
        // V2 API endpoints
        $prices = $this->api->get('v2/prices/zesa');
        $rates = $this->api->get('v2/rates/fx-rates');

        return [
            'prices' => $prices,
            'rates' => $rates
        ];
    }

    /**
     * Calculate electricity cost for given units using stepped bands.
     * Ported from zesa-tariff-calculator.php
     *
     * @param array $prices
     * @param int $units
     * @return float
     */
    public function calculateCost(array $prices, int $units): float {
        // Handle various array structures (legacy compat)
        $bands = $prices['prices']['bands'] ?? ($prices['bands'] ?? ($prices[0]['description'] ? $prices : []));

        if (empty($bands)) {
            return 0.0;
        }

        usort($bands, fn($a, $b) => $a['min_units'] - $b['min_units']);

        $total_cost = 0;
        foreach ($bands as $band) {
            $min_units = $band['min_units'] ?? 0;
            $max_units = $band['max_units'] ?? 0;
            $price = $band['zig_price_rea'] ?? $band['zig_price'] ?? 0;

            $band_range = $max_units - $min_units;
            $band_units = ($units <= $band_range) ? $units : $band_range;

            $total_cost += $band_units * $price;
            $units -= $band_units;

            if ($units <= 0) {
                break;
            }
        }
        return $total_cost;
    }

    /**
     * Get the highest ZiG price (REA) from bands.
     * Ported from zesa-highest-zig.php
     *
     * @param array $data
     * @return float
     */
    public function getHighestZigPriceRea(array $data): float {
        $highest = 0.0;
        $bands = $data['prices']['bands'] ?? []; // Assume standard structure for this method
        
        foreach ($bands as $band) {
            if (isset($band['zig_price_rea']) && $band['zig_price_rea'] > $highest) {
                $highest = (float)$band['zig_price_rea'];
            }
        }
        return $highest;
    }

    /**
     * Calculate cost of the "Cheap" quota (all bands except the last expensive one).
     *
     * @param array $data
     * @param bool $round
     * @return float
     */
    public function getCheapQuotaCost(array $data, bool $round = false): float {
        $bands = $data['prices']['bands'] ?? [];
        if (empty($bands)) return 0.0;

        usort($bands, fn($a, $b) => $a['min_units'] - $b['min_units']);
        $total_cost = 0;
        $count = 0;
        $band_count = count($bands);

        foreach ($bands as $band) {
            // Sum all except the last band
            if ($count < ($band_count - 1)) {
                $units = ($band['max_units'] ?? 0) - ($band['min_units'] ?? 0);
                $total_cost += $units * ($band['zig_price_rea'] ?? $band['zig_price'] ?? 0);
                $count++;
            }
        }

        return $round ? (ceil($total_cost / 100) * 100) : $total_cost;
    }

    /**
     * Get totals for bands (cumulative units and costs).
     *
     * @param array $data
     * @return array
     */
    public function getBandTotals(array $data): array {
        $bands = $data['prices']['bands'] ?? [];
        if (empty($bands)) return [];

        usort($bands, fn($a, $b) => ($a['min_units'] ?? 0) - ($b['min_units'] ?? 0));
        $band_totals = [];
        $cumulative_total = 0;

        // Iterate over all but the last band
        for ($i = 0; $i < count($bands) - 1; $i++) {
            $band = $bands[$i];
            $total_units = ($band['max_units'] ?? 0) - ($band['min_units'] ?? 0);
            $cumulative_total += $total_units;

            $band_totals[$i] = [
                'total_units' => $total_units,
                'cumulative_total' => $cumulative_total,
                'zig_price_rea' => $band['zig_price_rea'] ?? $band['zig_price'] ?? 0,
            ];
        }

        return $band_totals;
    }

    /**
     * Calculate REA factor.
     * Ported from templates/zesa-explanation.php logic
     *
     * @param array $data
     * @return float
     */
    public function getReaFactor(array $data): float {
        $bands = $data['prices']['bands'] ?? [];
        if (empty($bands)) return 0.0;

        $min_rea = PHP_FLOAT_MAX;
        foreach ($bands as $band) {
            if (empty($band['zig_price'])) continue;
            $zig_rea = $band['zig_price_rea'] ?? $band['zig_price'] ?? 0;
            if ($zig_rea == 0) continue;
            $rea = $zig_rea / $band['zig_price'];
            if ($rea < $min_rea) {
                $min_rea = $rea;
            }
        }
        return ($min_rea === PHP_FLOAT_MAX) ? 0.0 : $min_rea;
    }
}
