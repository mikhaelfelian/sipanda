<?php

// API Keys for X.com
$apiKey = '680KT6hN6RTK8mzzLaKCOOiYE';
$apiSecret = 'IzF6Vojb7oZmVHui4R2y64xN46DfbMLHSkHKNfIE8ErquXmWTQ';
$username = 'jokowi'; // The username to test

// Create credentials string and encode
$credentials = base64_encode($apiKey . ':' . $apiSecret);

// Step 1: Get bearer token
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://api.twitter.com/oauth2/token');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, 'grant_type=client_credentials');
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Basic ' . $credentials,
    'Content-Type: application/x-www-form-urlencoded;charset=UTF-8'
]);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Only for testing

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "STEP 1: Get Bearer Token\n";
echo "HTTP Code: " . $httpCode . "\n";
echo "Response: " . $response . "\n\n";

if ($httpCode != 200) {
    echo "ERROR: Failed to get bearer token. API credentials may be invalid or revoked.\n";
    exit;
}

$data = json_decode($response, true);
$bearerToken = $data['access_token'] ?? null;

if (!$bearerToken) {
    echo "ERROR: No bearer token in response.\n";
    exit;
}

echo "Bearer token obtained successfully.\n\n";

// Step 2: Get user profile
$ch = curl_init();
$url = "https://api.twitter.com/2/users/by/username/{$username}?user.fields=description,created_at,location,profile_image_url,public_metrics,url,verified";
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $bearerToken,
    'Content-Type: application/json'
]);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Only for testing

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "STEP 2: Get User Profile for @{$username}\n";
echo "HTTP Code: " . $httpCode . "\n";
echo "Response: " . $response . "\n\n";

if ($httpCode != 200) {
    echo "ERROR: Failed to get user profile. Response code indicates an error.\n";
    if ($httpCode == 401) {
        echo "Authentication error - bearer token may be invalid or expired.\n";
    } elseif ($httpCode == 404) {
        echo "User not found - the username may not exist or be available.\n";
    } elseif ($httpCode == 429) {
        echo "Rate limit exceeded - too many requests.\n";
    }
    exit;
}

$data = json_decode($response, true);
if (isset($data['data'])) {
    echo "SUCCESS: User profile retrieved successfully.\n";
    echo "Username: " . $data['data']['username'] . "\n";
    echo "Display Name: " . $data['data']['name'] . "\n";
    echo "Description: " . ($data['data']['description'] ?? 'N/A') . "\n";
    echo "Followers: " . ($data['data']['public_metrics']['followers_count'] ?? 'N/A') . "\n";
} else {
    echo "ERROR: User data not found in response.\n";
} 