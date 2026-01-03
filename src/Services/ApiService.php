<?php

namespace ZPC\ApiEnds\Services;

use Exception;
use ZPC\ApiEnds\Utils\NetworkUtil;

/**
 * Api Service
 * 
 * Performance-optimized, strictly-typed API client for V2 endpoints.
 */
final class ApiService
{
    private ?string $baseUrl = null;
    private ?int $cacheTtl = null;

    /**
     * Get the base URL, reading from option if not cached.
     */
    private function getBaseUrl(): string
    {
        if ($this->baseUrl === null) {
            $default = defined('ZIMAPI_BASE') ? ZIMAPI_BASE : '';
            $this->baseUrl = get_option('zpc_api_base_url', $default);
        }
        return $this->baseUrl;
    }

    /**
     * Get cache TTL.
     */
    private function getCacheTtl(): int
    {
        if ($this->cacheTtl === null) {
            $this->cacheTtl = (int) get_option('zpc_cache_duration', 3600);
        }
        return $this->cacheTtl;
    }

    /**
     * Perform a GET request with caching.
     */
    public function get(string $endpoint, array $params = [], bool $useCache = true): array
    {
        $url = $this->buildUrl($endpoint, $params);
        $cacheKey = 'zpc_api_' . md5($url);

        error_log('[ApiService::get] Endpoint: ' . $endpoint);
        error_log('[ApiService::get] Base URL: ' . $this->getBaseUrl());
        error_log('[ApiService::get] Full URL: ' . $url);

        if ($useCache && ($cached = get_transient($cacheKey)) !== false) {
            error_log('[ApiService::get] Returning cached data for: ' . $endpoint);
            return $cached;
        }

        $response = wp_remote_get($url, [
            'timeout' => 10,
            'headers' => [
                'Accept' => 'application/json',
                'X-Remote-IP' => NetworkUtil::getRemoteIp(),
            ],
        ]);

        error_log('[ApiService::get] wp_remote_get response type: ' . gettype($response));
        if (is_wp_error($response)) {
            error_log('[ApiService::get] WP Error: ' . $response->get_error_message());
        } else {
            error_log('[ApiService::get] Response code: ' . wp_remote_retrieve_response_code($response));
        }

        $data = $this->parseResponse($response);

        if ($useCache && !empty($data)) {
            set_transient($cacheKey, $data, $this->getCacheTtl());
        }

        return $data;
    }

    /**
     * Build the full URL with query parameters.
     */
    private function buildUrl(string $endpoint, array $params): string
    {
        $url = rtrim($this->getBaseUrl(), '/') . '/' . ltrim($endpoint, '/');
        
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
