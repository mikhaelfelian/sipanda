<?php
// Disable error reporting
error_reporting(0);
ini_set('display_errors', 0);

// Set headers
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');

// API Key
$apiKey = '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';

// Check if this is a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die(json_encode(['error' => 'Method not allowed']));
}

// Get the message
$userMessage = isset($_POST['message']) ? trim($_POST['message']) : '';
if (empty($userMessage)) {
    die(json_encode(['error' => 'Pesan tidak boleh kosong']));
}

// Initialize cURL
$ch = curl_init('https://api.openai.com/v1/chat/completions');

// Set cURL options
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_SSL_VERIFYHOST => 0,
    CURLOPT_HTTPHEADER => [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $apiKey
    ],
    CURLOPT_POSTFIELDS => json_encode([
        'model' => 'gpt-3.5-turbo',
        'messages' => [
            [
                'role' => 'system',
                'content' => 'Kamu adalah asisten yang membantu dalam bahasa Indonesia. Berikan jawaban yang sopan dan formal.'
            ],
            ['role' => 'user', 'content' => $userMessage]
        ],
        'temperature' => 0.7,
        'max_tokens' => 150
    ])
]);

// Execute cURL request
$response = curl_exec($ch);
$error = curl_error($ch);
curl_close($ch);

// Handle errors
if ($error) {
    die(json_encode([
        'success' => false,
        'message' => 'Error koneksi: ' . $error
    ]));
}

// Parse response
$result = json_decode($response, true);

if (isset($result['error'])) {
    die(json_encode([
        'success' => false,
        'message' => $result['error']['message'] ?? 'Error API'
    ]));
}

if (!isset($result['choices'][0]['message']['content'])) {
    die(json_encode([
        'success' => false,
        'message' => 'Format response tidak valid'
    ]));
}

// Return success response
echo json_encode([
    'success' => true,
    'message' => $result['choices'][0]['message']['content']
]); 