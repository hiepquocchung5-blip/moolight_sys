<?php
/**
 * Moonlight Digital - API Engine V3 (X-Ray Debugger)
 * Upgraded for strict HTTPS and Nginx Error capturing.
 */

function call_moonlight_api($endpoint, $method = 'POST', $data = []) {
    $url = get_url('api', $endpoint);
    $ch = curl_init($url);
    
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($method));
    
    // Ignore SSL verification for internal server-to-server chatter
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
    
    if (curl_errno($ch)) {
        $error_msg = curl_error($ch);
        curl_close($ch);
        return ['status_code' => 500, 'body' => ['status' => 'error', 'message' => 'cURL Network Fault: ' . $error_msg]];
    }
    
    curl_close($ch);

    $decoded = json_decode($response, true);
    
    // X-RAY DEBUGGER: If the response is not valid JSON (e.g., an HTML 404 page)
    if (json_last_error() !== JSON_ERROR_NONE) {
        // Strip out the HTML tags and grab the first 60 characters to show in the error URL
        $clean_raw = substr(trim(strip_tags($response)), 0, 60);
        $safe_error = empty($clean_raw) ? "Empty response from server." : $clean_raw;
        
        return [
            'status_code' => 500,
            'body' => [
                'status' => 'error', 
                'message' => 'Server Error [' . $http_code . ']: ' . $safe_error
            ]
        ];
    }

    return [
        'status_code' => $http_code,
        'body' => $decoded
    ];
}