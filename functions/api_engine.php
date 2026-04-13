<?php
/**
 * Moonlight Digital - API Engine V1.2
 * A secure cURL wrapper that acts as the bridge between your Frontend Portals and your API.
 * Supports standard JSON payloads as well as Multipart Form Data for Image Uploads.
 */

function call_moonlight_api($endpoint, $method = 'POST', $data = []) {
    // 1. Generate the absolute URL for the API endpoint using our global function
    $url = get_url('api', $endpoint);
    
    $ch = curl_init($url);
    
    // 2. Configure standard cURL options
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($method));
    
    // 3. Process the payload if it's not a GET request
    if (strtoupper($method) !== 'GET') {
        
        // Check if the data array contains a physical file (CURLFile object)
        $is_multipart = false;
        foreach ($data as $key => $value) {
            if ($value instanceof CURLFile) {
                $is_multipart = true;
                break;
            }
        }

        if ($is_multipart) {
            // For file uploads (like checkout screenshots), let cURL handle the multipart headers automatically
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        } else {
            // For standard data (like Login/Register), convert the array to JSON
            $payload = json_encode($data);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Content-Length: ' . strlen($payload)
            ]);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        }
    }

    // 4. Execute the request and capture the response
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
    // 5. Handle cURL errors (e.g., connection refused if API port is wrong)
    if (curl_errno($ch)) {
        $error_msg = curl_error($ch);
        curl_close($ch);
        return [
            'status_code' => 500,
            'body' => [
                'status' => 'error',
                'message' => 'Internal Server Connection Error: ' . $error_msg
            ]
        ];
    }
    
    curl_close($ch);

    // 6. Return formatted array
    return [
        'status_code' => $http_code,
        'body' => json_decode($response, true)
    ];
}
?>