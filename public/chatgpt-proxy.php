<?php

// Disable error reporting
error_reporting(0);
ini_set('display_errors', 0);

// Allow CORS
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    die(json_encode(['error' => 'Method not allowed']));
}

// Get POST data
$userMessage = $_POST['message'] ?? '';
if (empty($userMessage)) {
    http_response_code(400);
    die(json_encode(['error' => 'Pesan tidak boleh kosong']));
}

// API Key
$apiKey = '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';

try {
    $ch = curl_init('https://api.openai.com/v1/chat/completions');
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

    $response = curl_exec($ch);
    $error = curl_error($ch);
    curl_close($ch);

    if ($error) {
        throw new Exception('Error koneksi: ' . $error);
    }

    $result = json_decode($response, true);

    if (isset($result['error'])) {
        throw new Exception($result['error']['message'] ?? 'Error API');
    }

    if (!isset($result['choices'][0]['message']['content'])) {
        throw new Exception('Format response tidak valid');
    }

    echo json_encode([
        'success' => true,
        'message' => $result['choices'][0]['message']['content']
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
} 