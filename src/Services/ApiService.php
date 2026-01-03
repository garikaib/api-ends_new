<?php

namespace ZPC\ApiEnds\Services;

use Exception;
use ZPC\ApiEnds\Utils\NetworkUtil;

/**
 * Api Service
 * 
 * Performance-optimized, strictly-typed API client for V2 endpoints.
 */
final readonly class ApiService
{
    private string $baseUrl;
    private int $cacheTtl;

    public function __construct()
    {
        $this->baseUrl = get_option('zimapi_base_url', '');
        $this->cacheTtl = (int) get_option('zimapi_cache_duration', 3600);
    }

    /**
     * Perform a GET request with caching.
     */
    public function get(string $endpoint, array $params = [], bool $useCache = true): array
    {
        $url = $this->buildUrl($endpoint, $params);
        $cacheKey = 'zpc_api_' . md5($url);

        if ($useCache && ($cached = get_transient($cacheKey)) !== false) {
            return $cached;
        }

        $response = wp_remote_get($url, [
            'timeout' => 10,
            'headers' => [
                'Accept' => 'application/json',
                'X-Remote-IP' => NetworkUtil::getRemoteIp(),
            ],
        ]);

        $data = $this->parseResponse($response);

        if ($useCache && !empty($data)) {
            set_transient($cacheKey, $data, $this->cacheTtl);
        }

        return $data;
    }

    /**
     * Build the full URL with query parameters.
     */
    private function buildUrl(string $endpoint, array $params): string
    {
        $url = rtrim($this->baseUrl, '/') . '/' . ltrim($endpoint, '/');
        
        if (!empty($params)) {
            $url = add_query_arg($params, $url);
        }

        return $url;
    }

    /**
     * Parse and validate the WordPress remote response.
     */
    private function parseResponse(mixed $response): array
    {
        if (is_wp_error($response)) {
            error_log('ZPC API Error: ' . $response->get_error_message());
            return [];
        }

        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            error_log('ZPC API JSON Error: ' . json_last_error_msg());
            return [];
        }

        return is_array($data) ? $data : [];
    }
}
