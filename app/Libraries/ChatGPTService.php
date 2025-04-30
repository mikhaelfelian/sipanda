<?php

namespace App\Libraries;

class ChatGPTService
{
    protected $apiKey;
    protected $endpoint = 'https://api.openai.com/v1/chat/completions';

    public function __construct()
    {
        $this->apiKey = getenv('OPENAI_API_KEY') ?: '';
    }

    /**
     * Mengatur API key secara manual jika tidak diatur melalui environment
     * 
     * @param string $apiKey Kunci API OpenAI
     * @return void
     */
    public function setApiKey($apiKey)
    {
        $this->apiKey = $apiKey;
    }

    /**
     * Mengajukan pertanyaan ke ChatGPT
     * 
     * @param string $prompt Pertanyaan atau prompt dari pengguna
     * @param string $model Model yang digunakan (default: gpt-3.5-turbo)
     * @param float $temperature Pengaturan temperature (0-1)
     * @param int $maxTokens Jumlah maksimum token yang dihasilkan
     * @return string Respons dari ChatGPT
     */
    public function ask($prompt, $model = 'gpt-3.5-turbo', $temperature = 0.7, $maxTokens = 1000)
    {
        $data = [
            'model' => $model,
            'messages' => [
                ['role' => 'user', 'content' => $prompt]
            ],
            'temperature' => $temperature,
            'max_tokens' => $maxTokens
        ];

        return $this->makeRequest($data);
    }

    /**
     * Mengajukan pertanyaan dengan instruksi sistem
     * 
     * @param string $prompt Pertanyaan atau prompt dari pengguna
     * @param string $systemInstruction Instruksi sistem untuk mengarahkan model
     * @param string $model Model yang digunakan
     * @param float $temperature Pengaturan temperature (0-1)
     * @param int $maxTokens Jumlah maksimum token yang dihasilkan
     * @return string Respons dari ChatGPT
     */
    public function askWithSystemInstruction($prompt, $systemInstruction, $model = 'gpt-3.5-turbo', $temperature = 0.7, $maxTokens = 1000)
    {
        $data = [
            'model' => $model,
            'messages' => [
                ['role' => 'system', 'content' => $systemInstruction],
                ['role' => 'user', 'content' => $prompt]
            ],
            'temperature' => $temperature,
            'max_tokens' => $maxTokens
        ];

        return $this->makeRequest($data);
    }
    
    /**
     * Melanjutkan percakapan dengan ChatGPT
     * 
     * @param array $messages Array pesan-pesan sebelumnya
     * @param string $model Model yang digunakan
     * @param float $temperature Pengaturan temperature (0-1)
     * @param int $maxTokens Jumlah maksimum token yang dihasilkan
     * @return string Respons dari ChatGPT
     */
    public function conversation($messages, $model = 'gpt-3.5-turbo', $temperature = 0.7, $maxTokens = 1000)
    {
        $data = [
            'model' => $model,
            'messages' => $messages,
            'temperature' => $temperature,
            'max_tokens' => $maxTokens
        ];

        return $this->makeRequest($data);
    }

    /**
     * Membuat permintaan API ke OpenAI
     * 
     * @param array $data Data permintaan
     * @return string|array Konten respons atau pesan kesalahan
     */
    protected function makeRequest($data)
    {
        if (empty($this->apiKey)) {
            return 'API key not set';
        }

        $headers = [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->apiKey,
        ];

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => $this->endpoint,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => 0,
        ]);

        $response = curl_exec($curl);

        if (curl_errno($curl)) {
            return 'Curl error: ' . curl_error($curl);
        }

        curl_close($curl);

        $result = json_decode($response, true);

        if (isset($result['error'])) {
            return 'API error: ' . ($result['error']['message'] ?? 'Unknown error');
        }

        return $result['choices'][0]['message']['content'] ?? 'No response';
    }
} 