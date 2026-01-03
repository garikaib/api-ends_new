<?php

namespace ZPC\ApiEnds\Services;

use ZPC\ApiEnds\Utils\DateUtil;
use ZPC\ApiEnds\Utils\PriceUtil;

/**
 * Historical Rates Service
 * 
 * Manages the creation and retrieval of historical rate posts.
 * PHP 8.2+ Optimized.
 */
final readonly class HistoricalRatesService
{
    public function __construct(
        private ApiService $apiService
    ) {}

    /**
     * Publish a single day's rate post.
     */
    public function publishDate(string $date): int|false
    {
        if (!DateUtil::isValidIso($date)) {
            return false;
        }

        // 1. Fetch data from V2 API
        $rateData = $this->apiService->get('rates/fx-rates', ['day' => $date]);
        
        if (empty($rateData) || !($rateData['success'] ?? false)) {
            return false;
        }

        // 2. Prepare Post Data
        $title = 'Black Market Exchange Rates ' . DateUtil::formatDisplayDate($date);
        $slug = 'rates-' . $date;

        $postData = [
            'post_title'   => $title,
            'post_name'    => $slug,
            'post_status'  => 'publish',
            'post_type'    => 'exchange-rates',
            'post_date'    => $date . ' 12:00:00',
            'post_content' => '<!-- wp:shortcode -->[show-historical-rate date="' . $date . '"]<!-- /wp:shortcode -->',
            'post_author'  => 1,
        ];

        // 3. Insert or Update Post
        $post_id = $this->ensurePost($postData);

        if ($post_id) {
            // 4. Store metadata (DRY & Decoupled)
            update_post_meta($post_id, '_zpc_rate_date', $date);
            update_post_meta($post_id, '_zpc_rate_data', $rateData);
        }

        return $post_id;
    }

    /**
     * Ensure a post exists or create it.
     */
    private function ensurePost(array $postData): int|false
    {
        $existing = get_page_by_path($postData['post_name'], OBJECT, 'exchange-rates');
        
        if ($existing) {
            $postData['ID'] = $existing->ID;
            return wp_update_post($postData);
        }

        return wp_insert_post($postData);
    }
}
