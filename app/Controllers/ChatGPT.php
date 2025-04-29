<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class ChatGPT extends Controller
{
    private $apiKey = '';

    public function __construct()
    {
        // Force close any active sessions
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_write_close();
        }
        
        // Prevent new sessions from starting
        ini_set('session.use_cookies', '0');
        ini_set('session.use_only_cookies', '0');
        ini_set('session.use_trans_sid', '0');
    }

    public function send()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Metode request tidak valid'
            ])->setStatusCode(403);
        }

        try {
            $userMessage = $this->request->getPost('message');
            if (empty($userMessage)) {
                throw new \Exception('Pesan tidak boleh kosong');
            }

            $ch = curl_init('https://api.openai.com/v1/chat/completions');
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => 0,
                CURLOPT_HTTPHEADER => [
                    'Content-Type: application/json',
                    'Authorization: Bearer ' . $this->apiKey
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
                throw new \Exception('Error koneksi: ' . $error);
            }

            $result = json_decode($response, true);

            if (isset($result['error'])) {
                throw new \Exception($result['error']['message'] ?? 'Error API');
            }

            if (!isset($result['choices'][0]['message']['content'])) {
                throw new \Exception('Format response tidak valid');
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => $result['choices'][0]['message']['content']
            ]);

        } catch (\Exception $e) {
            log_message('error', '[ChatGPT] Error: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }
} 