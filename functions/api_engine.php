<?php
/**
 * Moonlight Digital - API Engine V4 (Production)
 * Upgraded to support JWT Bearer Tokens for secure User Portal queries.
 */

function call_moonlight_api($endpoint, $method = 'POST', $data = [], $jwt_token = null) {
    $url = get_url('api', $endpoint);
    $ch = curl_init($url);
    
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($method));
    
    // Ignore SSL verification for internal server-to-server chatter
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    
    // Setup Headers
    $headers = ['Content-Type: application/json'];
    if ($jwt_token) {
        $headers[] = 'Authorization: Bearer ' . $jwt_token;
    }
    
    if (strtoupper($method) !== 'GET') {
        $is_multipart = false;
        foreach ($data as $key => $value) {
            if ($value instanceof CURLFile) {
                $is_multipart = true;
                break;
            }
        }

        if ($is_multipart) {
            // Remove Content-Type so cURL auto-sets multipart boundary
            $headers = array_filter($headers, function($h) { return strpos($h, 'Content-Type') === false; });
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        } else {
            $payload = json_encode($data);
            $headers[] = 'Content-Length: ' . strlen($payload);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        }
    }

    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
    if (curl_errno($ch)) {
        $error_msg = curl_error($ch);
        curl_close($ch);
        return ['status_code' => 500, 'body' => ['status' => 'error', 'message' => 'cURL Network Fault: ' . $error_msg]];
    }
    
    curl_close($ch);

    $decoded = json_decode($response, true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        $clean_raw = substr(trim(strip_tags($response)), 0, 60);
        $safe_error = empty($clean_raw) ? "Empty response from server." : $clean_raw;
        return [
            'status_code' => 500,
            'body' => ['status' => 'error', 'message' => 'Server Error [' . $http_code . ']: ' . $safe_error]
        ];
    }

    return ['status_code' => $http_code, 'body' => $decoded];
}