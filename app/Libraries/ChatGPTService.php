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
     * Set API key manually if not set via environment
     * 
     * @param string $apiKey OpenAI API key
     * @return void
     */
    public function setApiKey($apiKey)
    {
        $this->apiKey = $apiKey;
    }

    /**
     * Ask a question to ChatGPT
     * 
     * @param string $prompt The user's question or prompt
     * @param string $model The model to use (default: gpt-3.5-turbo)
     * @param float $temperature Temperature setting (0-1)
     * @param int $maxTokens Maximum tokens to generate
     * @return string The response from ChatGPT
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
     * Ask with system instruction
     * 
     * @param string $prompt The user's question or prompt
     * @param string $systemInstruction System instruction to guide the model
     * @param string $model The model to use
     * @param float $temperature Temperature setting (0-1)
     * @param int $maxTokens Maximum tokens to generate
     * @return string The response from ChatGPT
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
     * Continue a conversation with ChatGPT
     * 
     * @param array $messages Array of previous messages
     * @param string $model The model to use
     * @param float $temperature Temperature setting (0-1)
     * @param int $maxTokens Maximum tokens to generate
     * @return string The response from ChatGPT
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
     * Make the API request to OpenAI
     * 
     * @param array $data The request data
     * @return string|array The response content or error message
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