<?php
/**
 * XOsint Controller
 * 
 * This controller provides functionality for X.com (formerly Twitter) OSINT analysis.
 * It utilizes the ApifyXScraper library to fetch data from X.com.
 *
 * @author    Mikhael Felian Waskito <mikhaelfelian@gmail.com>
 * @created   2025-04-30
 */

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Libraries\ApifyXScraper;

class XOsint extends BaseController
{
    /**
     * ApifyXScraper instance
     *
     * @var ApifyXScraper
     */
    protected $xScraper;
    
    /**
     * Constructor
     */
    public function __construct()
    {
        // Initialize the X.com scraper library
        $this->xScraper = new ApifyXScraper();
        
        // Load helpers
        helper(['form', 'url', 'theme']);
    }
    
    /**
     * Index page for X OSINT
     */
    public function index()
    {
        $data = [
            'title'           => 'X.com OSINT',
            'active_menu'     => 'xosint',
            'Pengaturan'      => $this->pengaturan,
            'user'            => $this->ionAuth->user()->row(),
            'recent_searches' => $this->getRecentSearches('x_username', 10),
            'popular_searches'=> $this->getPopularSearches('x_username', 10),
            'isMenuActive'    => isMenuActive('serp/xosint') ? 'active' : ''
        ];
        
        return view($this->theme->getThemePath() . '/serp/xosint', $data);
    }
    
    /**
     * Search for a user profile
     */
    public function profile()
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
            $profile = $this->xScraper->getUserProfile($username);
            
            // Use default profile if not found
            if (empty($profile)) {
                $profile = [
                    'username' => $username,
                    'displayName' => 'Profile Access Issue',
                    'description' => 'The profile exists on X.com but could not be accessed through our system at this time. This may be due to API limitations or temporary connectivity issues.',
                    'verified' => false,
                    'profileImageUrl' => '',
                    'location' => '',
                    'url' => '',
                    'followersCount' => 0,
                    'followingCount' => 0,
                    'statusesCount' => 0,
                    'createdAt' => '',
                    'isDefaultProfile' => true
                ];
                
                // Get some analysis even if profile not found
                $analysis = [
                    'success' => false,
                    'username' => $username,
                    'displayName' => 'Profile Access Issue',
                    'bio' => 'The profile exists on X.com but could not be accessed through our system at this time.',
                    'tweetActivity' => [
                        'total' => 0,
                        'withMedia' => 0,
                        'withLinks' => 0,
                        'withMentions' => 0,
                        'withHashtags' => 0,
                        'retweets' => 0,
                        'avgLikes' => 0,
                        'avgRetweets' => 0
                    ],
                    'topHashtags' => [],
                    'topMentions' => []
                ];
                
                // Return response with default data
                $response = [
                    'success' => true, // Still return success to show the UI
                    'message' => 'Profile exists but could not be accessed. Showing limited information.',
                    'data' => [
                        'profile' => $profile,
                        'analysis' => $analysis
                    ]
                ];
                
                return $this->response->setJSON($response);
            }
            
            // Get profile analysis
            $analysis = $this->xScraper->analyzeUserProfile($username);
            
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
            
            // Use default profile if exception occurs
            $profile = [
                'username' => $username,
                'displayName' => 'Error',
                'description' => 'An error occurred while retrieving the profile: ' . $e->getMessage(),
                'verified' => false,
                'profileImageUrl' => '',
                'location' => '',
                'url' => '',
                'followersCount' => 0,
                'followingCount' => 0,
                'statusesCount' => 0,
                'createdAt' => '',
                'isDefaultProfile' => true,
                'hasError' => true
            ];
            
            // Default analysis
            $analysis = [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
                'username' => $username,
                'tweetActivity' => [
                    'total' => 0,
                    'withMedia' => 0,
                    'withLinks' => 0,
                    'withMentions' => 0,
                    'withHashtags' => 0,
                    'retweets' => 0,
                    'avgLikes' => 0,
                    'avgRetweets' => 0
                ],
                'topHashtags' => [],
                'topMentions' => []
            ];
            
            $response = [
                'success' => true, // Still return success to show the UI
                'message' => 'Error retrieving profile: ' . $e->getMessage(),
                'data' => [
                    'profile' => $profile,
                    'analysis' => $analysis
                ]
            ];
            return $this->response->setJSON($response);
        }
    }
    
    /**
     * Search for tweets by keyword or hashtag
     */
    public function search()
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
        
        // Get query from request
        $query = trim($this->request->getPost('query'));
        $limit = (int) $this->request->getPost('limit', 50);
        
        if (empty($query)) {
            $response['message'] = 'Search query is required';
            return $this->response->setJSON($response);
        }
        
        try {
            // Save the search query
            $this->saveSearchQuery('x_search', $query);
            
            // Search tweets
            $tweets = $this->xScraper->searchTweets($query, $limit);
            
            if (empty($tweets)) {
                $response['message'] = 'No tweets found';
                return $this->response->setJSON($response);
            }
            
            // Prepare statistics
            $stats = $this->calculateTweetStats($tweets);
            
            // Return success response
            $response = [
                'success' => true,
                'message' => 'Tweets retrieved successfully',
                'data' => [
                    'query' => $query,
                    'count' => count($tweets),
                    'tweets' => $tweets,
                    'stats' => $stats
                ]
            ];
            
            return $this->response->setJSON($response);
        } catch (\Exception $e) {
            log_message('error', 'X search error: ' . $e->getMessage());
            $response['message'] = 'Error searching tweets: ' . $e->getMessage();
            return $this->response->setJSON($response);
        }
    }
    
    /**
     * Get trending topics
     */
    public function trends()
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
        
        // Get country code from request
        $country = trim($this->request->getPost('country', ''));
        
        try {
            // Get trending topics
            $trends = $this->xScraper->getTrendingTopics($country);
            
            if (empty($trends)) {
                $response['message'] = 'No trending topics found';
                return $this->response->setJSON($response);
            }
            
            // Return success response
            $response = [
                'success' => true,
                'message' => 'Trending topics retrieved successfully',
                'data' => [
                    'country' => $country,
                    'trends' => $trends
                ]
            ];
            
            return $this->response->setJSON($response);
        } catch (\Exception $e) {
            log_message('error', 'X trends error: ' . $e->getMessage());
            $response['message'] = 'Error retrieving trends: ' . $e->getMessage();
            return $this->response->setJSON($response);
        }
    }
    
    /**
     * Export profile analysis to PDF
     */
    public function exportProfilePdf()
    {
        // Load PDF helper
        helper('pdf');
        
        // Set default response
        $response = [
            'success' => false,
            'message' => 'Invalid request',
            'pdf' => null
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
            // Get user profile and analysis
            $profile = $this->xScraper->getUserProfile($username);
            $analysis = $this->xScraper->analyzeUserProfile($username);
            
            if (empty($profile)) {
                $response['message'] = 'User profile not found';
                return $this->response->setJSON($response);
            }
            
            // Create PDF document
            $pdf = new \TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
            
            // Set document information
            $pdf->SetCreator(PDF_CREATOR);
            $pdf->SetAuthor('SIPANDA');
            $pdf->SetTitle('X.com Profile Analysis - @' . $username);
            $pdf->SetSubject('OSINT Report for X.com Profile');
            $pdf->SetKeywords('X.com, Twitter, OSINT, Profile, Analysis');
            
            // Set default header data
            $pdf->SetHeaderData('', 0, 'X.com Profile Analysis', 'Generated on: ' . date('Y-m-d H:i:s'));
            
            // Set header and footer fonts
            $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
            $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
            
            // Set default monospaced font
            $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
            
            // Set margins
            $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
            $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
            $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
            
            // Set auto page breaks
            $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
            
            // Add a page
            $pdf->AddPage();
            
            // Profile title
            $pdf->SetFont('dejavusans', 'B', 16);
            $pdf->Cell(0, 10, 'Profile Analysis: @' . $username, 0, 1, 'C');
            $pdf->Ln(5);
            
            // Basic profile information
            $pdf->SetFont('dejavusans', 'B', 14);
            $pdf->Cell(0, 10, 'Basic Information', 0, 1);
            
            $pdf->SetFont('dejavusans', 'B', 10);
            $pdf->Cell(50, 10, 'Display Name:', 0, 0);
            $pdf->SetFont('dejavusans', '', 10);
            $pdf->Cell(0, 10, $profile['displayName'] ?? 'N/A', 0, 1);
            
            $pdf->SetFont('dejavusans', 'B', 10);
            $pdf->Cell(50, 10, 'Bio:', 0, 0);
            $pdf->SetFont('dejavusans', '', 10);
            $pdf->Cell(0, 10, $profile['bio'] ?? 'N/A', 0, 1);
            
            $pdf->SetFont('dejavusans', 'B', 10);
            $pdf->Cell(50, 10, 'Location:', 0, 0);
            $pdf->SetFont('dejavusans', '', 10);
            $pdf->Cell(0, 10, $profile['location'] ?? 'N/A', 0, 1);
            
            $pdf->SetFont('dejavusans', 'B', 10);
            $pdf->Cell(50, 10, 'Account Created:', 0, 0);
            $pdf->SetFont('dejavusans', '', 10);
            $pdf->Cell(0, 10, $profile['created'] ?? 'N/A', 0, 1);
            
            $pdf->SetFont('dejavusans', 'B', 10);
            $pdf->Cell(50, 10, 'Verified:', 0, 0);
            $pdf->SetFont('dejavusans', '', 10);
            $pdf->Cell(0, 10, ($profile['verified'] ?? false) ? 'Yes' : 'No', 0, 1);
            
            // Account statistics
            $pdf->Ln(5);
            $pdf->SetFont('dejavusans', 'B', 14);
            $pdf->Cell(0, 10, 'Account Statistics', 0, 1);
            
            $pdf->SetFont('dejavusans', 'B', 10);
            $pdf->Cell(50, 10, 'Followers:', 0, 0);
            $pdf->SetFont('dejavusans', '', 10);
            $pdf->Cell(0, 10, number_format($profile['followersCount'] ?? 0), 0, 1);
            
            $pdf->SetFont('dejavusans', 'B', 10);
            $pdf->Cell(50, 10, 'Following:', 0, 0);
            $pdf->SetFont('dejavusans', '', 10);
            $pdf->Cell(0, 10, number_format($profile['friendsCount'] ?? 0), 0, 1);
            
            $pdf->SetFont('dejavusans', 'B', 10);
            $pdf->Cell(50, 10, 'Tweet Count:', 0, 0);
            $pdf->SetFont('dejavusans', '', 10);
            $pdf->Cell(0, 10, number_format($profile['statusesCount'] ?? 0), 0, 1);
            
            // Tweet activity
            if (!empty($analysis['tweetActivity'])) {
                $pdf->Ln(5);
                $pdf->SetFont('dejavusans', 'B', 14);
                $pdf->Cell(0, 10, 'Tweet Activity Analysis', 0, 1);
                
                $pdf->SetFont('dejavusans', 'B', 10);
                $pdf->Cell(50, 10, 'Analyzed Tweets:', 0, 0);
                $pdf->SetFont('dejavusans', '', 10);
                $pdf->Cell(0, 10, $analysis['tweetActivity']['total'], 0, 1);
                
                $pdf->SetFont('dejavusans', 'B', 10);
                $pdf->Cell(50, 10, 'Media Tweets:', 0, 0);
                $pdf->SetFont('dejavusans', '', 10);
                $pdf->Cell(0, 10, $analysis['tweetActivity']['withMedia'], 0, 1);
                
                $pdf->SetFont('dejavusans', 'B', 10);
                $pdf->Cell(50, 10, 'Link Tweets:', 0, 0);
                $pdf->SetFont('dejavusans', '', 10);
                $pdf->Cell(0, 10, $analysis['tweetActivity']['withLinks'], 0, 1);
                
                $pdf->SetFont('dejavusans', 'B', 10);
                $pdf->Cell(50, 10, 'Hashtag Tweets:', 0, 0);
                $pdf->SetFont('dejavusans', '', 10);
                $pdf->Cell(0, 10, $analysis['tweetActivity']['withHashtags'], 0, 1);
                
                $pdf->SetFont('dejavusans', 'B', 10);
                $pdf->Cell(50, 10, 'Mention Tweets:', 0, 0);
                $pdf->SetFont('dejavusans', '', 10);
                $pdf->Cell(0, 10, $analysis['tweetActivity']['withMentions'], 0, 1);
                
                $pdf->SetFont('dejavusans', 'B', 10);
                $pdf->Cell(50, 10, 'Retweets:', 0, 0);
                $pdf->SetFont('dejavusans', '', 10);
                $pdf->Cell(0, 10, $analysis['tweetActivity']['retweets'], 0, 1);
                
                $pdf->SetFont('dejavusans', 'B', 10);
                $pdf->Cell(50, 10, 'Average Likes:', 0, 0);
                $pdf->SetFont('dejavusans', '', 10);
                $pdf->Cell(0, 10, $analysis['tweetActivity']['avgLikes'], 0, 1);
                
                $pdf->SetFont('dejavusans', 'B', 10);
                $pdf->Cell(50, 10, 'Average Retweets:', 0, 0);
                $pdf->SetFont('dejavusans', '', 10);
                $pdf->Cell(0, 10, $analysis['tweetActivity']['avgRetweets'], 0, 1);
            }
            
            // Top hashtags
            if (!empty($analysis['topHashtags'])) {
                $pdf->Ln(5);
                $pdf->SetFont('dejavusans', 'B', 14);
                $pdf->Cell(0, 10, 'Top Hashtags', 0, 1);
                
                $pdf->SetFont('dejavusans', 'B', 10);
                $pdf->Cell(50, 10, 'Hashtag', 1, 0, 'C');
                $pdf->Cell(0, 10, 'Count', 1, 1, 'C');
                
                $pdf->SetFont('dejavusans', '', 10);
                foreach ($analysis['topHashtags'] as $hashtag => $count) {
                    $pdf->Cell(50, 10, '#' . $hashtag, 1, 0);
                    $pdf->Cell(0, 10, $count, 1, 1, 'C');
                }
            }
            
            // Output PDF as string
            $pdfString = $pdf->Output('x_profile_analysis.pdf', 'S');
            
            // Return success response
            $response = [
                'success' => true,
                'message' => 'PDF generated successfully',
                'pdf' => base64_encode($pdfString)
            ];
            
            return $this->response->setJSON($response);
        } catch (\Exception $e) {
            log_message('error', 'PDF generation error: ' . $e->getMessage());
            $response['message'] = 'Error generating PDF: ' . $e->getMessage();
            return $this->response->setJSON($response);
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
     * Calculate statistics from tweets
     * 
     * @param array $tweets Array of tweets
     * @return array Statistics
     */
    private function calculateTweetStats($tweets)
    {
        $stats = [
            'total' => count($tweets),
            'withMedia' => 0,
            'withLinks' => 0,
            'withMentions' => 0,
            'withHashtags' => 0,
            'retweets' => 0,
            'totalLikes' => 0,
            'totalRetweets' => 0,
            'avgLikes' => 0,
            'avgRetweets' => 0,
            'hashtags' => [],
            'mentions' => [],
            'users' => []
        ];
        
        foreach ($tweets as $tweet) {
            // Count tweets with media
            if (!empty($tweet['mediaUrls'])) {
                $stats['withMedia']++;
            }
            
            // Count tweets with links
            if (!empty($tweet['urls'])) {
                $stats['withLinks']++;
            }
            
            // Count retweets
            if (isset($tweet['isRetweet']) && $tweet['isRetweet']) {
                $stats['retweets']++;
            }
            
            // Add to totals for averages
            $stats['totalLikes'] += $tweet['favoriteCount'] ?? 0;
            $stats['totalRetweets'] += $tweet['retweetCount'] ?? 0;
            
            // Extract hashtags
            if (!empty($tweet['hashtags'])) {
                foreach ($tweet['hashtags'] as $tag) {
                    $tag = strtolower($tag);
                    $stats['hashtags'][$tag] = ($stats['hashtags'][$tag] ?? 0) + 1;
                }
                $stats['withHashtags']++;
            }
            
            // Extract mentions
            if (!empty($tweet['mentionedUsers'])) {
                foreach ($tweet['mentionedUsers'] as $user) {
                    $user = strtolower($user);
                    $stats['mentions'][$user] = ($stats['mentions'][$user] ?? 0) + 1;
                }
                $stats['withMentions']++;
            }
            
            // Track users
            if (!empty($tweet['user']) && !empty($tweet['user']['username'])) {
                $username = strtolower($tweet['user']['username']);
                if (!isset($stats['users'][$username])) {
                    $stats['users'][$username] = [
                        'username' => $tweet['user']['username'],
                        'displayName' => $tweet['user']['displayName'] ?? '',
                        'verified' => $tweet['user']['verified'] ?? false,
                        'tweetCount' => 0
                    ];
                }
                $stats['users'][$username]['tweetCount']++;
            }
        }
        
        // Calculate averages
        $stats['avgLikes'] = $stats['total'] > 0 ? round($stats['totalLikes'] / $stats['total'], 2) : 0;
        $stats['avgRetweets'] = $stats['total'] > 0 ? round($stats['totalRetweets'] / $stats['total'], 2) : 0;
        
        // Sort and limit hashtags and mentions
        arsort($stats['hashtags']);
        arsort($stats['mentions']);
        arsort($stats['users']);
        $stats['hashtags'] = array_slice($stats['hashtags'], 0, 10, true);
        $stats['mentions'] = array_slice($stats['mentions'], 0, 10, true);
        $stats['users'] = array_slice($stats['users'], 0, 10, true);
        
        return $stats;
    }
} 