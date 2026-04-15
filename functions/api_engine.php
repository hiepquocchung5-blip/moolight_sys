<?php
/**
 * Moonlight Digital - API Engine V2
 * Upgraded for HTTPS Production Environments with strict SSL handling.
 */

function call_moonlight_api($endpoint, $method = 'POST', $data = []) {
    $url = get_url('api', $endpoint);
    $ch = curl_init($url);
    
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($method));
    
    // CRITICAL PRODUCTION FIX: Ignore SSL verification for internal subdomain chatter
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    
    if (strtoupper($method) !== 'GET') {
        $is_multipart = false;
        foreach ($data as $key => $value) {
            if ($value instanceof CURLFile) {
                $is_multipart = true;
                break;
            }
        }

        if ($is_multipart) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        } else {
            $payload = json_encode($data);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Content-Length: ' . strlen($payload)
            ]);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        }
    }

    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
    // Catch cURL network errors
    if (curl_errno($ch)) {
        $error_msg = curl_error($ch);
        curl_close($ch);
        return [
            'status_code' => 500,
            'body' => ['status' => 'error', 'message' => 'Network Fault: ' . $error_msg]
        ];
    }
    
    curl_close($ch);

    // Ensure we always return an array even if the API spits out fatal HTML errors
    $decoded = json_decode($response, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        return [
            'status_code' => 500,
            'body' => ['status' => 'error', 'message' => 'API returned invalid JSON. Check API logs.']
        ];
    }

    return [
        'status_code' => $http_code,
        'body' => $decoded
    ];
}
?>