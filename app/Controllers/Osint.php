<?php

namespace App\Controllers;

/**
 * OSINT Controller
 * 
 * Controller for OSINT (Open Source Intelligence) tools including social media analysis
 */
class Osint extends BaseController
{
    protected $ionAuth;
    protected $session;
    protected $theme;
    protected $pengaturan;
    protected $apifyXScraper;

    public function __construct()
    {
        parent::__construct();
        
        // Load helpers
        helper(['form', 'url', 'theme']);
        
        // Initialize X Scraper
        $this->apifyXScraper = new \App\Libraries\ApifyXScraper();
        
        // Set X API credentials
        $credentials = $this->getXApiCredentials();
        if ($credentials['api_key'] && $credentials['secret_key']) {
            $this->apifyXScraper->setXApiCredentials(
                $credentials['api_key'],
                $credentials['secret_key']
            );
        }
    }
    
    /**
     * Main OSINT dashboard
     */
    public function index()
    {
        $data = [
            'title' => 'OSINT Dashboard',
            'active_menu' => 'osint',
            'Pengaturan' => $this->pengaturan,
            'user' => $this->ionAuth->user()->row(),
            'recent_searches' => $this->getRecentSearches('x_username', 10),
            'popular_searches' => $this->getPopularSearches('x_username', 10)
        ];
        
        return view(theme_path('osint/osint'), $data);
    }
    
    /**
     * X.com OSINT dashboard
     */
    public function x()
    {
        $data = [
            'title' => 'X.com OSINT',
            'active_menu' => 'osint',
            'Pengaturan' => $this->pengaturan,
            'user' => $this->ionAuth->user()->row(),
            'recent_searches' => $this->getRecentSearches('x_username', 10),
            'popular_searches' => $this->getPopularSearches('x_username', 10)
        ];
        
        return view(theme_path('osint/x'), $data);
    }
    
    /**
     * Get X.com profile
     */
    public function xProfile()
    {
        // Set default response
        $response = [
            'success' => false,
            'message' => 'Invalid request',
            'data' => null
        ];
        
        // Only process AJAX requests
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON($response);
        }
        
        // Get username from request
        $username = trim($this->request->getPost('username'));
        
        if (empty($username)) {
            $response['message'] = 'Username is required';
            return $this->response->setJSON($response);
        }
        
        try {
            // Save the search query
            $this->saveSearchQuery('x_username', $username);
            
            // Get user profile
            $profile = $this->apifyXScraper->getUserProfile($username);
            
            if (empty($profile)) {
                $response['message'] = 'User profile not found';
                return $this->response->setJSON($response);
            }
            
            // Get profile analysis
            $analysis = $this->apifyXScraper->analyzeUserProfile($username);
            
            // Return success response
            $response = [
                'success' => true,
                'message' => 'Profile retrieved successfully',
                'data' => [
                    'profile' => $profile,
                    'analysis' => $analysis
                ]
            ];
            
            return $this->response->setJSON($response);
        } catch (\Exception $e) {
            log_message('error', 'X profile error: ' . $e->getMessage());
            $response['message'] = 'Error retrieving profile: ' . $e->getMessage();
            return $this->response->setJSON($response);
        }
    }
    
    /**
     * Export X.com profile to PDF
     */
    public function xExportProfile($username)
    {
        // Load PDF helper
        helper('pdf');
        
        try {
            // Get user profile
            $profile = $this->apifyXScraper->getUserProfile($username);
            
            if (empty($profile)) {
                return redirect()->to('/osint')->with('error', 'User profile not found');
            }
            
            // Get profile analysis
            $analysis = $this->apifyXScraper->analyzeUserProfile($username);
            
            // Generate PDF
            $data = [
                'profile' => $profile,
                'analysis' => $analysis,
                'date' => date('Y-m-d H:i:s'),
                'user' => $this->ionAuth->user()->row()
            ];
            
            $pdfFile = generate_pdf($data, 'X.com Profile Analysis - @' . $username, 'twitter_profile');
            
            // Return PDF for download
            return $this->response->download($pdfFile, null)->setFileName('x_profile_' . $username . '.pdf');
        } catch (\Exception $e) {
            log_message('error', 'X profile PDF error: ' . $e->getMessage());
            return redirect()->to('/osint')->with('error', 'Error generating PDF: ' . $e->getMessage());
        }
    }
    
    /**
     * Get recent searches
     * 
     * @param string $type Search type
     * @param int $limit Maximum number of searches to retrieve
     * @return array Recent searches
     */
    private function getRecentSearches($type, $limit = 10)
    {
        $searchModel = new \App\Models\SearchHistoryModel();
        return $searchModel->getRecentSearchesByType($this->ionAuth->getUserId(), $type, (int)$limit);
    }
    
    /**
     * Get popular searches
     * 
     * @param string $type Search type
     * @param int $limit Maximum number of searches to retrieve
     * @return array Popular searches
     */
    private function getPopularSearches($type, $limit = 10)
    {
        $searchModel = new \App\Models\SearchHistoryModel();
        return $searchModel->getPopularSearches((int)$limit);
    }
    
    /**
     * Save search query to history
     * 
     * @param string $type Search type
     * @param string $query Search query
     * @param string $engine Search engine used
     * @param int $resultCount Number of results found
     * @return bool Success status
     */
    private function saveSearchQuery($type, $query, $engine = 'twitter', $resultCount = 0)
    {
        $searchModel = new \App\Models\SearchHistoryModel();
        return $searchModel->logSearch(
            $this->ionAuth->getUserId(),
            $type,
            $query,
            $engine,
            $resultCount
        );
    }

    /**
     * Get X API credentials from database
     * 
     * @return array Array containing API key and secret
     */
    private function getXApiCredentials()
    {
        $db = \Config\Database::connect();
        
        $apiKey = $db->table('tbl_pengaturan_api')
                    ->where('name', 'x_api_key')
                    ->where('deleted_at IS NULL')
                    ->get()
                    ->getRow();
                    
        $secretKey = $db->table('tbl_pengaturan_api')
                    ->where('name', 'x_secret_key')
                    ->where('deleted_at IS NULL')
                    ->get()
                    ->getRow();
                    
        return [
            'api_key' => $apiKey ? $apiKey->tokens : null,
            'secret_key' => $secretKey ? $secretKey->tokens : null
        ];
    }
} 