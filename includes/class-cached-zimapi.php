<?php

/**
 * Class CachedZIMAPI
 *
 * Extends ZIMAPI to add caching capabilities using WordPress transients.
 */
class CachedZIMAPI extends ZIMAPI
{
    /**
     * @var CachedZIMAPI|null The singleton instance.
     */
    private static $default_instance = null;

    /**
     * Get the default CachedZIMAPI instance.
     *
     * @return CachedZIMAPI
     */
    public static function get_default_client(): self
    {
        if (self::$default_instance === null) {
            $base_url = defined('ZIMAPI_BASE') ? ZIMAPI_BASE : 'https://a.garikai.xyz/api';
            self::$default_instance = new self($base_url);
        }
        return self::$default_instance;
    }

    /**
     * @var int Cache duration in seconds. Default is 1 hour.
     */
    private $cacheDuration;

    /**
     * Constructor.
     *
     * @param string $apiBase The base URL for the API.
     * @param int $cacheDuration Cache duration in seconds.
     */
    public function __construct(string $apiBase, int $cacheDuration = 3600)
    {
        parent::__construct($apiBase);
        $this->cacheDuration = $cacheDuration;
    }

    /**
     * Calls the API with caching.
     *
     * @param string $url The API endpoint.
     * @param string $remoteIP The remote IP address.
     * @param string|null $day The day parameter.
     * @param string $httpMethod The HTTP method.
     * @return array|null The API response or null on failure.
     * @throws Exception
     */
    public function callApi(string $url, string $remoteIP = '0.0.0.0', string $day = null, string $httpMethod = 'GET'): ?array
    {
        // Get cache duration from settings, fallback to default
        $settingsDuration = carbon_get_theme_option('api_cache_duration');
        $duration = !empty($settingsDuration) ? (int)$settingsDuration : $this->cacheDuration;

        // Get cache version
        $version = get_option('zimapi_cache_version', 1);

        // Generate a unique cache key with version (no client IP!)
        $cacheKey = 'zimapi_v' . $version . '_' . md5($url . '_' . $day . '_' . $httpMethod);

        // Check for cached response
        $cachedResponse = get_transient($cacheKey);
        if ($cachedResponse !== false) {
            return $cachedResponse;
        }

        // Call the parent method
        try {
            $response = parent::callApi($url, $remoteIP, $day, $httpMethod);
            
            // Cache the successful response
            if ($response !== null) {
                set_transient($cacheKey, $response, $duration);
            }

            return $response;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Makes multiple API calls with caching.
     *
     * @param array $endpoints List of endpoints and their configurations.
     * @param string $remoteIP The remote IP address.
     * @param string|null $day The day parameter.
     * @param string $httpMethod The HTTP method.
     * @return array The combined results.
     */
    public function multiCallApi(array $endpoints, string $remoteIP = '0.0.0.0', string $day = null, string $httpMethod = 'GET'): array
    {
        $results = [];
        $endpointsToFetch = [];

        // Get cache duration from settings, fallback to default
        $settingsDuration = carbon_get_theme_option('api_cache_duration');
        $duration = !empty($settingsDuration) ? (int)$settingsDuration : $this->cacheDuration;

        // Get cache version
        $version = get_option('zimapi_cache_version', 1);

        // Check cache for each endpoint
        foreach ($endpoints as $key => $endpoint) {
            $url = $endpoint['endpoint'] ?? '';
            $method = $endpoint['method'] ?? $httpMethod;
            $payload = $endpoint['payload'] ?? [];
            
            // Create a cache key specific to this endpoint request with version (no client IP!)
            $cacheKey = 'zimapi_multi_v' . $version . '_' . md5($url . serialize($payload) . '_' . $day . '_' . $method);
            
            $cachedResponse = get_transient($cacheKey);
            if ($cachedResponse !== false) {
                $results[$key] = $cachedResponse;
            } else {
                $endpointsToFetch[$key] = $endpoint;
            }
        }

        // Fetch non-cached endpoints
        if (!empty($endpointsToFetch)) {
            $fetchedResults = parent::multiCallApi($endpointsToFetch, $remoteIP, $day, $httpMethod);

            foreach ($fetchedResults as $key => $result) {
                $results[$key] = $result;

                // Cache successful results (only cache if the request succeeded AND the API response internal success is true)
                if (isset($result['success']) && $result['success'] === true && !empty($result['data']['success'])) {
                    $url = $endpointsToFetch[$key]['endpoint'] ?? '';
                    $method = $endpointsToFetch[$key]['method'] ?? $httpMethod;
                    $payload = $endpointsToFetch[$key]['payload'] ?? [];
                    $cacheKey = 'zimapi_multi_v' . $version . '_' . md5($url . serialize($payload) . '_' . $day . '_' . $method);
                    
                    set_transient($cacheKey, $result, $duration);
                }
            }
        }

        return $results;
    }
}
