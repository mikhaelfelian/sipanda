<?php

namespace App\Controllers;

use App\Libraries\SerpApi;
use App\Libraries\OsintAnalyzer;
use App\Models\KeywordModel;

class Serp extends BaseController
{
    protected $serpApi;
    protected $keywordModel;
    protected $osintAnalyzer;

    public function __construct()
    {
        $this->serpApi = new SerpApi();
        $this->keywordModel = new KeywordModel();
        $this->osintAnalyzer = new OsintAnalyzer();
    }

    public function index()
    {
        // Check if user is logged in
        if (!$this->ionAuth->loggedIn()) {
            return redirect()->to('/auth/login');
        }

        $userId = $this->ionAuth->user()->row()->id;
        $recentSearches = $this->keywordModel->getUserHistory($userId, 5);
        $popularKeywords = $this->keywordModel->getPopularKeywords(5);

        $data = [
            'title'           => 'Google Search Analysis',
            'Pengaturan'      => $this->pengaturan,
            'user'            => $this->ionAuth->user()->row(),
            'isMenuActive'    => isMenuActive('serp') ? 'active' : '',
            'searchResults'   => [],
            'recentSearches'  => $recentSearches,
            'popularKeywords' => $popularKeywords
        ];

        return view($this->theme->getThemePath() . '/serp/index', $data);
    }

    public function search()
    {
        // Validate input
        $rules = [
            'query' => 'required|min_length[3]|max_length[255]',
            'deep_search' => 'permit_empty|in_list[0,1]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $query = $this->request->getPost('query');
        $deepSearch = (bool)$this->request->getPost('deep_search');

        // Save keyword to database
        $this->keywordModel->save([
            'keyword' => $query,
            'search_count' => 1,
            'last_searched' => date('Y-m-d H:i:s')
        ]);

        // Perform OSINT analysis
        $osintAnalysis = $this->osintAnalyzer->analyzeTrend($query);

        // Prepare search parameters
        $searchParams = [
            'engine' => 'google',
            'google_domain' => 'google.co.id',
            'gl' => 'id',
            'hl' => 'id',
            'num' => 20
        ];

        // Add deep search parameters if enabled
        if ($deepSearch) {
            $searchParams = array_merge($searchParams, [
                'tbs' => 'qdr:y' // Past year
            ]);
        }

        try {
            // Perform search
            $results = $this->serpApi->search($query, $searchParams);

            // Prepare data for view
            $data = [
                'title' => 'Search Results',
                'query' => $query,
                'results' => $results['organic_results'] ?? [],
                'osintAnalysis' => $osintAnalysis,
                'deepSearch' => $deepSearch
            ];

            return view('admin-lte-3/serp/results', $data);
        } catch (\RuntimeException $e) {
            return redirect()->back()->withInput()->with('error', 'Search failed: ' . $e->getMessage());
        }
    }
} 