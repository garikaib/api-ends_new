<?php

class ZIMAPI
{
    private $callSuccess = false;
    private $errorMessage = null;
    private $apiBase;

    public function __construct(string $apiBase)
    {
        $this->apiBase = $apiBase;
    }

    public function callApi(string $url, string $remoteIP = '0.0.0.0', string $day = null, string $httpMethod = 'GET'): ?array
    {
        $payload = [];
        $payload["remote_ip"] = $remoteIP;

        if ($day !== null) {
            $payload["day"] = $day;
        }

        $result = $this->runHttpRequest($url, $payload, $httpMethod);

        if (!is_null($result) && $result['success'] == true) {
            return $result;
        }

        throw new Exception("API call failed: " . $this->errorMessage);
    }

    private function runHttpRequest(string $endpoint, array $payload = [], string $httpMethod = 'POST'): ?array
    {
        $url = $this->apiBase . $endpoint;

        $headers = [
            'ContentType' => 'application/json',
        ];

        $response = wp_remote_post($url, [
            'method' => $httpMethod,
            'timeout' => 5,
            'redirection' => 5,
            'blocking' => true,
            'headers' => $headers,
            'body' => $payload,
        ]);

        if (!is_wp_error($response)) {
            $data = wp_remote_retrieve_body($response);

            if (!is_wp_error($data) && $data = json_decode($data, true)) {
                return $data;
            } else {
                $this->errorMessage = true;
            }
        } else {
            $this->errorMessage = $response->get_error_message();
        }

        return null;
    }

    public function multiCallApi(array $endpoints, string $remoteIP = '0.0.0.0', string $day = null, string $httpMethod = 'GET'): array
    {
        $requests = [];
        // error_log(print_r($endpoints,true));
        foreach ($endpoints as $key => $endpoint) {
            $url = $endpoint['endpoint'] ?? '';
            $method = $endpoint['method'] ?? $httpMethod;
            $payload = $endpoint['payload'] ?? [];
            $payload['remote_ip'] = $remoteIP;

            if ($day !== null) {
                $payload['day'] = $day;
            }

            $requests[$key] = [
                'url' => $this->apiBase . $url,
                'type' => strtoupper($method),
                'headers' => [
                    'ContentType' => 'application/json',
                ],
                'data' => $payload,
            ];
        }

        $responses = WpOrg\Requests\Requests::request_multiple($requests);

        $formattedResults = [];
        foreach ($responses as $endpoint => $response) {
            if ($response instanceof WpOrg\Requests\Response) {
                $data = json_decode($response->body, true);
                $formattedResults[$endpoint] = [
                    'success' => true,
                    'data' => $data,
                ];
            } elseif ($response instanceof WpOrg\Requests\Exception) {
                $formattedResults[$endpoint] = [
                    'success' => false,
                    'error' => $response->getMessage(),
                ];
            } else {
                $formattedResults[$endpoint] = [
                    'success' => false,
                    'error' => 'Unexpected response',
                ];
            }
        }

        return $formattedResults;
    }
}
