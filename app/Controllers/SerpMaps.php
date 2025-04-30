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

        // If location is provided, use it instead of default coordinates
        if ($location !== 'Indonesia') {
            $searchParams['data'] = 'local';
            $searchParams['ll'] = null; // Clear the default coordinates
            $searchParams['location'] = $location;
        }

        try {
            // Perform search
            $results = $this->serpApi->search($query, $searchParams);

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
                'isMenuActive'   => isMenuActive('serp/maps') ? 'active' : ''
            ];

            return view($this->theme->getThemePath() . '/serp/maps_results', $data);
        } catch (\RuntimeException $e) {
            if (strpos($e->getMessage(), 'timed out') !== false) {
                // Show a toast error for timeout
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Timeout koneksi ke Google Maps API. Silakan coba lagi nanti.');
            } else if (strpos($e->getMessage(), 'SERP API Error') !== false) {
                // Show a toast error for other API errors
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Koneksi ke Google Maps API gagal: ' . $e->getMessage());
            }
            
            // For other unexpected errors, rethrow
            throw $e;
        }
    }
} 