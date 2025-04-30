<?php
// API Key OpenAI Anda
$apiKey = "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";

// Periksa jika ada input dari pengguna
if (isset($_POST['message'])) {
    $userMessage = trim($_POST['message']);
    
    // Endpoint API OpenAI
    $url = "https://api.openai.com/v1/chat/completions";

    // Data permintaan ke API
    $data = [
        "model" => "gpt-3.5-turbo", // Anda bisa ganti ke "gpt-4" jika memiliki akses
        "messages" => [
            ["role" => "system", "content" => "You are a helpful assistant."],
            ["role" => "user", "content" => $userMessage]
        ],
        "max_tokens" => 200,
        "temperature" => 0.7
    ];

    // Inisialisasi cURL
    $ch = curl_init();

    // Set opsi cURL
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json",
        "Authorization: Bearer $apiKey"
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

    // Eksekusi cURL dan ambil respons
    $response = curl_exec($ch);
    curl_close($ch);

    // Decode respons JSON
    $result = json_decode($response, true);

    // Periksa apakah respons API valid
    if (isset($result['choices'][0]['message']['content'])) {
        echo $result['choices'][0]['message']['content'];
    } else {
        echo "Terjadi kesalahan dalam memproses permintaan.";
    }
}
?>
