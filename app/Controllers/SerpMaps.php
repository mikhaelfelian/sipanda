<?php
/**
 * SerpMaps Controller
 * 
 * Displays Google Maps search results using SerpApi
 */

namespace App\Controllers;

use App\Libraries\SerpApi;
use App\Models\KeywordModel;

class SerpMaps extends BaseController
{
    protected $serpApi;
    protected $keywordModel;

    public function __construct()
    {
        $this->serpApi = new SerpApi();
        $this->keywordModel = new KeywordModel();
    }

    /**
     * Display the maps search interface
     */
    public function index()
    {
        // Check if user is logged in
        if (!$this->ionAuth->loggedIn()) {
            return redirect()->to('/auth/login');
        }

        $userId = $this->ionAuth->user()->row()->id;
        $recentSearches = $this->keywordModel->getUserHistory($userId, 5);
        $popularKeywords = $this->keywordModel->getPopularKeywords(5);
        
        // Get trending searches from Google Trends with debug info
        $this->serpApi->setApiKey(config('Serp')->apiKey);
        log_message('info', 'Maps Controller: Fetching trending searches');
        $trendingSearches = $this->serpApi->getTrendingSearches('ID', 10);
        log_message('info', 'Maps Controller: Received ' . count($trendingSearches) . ' trending searches');

        $data = [
            'title'           => 'Google Maps Search',
            'Pengaturan'      => $this->pengaturan,
            'user'            => $this->ionAuth->user()->row(),
            'isMenuActive'    => isMenuActive('serp/maps') ? 'active' : '',
            'mapResults'      => [],
            'recentSearches'  => $recentSearches,
            'popularKeywords' => $popularKeywords,
            'trendingSearches' => $trendingSearches,
            'googleMapsKey'   => config('GoogleMaps')->apiKey
        ];

        return view($this->theme->getThemePath() . '/serp/maps', $data);
    }

    /**
     * Perform a maps search using SerpApi
     */
    public function search()
    {
        // Get query from POST or GET
        $query = $this->request->getPost('query') ?? $this->request->getGet('query');
        $location = $this->request->getPost('location') ?? $this->request->getGet('location') ?? 'Indonesia';
        $coordinates = $this->request->getPost('coordinates') ?? $this->request->getGet('coordinates') ?? null;
        
        // Validate input
        if (empty($query) || strlen($query) < 3) {
            return redirect()->back()->withInput()->with('errors', ['query' => 'Kata kunci pencarian diperlukan (minimal 3 karakter)']);
        }

        // Check if keyword exists
        $existing = $this->keywordModel->where('keyword', $query)->first();

        if ($existing) {
            // Update existing: increment count and update last_searched
            $this->keywordModel->update($existing['id'], [
                'search_count' => $existing['search_count'] + 1,
                'last_searched' => date('Y-m-d H:i:s')
            ]);
        } else {
            // Insert new
            $this->keywordModel->insert([
                'keyword' => $query,
                'search_count' => 1,
                'last_searched' => date('Y-m-d H:i:s')
            ]);
        }

        // Prepare search parameters for maps search
        $searchParams = [
            'engine' => 'google_maps',
            'q' => $query,
            'google_domain' => 'google.co.id',
            'gl' => 'id',
            'hl' => 'id',
            'll' => '@-6.2088,106.8456,10z', // Default to Jakarta coordinates
            'type' => 'search',
            'data' => 'local'
        ];

        // If coordinates are provided (from map click), use them
        if ($coordinates) {
            // Format should be lat,lng
            $searchParams['ll'] = '@' . $coordinates . ',14z'; // 14z is a good zoom level for specific coordinates
            
            // Set location name based on coordinates for display purposes
            $location = 'Koordinat (' . $coordinates . ')';
        }
        // Otherwise if location is provided, use it instead of default coordinates
        else if ($location !== 'Indonesia') {
            $searchParams['data'] = 'local';
            $searchParams['ll'] = null; // Clear the default coordinates
            $searchParams['location'] = $location;
        }

        try {
            // Log the search parameters for debugging
            log_message('info', 'Maps Search: Searching for "' . $query . '" in "' . $location . '"');
            log_message('debug', 'Maps Search parameters: ' . json_encode($searchParams));
            
            // Perform search
            $results = $this->serpApi->search($query, $searchParams);
            
            // Log the search results for debugging
            $hasResults = isset($results['local_results']) && !empty($results['local_results']);
            log_message('info', 'Maps Search: Found ' . ($hasResults ? count($results['local_results']) : 0) . ' results');
            
            // Get trending searches for the results page
            $trendingSearches = $this->serpApi->getTrendingSearches('ID', 10);
            
            // Get user's recent searches and popular keywords
            $userId = $this->ionAuth->user()->row()->id;
            $recentSearches = $this->keywordModel->getUserHistory($userId, 5);
            $popularKeywords = $this->keywordModel->getPopularKeywords(5);

            // Prepare data for view
            $data = [
                'title'          => 'Maps Search Results',
                'query'          => $query,
                'location'       => $location,
                'mapResults'     => $results['local_results'] ?? [],
                'placesResults'  => $results['places_results'] ?? [],
                'mapInfo'        => $results['search_metadata'] ?? [],
                'Pengaturan'     => $this->pengaturan,
                'user'           => $this->ionAuth->user()->row(),
                'isMenuActive'   => isMenuActive('serp/maps') ? 'active' : '',
                'recentSearches' => $recentSearches,
                'popularKeywords' => $popularKeywords,
                'trendingSearches' => $trendingSearches
            ];
            
            // Check if we have results
            if (!$hasResults) {
                // Add a flash message to indicate no results were found
                session()->setFlashdata('info', 'Tidak ada hasil ditemukan untuk "' . esc($query) . '"' . 
                    ($location !== 'Indonesia' ? ' di "' . esc($location) . '"' : '') . 
                    '. Coba kata kunci atau lokasi yang berbeda.');
            }

            return view($this->theme->getThemePath() . '/serp/maps_results', $data);
        } catch (\RuntimeException $e) {
            if (strpos($e->getMessage(), 'timed out') !== false) {
                // Show a toast error for timeout
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Timeout koneksi ke Google Maps API. Silakan coba lagi nanti.');
            } else if (strpos($e->getMessage(), 'SERP API Error') !== false) {
                // Show a toast error for other API errors
                log_message('error', 'Maps Search API Error: ' . $e->getMessage());
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Koneksi ke Google Maps API gagal: ' . $e->getMessage());
            }
            
            // For other unexpected errors, log and show a generic message
            log_message('error', 'Maps Search Error: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat mencari di Maps. Silakan coba lagi nanti.');
        }
    }
} 