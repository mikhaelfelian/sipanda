<?php

namespace App\Libraries;

use CodeIgniter\Config\Services;

class SerpApi
{
    protected $apiKey;
    protected $baseUrl = 'https://serpapi.com/search.json';
    protected $defaultParams = [
        'engine' => 'google',
        'google_domain' => 'google.com',
        'gl' => 'us',
        'hl' => 'en'
    ];

    public function __construct()
    {
        // Load configuration
        $config = config('Serp');
        $this->apiKey = $config->apiKey;
        $this->defaultParams = array_merge($this->defaultParams, $config->defaultParams);
    }

    /**
     * Set API key
     * 
     * @param string $apiKey
     * @return $this
     */
    public function setApiKey(string $apiKey): self
    {
        $this->apiKey = $apiKey;
        return $this;
    }

    /**
     * Set default parameters
     * 
     * @param array $params
     * @return $this
     */
    public function setDefaultParams(array $params): self
    {
        $this->defaultParams = array_merge($this->defaultParams, $params);
        return $this;
    }

    /**
     * Perform a search
     * 
     * @param string $query
     * @param array $params
     * @return array
     * @throws \RuntimeException
     */
    public function search(string $query, array $params = []): array
    {
        if (empty($this->apiKey)) {
            throw new \RuntimeException('SERP API Error: API key is not set');
        }

        // Merge default and custom parameters
        $params = array_merge($this->defaultParams, $params);
        
        // Ensure required parameters are set
        $params['q'] = $query;
        $params['api_key'] = $this->apiKey;
        
        // Remove any empty parameters
        $params = array_filter($params, function($value) {
            return $value !== null && $value !== '';
        });

        // Build query string
        $queryString = http_build_query($params);
        $url = $this->baseUrl . '?' . $queryString;

        // Initialize cURL
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        // Execute request
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        // Check for errors
        if (curl_errno($ch)) {
            $error = curl_error($ch);
            curl_close($ch);
            throw new \RuntimeException('SERP API Error: ' . $error);
        }

        curl_close($ch);

        // Check HTTP status
        if ($httpCode !== 200) {
            $errorMessage = 'SERP API Error: HTTP ' . $httpCode;
            if ($response) {
                $errorData = json_decode($response, true);
                if (isset($errorData['error'])) {
                    $errorMessage .= ' - ' . $errorData['error'];
                }
            }
            throw new \RuntimeException($errorMessage);
        }

        // Decode response
        $result = json_decode($response, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \RuntimeException('SERP API Error: Invalid JSON response');
        }

        return $result;
    }

    /**
     * Get organic search results
     * 
     * @param string $query
     * @param array $params
     * @return array
     */
    public function getOrganicResults(string $query, array $params = []): array
    {
        $results = $this->search($query, $params);
        return $results['organic_results'] ?? [];
    }

    /**
     * Get local search results
     * 
     * @param string $query
     * @param array $params
     * @return array
     */
    public function getLocalResults(string $query, array $params = []): array
    {
        $results = $this->search($query, $params);
        return $results['local_results'] ?? [];
    }

    /**
     * Get related questions
     * 
     * @param string $query
     * @param array $params
     * @return array
     */
    public function getRelatedQuestions(string $query, array $params = []): array
    {
        $results = $this->search($query, $params);
        return $results['related_questions'] ?? [];
    }

    /**
     * Get knowledge graph
     * 
     * @param string $query
     * @param array $params
     * @return array
     */
    public function getKnowledgeGraph(string $query, array $params = []): array
    {
        $results = $this->search($query, $params);
        return $results['knowledge_graph'] ?? [];
    }

    /**
     * Get top stories
     * 
     * @param string $query
     * @param array $params
     * @return array
     */
    public function getTopStories(string $query, array $params = []): array
    {
        $results = $this->search($query, $params);
        return $results['top_stories'] ?? [];
    }
} 