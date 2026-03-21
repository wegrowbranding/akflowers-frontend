<?php

class ApiClient {
    public static function request(string $method, string $endpoint, array $data = [], bool $auth = false): array {
        $url     = API_BASE . $endpoint;
        $headers = ['Content-Type: application/json', 'Accept: application/json'];

        if ($auth) {
            $headers[] = 'Authorization: Bearer ' . token();
        }

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST  => strtoupper($method),
            CURLOPT_HTTPHEADER     => $headers,
            CURLOPT_TIMEOUT        => 15,
        ]);

        if (!empty($data)) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $decoded = json_decode($response, true) ?? [];
        $decoded['_http_code'] = $httpCode;
        return $decoded;
    }

    public static function get(string $endpoint, array $query = [], bool $auth = false): array {
        $url = $endpoint . (!empty($query) ? '?' . http_build_query($query) : '');
        return self::request('GET', $url, [], $auth);
    }

    public static function post(string $endpoint, array $data = [], bool $auth = false): array {
        return self::request('POST', $endpoint, $data, $auth);
    }

    public static function put(string $endpoint, array $data = [], bool $auth = false): array {
        return self::request('PUT', $endpoint, $data, $auth);
    }

    public static function delete(string $endpoint, bool $auth = false): array {
        return self::request('DELETE', $endpoint, [], $auth);
    }
}
