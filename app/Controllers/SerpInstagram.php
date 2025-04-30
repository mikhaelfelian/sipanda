<?php
/**
 * SerpInstagram Controller
 * 
 * Displays Instagram search results using instagram-php-scraper
 */

namespace App\Controllers;

use App\Models\KeywordModel;
use App\Libraries\InstaWrapper;
use \Phpfastcache\Helper\Psr16Adapter;

class SerpInstagram extends BaseController
{
    protected $keywordModel;
    protected $cachePool;
    protected $instagram;
    protected $apiKey;

    public function __construct()
    {
        parent::__construct();
        $this->keywordModel = new KeywordModel();
        
        // Set Instagram Meta API key
        $this->apiKey = 'ee851f252a1da460ad9760c91e024eb5';
        
        // Set up caching for Instagram scraper
        $this->cachePool = new Psr16Adapter('Files');
        
        try {
            // Initialize the Instagram wrapper with the cache
            $this->instagram = new InstaWrapper($this->cachePool);
            
            // Set the API key for the wrapper
            if (method_exists($this->instagram, 'setApiKey')) {
                $this->instagram->setApiKey($this->apiKey);
                log_message('info', 'Instagram API key set successfully');
            } else {
                log_message('warning', 'InstaWrapper does not have setApiKey method, API integration disabled');
            }
        } catch (\Exception $e) {
            // Log the exception but continue with no Instagram
            log_message('error', 'Failed to initialize Instagram wrapper: ' . $e->getMessage());
            // Make sure we at least have an instance to prevent fatal errors
            $this->instagram = new InstaWrapper($this->cachePool);
        }
        
        // Pre-set the API key statically to ensure it's available even in static calls
        if (method_exists('\\App\\Libraries\\InstaWrapper', 'setApiKeyStatic')) {
            \App\Libraries\InstaWrapper::setApiKeyStatic($this->apiKey);
        }
    }

    /**
     * Display the Instagram search interface
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
        $serpApi = new \App\Libraries\SerpApi();
        $serpApi->setApiKey(config('Serp')->apiKey);
        log_message('info', 'Instagram Controller: Fetching trending searches');
        $trendingSearches = $serpApi->getTrendingSearches('ID', 10);
        log_message('info', 'Instagram Controller: Received ' . count($trendingSearches) . ' trending searches');

        $data = [
            'title'           => 'Instagram Search',
            'Pengaturan'      => $this->pengaturan,
            'user'            => $this->ionAuth->user()->row(),
            'isMenuActive'    => isMenuActive('serp/instagram') ? 'active' : '',
            'recentSearches'  => $recentSearches,
            'popularKeywords' => $popularKeywords,
            'trendingSearches' => $trendingSearches
        ];

        return view($this->theme->getThemePath() . '/serp/instagram', $data);
    }

    /**
     * Search for Instagram profiles
     */
    public function searchProfiles()
    {
        // Validate input
        $rules = [
            'query' => 'required|min_length[3]|max_length[255]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $query = $this->request->getPost('query');
        
        // Save search to keyword history
        $this->saveKeyword($query);

        try {
            // Add a delay to avoid rate limiting
            sleep(1);
            
            // Search for accounts using static method
            try {
                $accounts = InstaWrapper::searchAccountsByUsername($query);
            } catch (\Error $e) {
                // Handle specific "Object of class stdClass could not be converted to string" error
                if (strpos($e->getMessage(), 'Object of class stdClass could not be converted to string') !== false) {
                    log_message('error', 'Instagram API Error (stdClass conversion): ' . $e->getMessage() . ' [Trace: ' . $e->getTraceAsString() . ']');
                    
                    // Return an empty array as fallback when this specific error occurs
                    $accounts = [];
                } else {
                    // Re-throw if it's a different error
                    throw $e;
                }
            }

            // Prepare data for view
            $data = [
                'title'          => 'Instagram Profile Results',
                'query'          => $query,
                'profiles'       => $accounts,
                'Pengaturan'     => $this->pengaturan,
                'user'           => $this->ionAuth->user()->row(),
                'isMenuActive'   => isMenuActive('serp/instagram') ? 'active' : ''
            ];

            return view($this->theme->getThemePath() . '/serp/instagram_profiles', $data);
        } catch (\Exception $e) {
            return $this->handleError($e);
        }
    }

    /**
     * Search for Instagram hashtags
     */
    public function searchHashtags()
    {
        // Validate input
        $rules = [
            'query' => 'required|min_length[3]|max_length[255]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $query = $this->request->getPost('query');
        
        // Remove # if present
        $query = ltrim($query, '#');
        
        // Save search to keyword history
        $this->saveKeyword($query);

        try {
            // Add a delay to avoid rate limiting
            sleep(1);
            
            // Search for hashtag using static proxy
            $hashtag = InstaWrapper::getHashtagStatic($query);
            $mediaCount = $hashtag->getMediaCount();
            
            // Add another delay before the second API call
            sleep(1);
            
            // Get recent media with this hashtag (limited to 12) using static proxy
            $medias = InstaWrapper::getMediasByTagStatic($query, 12);

            // Prepare data for view
            $data = [
                'title'          => 'Instagram Hashtag Results',
                'query'          => $query,
                'hashtag'        => $hashtag,
                'mediaCount'     => $mediaCount,
                'medias'         => $medias,
                'Pengaturan'     => $this->pengaturan,
                'user'           => $this->ionAuth->user()->row(),
                'isMenuActive'   => isMenuActive('serp/instagram') ? 'active' : ''
            ];

            return view($this->theme->getThemePath() . '/serp/instagram_hashtags', $data);
        } catch (\Exception $e) {
            return $this->handleError($e);
        }
    }

    /**
     * View an Instagram profile details
     */
    public function viewProfile($username)
    {
        try {
            // Add a delay to avoid rate limiting
            sleep(1);
            
            // Get account info - using static method
            $account = InstaWrapper::getAccount($username);
            
            // Add another delay before the second API call
            sleep(1);
            
            // Get account media (limited to 12) - using static method
            $medias = InstaWrapper::getMedias($username, 12);

            // Prepare data for view
            $data = [
                'title'          => 'Instagram Profile: ' . $username,
                'account'        => $account,
                'medias'         => $medias,
                'Pengaturan'     => $this->pengaturan,
                'user'           => $this->ionAuth->user()->row(),
                'isMenuActive'   => isMenuActive('serp/instagram') ? 'active' : ''
            ];

            return view($this->theme->getThemePath() . '/serp/instagram_profile_view', $data);
        } catch (\Exception $e) {
            return $this->handleError($e);
        }
    }

    /**
     * View details of a single Instagram post
     */
    public function viewPost($shortcode)
    {
        try {
            // Add a delay to avoid rate limiting
            sleep(1);
            
            // Get media by its shortcode - using static method
            $media = InstaWrapper::getMediaByUrl('https://www.instagram.com/p/' . $shortcode . '/');
            
            // Add another delay before the second API call
            sleep(1);
            
            // Get media comments (limited to 10) - using static method
            $comments = InstaWrapper::getMediaCommentsByCode($shortcode, 10);

            // Prepare data for view
            $data = [
                'title'          => 'Instagram Post',
                'media'          => $media,
                'comments'       => $comments,
                'Pengaturan'     => $this->pengaturan,
                'user'           => $this->ionAuth->user()->row(),
                'isMenuActive'   => isMenuActive('serp/instagram') ? 'active' : ''
            ];

            return view($this->theme->getThemePath() . '/serp/instagram_post_view', $data);
        } catch (\Exception $e) {
            return $this->handleError($e);
        }
    }

    /**
     * Save keyword to database or update if exists
     */
    private function saveKeyword($query)
    {
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
    }

    /**
     * Handle errors from Instagram API
     */
    private function handleError(\Exception $e)
    {
        $errorMessage = $e->getMessage();
        log_message('error', 'Instagram API Error: ' . $errorMessage . ' [Trace: ' . $e->getTraceAsString() . ']');
        
        // Check for rate limiting errors
        if (
            strpos($errorMessage, 'rate limit') !== false || 
            strpos($errorMessage, '429') !== false ||
            strpos($errorMessage, 'too many requests') !== false
        ) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Instagram rate limit exceeded. Please try again after a few minutes.');
        } 
        // Check for not found errors
        else if (
            strpos($errorMessage, '404') !== false || 
            strpos($errorMessage, 'not found') !== false
        ) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'The requested Instagram content was not found. It may have been deleted or made private.');
        } 
        // Check for authentication errors
        else if (
            strpos($errorMessage, '401') !== false || 
            strpos($errorMessage, 'auth') !== false ||
            strpos($errorMessage, 'login') !== false
        ) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Instagram requires authentication to access this content. This tool only works with public content.');
        }
        // Generic error fallback
        else {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Instagram API Error: ' . $errorMessage);
        }
    }
} 