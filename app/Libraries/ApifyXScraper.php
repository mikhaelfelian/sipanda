<?php
/**
 * ApifyXScraper Library
 * 
 * This library provides functionality to scrape X.com (formerly Twitter) using APIFY API.
 * It can be used from any controller to fetch tweets, profiles, and other data from X.com.
 *
 * @author    Mikhael Felian Waskito <mikhaelfelian@gmail.com>
 * @created   2025-04-30
 */

namespace App\Libraries;

class ApifyXScraper
{
    /**
     * APIFY API key
     *
     * @var string
     */
    protected $apiKey;
    
    /**
     * Base URL for APIFY API
     *
     * @var string
     */
    protected $baseUrl = 'https://api.apify.com/v2/';
    
    /**
     * Timeout in seconds for API requests
     *
     * @var int
     */
    protected $timeout = 120;
    
    /**
     * Constructor
     * 
     * @param string|null $apiKey APIFY API key
     */
    public function __construct($apiKey = null)
    {
        // If API key is provided, use it
        if (!empty($apiKey)) {
            $this->apiKey = $apiKey;
            return;
        }
        
        // Check if token is stored in the database
        $apiToken = $this->getStoredToken();
        
        // If token found in database, use it
        if (!empty($apiToken)) {
            $this->apiKey = $apiToken;
            return;
        }
        
        // Try environment variable or config
        $this->apiKey = getenv('APIFY_API_KEY') ?? config('App')->apifyApiKey ?? 'apify_api_OxNjPCt40Mf6Wuie3LimiXzmatDLNV1zG5tI';
        
        // Store the token in the database for future use
        if (!empty($this->apiKey)) {
            $this->storeToken($this->apiKey);
        } else {
            log_message('warning', 'APIFY API key is not set. X.com scraping will not work.');
        }
    }
    
    /**
     * Get stored API token from database
     * 
     * @return string|null API token
     */
    private function getStoredToken()
    {
        try {
            $db = \Config\Database::connect();
            $query = $db->table('tbl_api_tokens')
                        ->where('provider', 'apify')
                        ->where('is_active', 1)
                        ->get();
                        
            $result = $query->getRow();
            
            return $result ? $result->token : null;
        } catch (\Exception $e) {
            log_message('error', 'Error getting APIFY token: ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Store API token in database
     * 
     * @param string $token API token
     * @return bool Success status
     */
    private function storeToken($token)
    {
        try {
            $db = \Config\Database::connect();
            
            // Check if token exists
            $exists = $db->table('tbl_api_tokens')
                        ->where('provider', 'apify')
                        ->countAllResults() > 0;
            
            $data = [
                'provider' => 'apify',
                'token' => $token,
                'is_active' => 1,
                'updated_at' => date('Y-m-d H:i:s')
            ];
            
            if ($exists) {
                // Update existing token
                $result = $db->table('tbl_api_tokens')
                            ->where('provider', 'apify')
                            ->update($data);
            } else {
                // Add created_at for new records
                $data['created_at'] = date('Y-m-d H:i:s');
                
                // Insert new token
                $result = $db->table('tbl_api_tokens')
                            ->insert($data);
            }
            
            return $result;
        } catch (\Exception $e) {
            log_message('error', 'Error storing APIFY token: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Set API key
     * 
     * @param string $apiKey APIFY API key
     * @return $this
     */
    public function setApiKey($apiKey)
    {
        $this->apiKey = $apiKey;
        return $this;
    }
    
    /**
     * Get user profile by username
     * 
     * @param string $username X.com username (without @)
     * @return array|null User profile data or null on error
     */
    public function getUserProfile($username)
    {
        try {
            // Sanitize username (remove @ if present)
            $username = ltrim($username, '@');
            
            // Run the APIFY actor for X.com profile scraping
            $result = $this->runTask('apify/twitter-scraper', [
                'searchTerms' => [
                    ['username' => $username]
                ],
                'mode' => 'userProfile',
                'maxItems' => 1,
                'addUserInfo' => true
            ]);
            
            // Process the result to extract the user profile
            if (!empty($result) && is_array($result) && isset($result[0])) {
                return $result[0];
            }
            
            return null;
        } catch (\Exception $e) {
            log_message('error', 'Error fetching X profile: ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Search tweets by keyword or hashtag
     * 
     * @param string $query Search query (keyword or hashtag)
     * @param int $limit Maximum number of tweets to fetch
     * @return array Tweets matching the search query
     */
    public function searchTweets($query, $limit = 50)
    {
        try {
            // Run the APIFY actor for X.com tweet search
            $result = $this->runTask('apify/twitter-scraper', [
                'searchTerms' => [
                    ['term' => $query]
                ],
                'mode' => 'search',
                'maxItems' => $limit,
                'addUserInfo' => true
            ]);
            
            return $result ?? [];
        } catch (\Exception $e) {
            log_message('error', 'Error searching X tweets: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get tweets from a user's timeline
     * 
     * @param string $username X.com username (without @)
     * @param int $limit Maximum number of tweets to fetch
     * @return array Tweets from the user's timeline
     */
    public function getUserTweets($username, $limit = 50)
    {
        try {
            // Sanitize username (remove @ if present)
            $username = ltrim($username, '@');
            
            // Run the APIFY actor for X.com timeline scraping
            $result = $this->runTask('apify/twitter-scraper', [
                'searchTerms' => [
                    ['username' => $username]
                ],
                'mode' => 'userTimeline',
                'maxItems' => $limit,
                'addUserInfo' => true
            ]);
            
            return $result ?? [];
        } catch (\Exception $e) {
            log_message('error', 'Error fetching X user tweets: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get followers of a user
     * 
     * @param string $username X.com username (without @)
     * @param int $limit Maximum number of followers to fetch
     * @return array Followers data
     */
    public function getUserFollowers($username, $limit = 50)
    {
        try {
            // Sanitize username (remove @ if present)
            $username = ltrim($username, '@');
            
            // Run the APIFY actor for X.com followers scraping
            $result = $this->runTask('apify/twitter-scraper', [
                'searchTerms' => [
                    ['username' => $username]
                ],
                'mode' => 'userFollowers',
                'maxItems' => $limit,
                'addUserInfo' => true
            ]);
            
            return $result ?? [];
        } catch (\Exception $e) {
            log_message('error', 'Error fetching X user followers: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get users followed by a user
     * 
     * @param string $username X.com username (without @)
     * @param int $limit Maximum number of following users to fetch
     * @return array Following users data
     */
    public function getUserFollowing($username, $limit = 50)
    {
        try {
            // Sanitize username (remove @ if present)
            $username = ltrim($username, '@');
            
            // Run the APIFY actor for X.com following users scraping
            $result = $this->runTask('apify/twitter-scraper', [
                'searchTerms' => [
                    ['username' => $username]
                ],
                'mode' => 'userFriends',
                'maxItems' => $limit,
                'addUserInfo' => true
            ]);
            
            return $result ?? [];
        } catch (\Exception $e) {
            log_message('error', 'Error fetching X user following: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get trending topics
     * 
     * @param string $country Country code (e.g., 'US', 'ID')
     * @return array Trending topics
     */
    public function getTrendingTopics($country = '')
    {
        try {
            // Set up parameters for trending topics
            $params = [
                'mode' => 'trends',
                'maxItems' => 50
            ];
            
            // Add country filter if provided
            if (!empty($country)) {
                $params['country'] = strtoupper($country);
            }
            
            // Run the APIFY actor for X.com trending topics
            $result = $this->runTask('apify/twitter-scraper', $params);
            
            return $result ?? [];
        } catch (\Exception $e) {
            log_message('error', 'Error fetching X trending topics: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Analyze user profile for insights
     * 
     * @param string $username X.com username
     * @return array Analysis results
     */
    public function analyzeUserProfile($username)
    {
        $profile = $this->getUserProfile($username);
        $tweets = $this->getUserTweets($username, 100);
        
        if (empty($profile)) {
            return [
                'success' => false,
                'message' => 'User profile not found'
            ];
        }
        
        // Basic analysis stats
        $analysis = [
            'success' => true,
            'username' => $profile['username'] ?? $username,
            'displayName' => $profile['displayName'] ?? '',
            'bio' => $profile['bio'] ?? '',
            'followerCount' => $profile['followersCount'] ?? 0,
            'followingCount' => $profile['friendsCount'] ?? 0,
            'tweetCount' => $profile['statusesCount'] ?? 0,
            'accountCreated' => $profile['created'] ?? '',
            'isVerified' => $profile['verified'] ?? false,
            'profileImage' => $profile['profileImageUrl'] ?? '',
            'location' => $profile['location'] ?? '',
            'url' => $profile['url'] ?? '',
            'tweetActivity' => [
                'total' => count($tweets),
                'withMedia' => 0,
                'withLinks' => 0,
                'withMentions' => 0,
                'withHashtags' => 0,
                'retweets' => 0,
                'avgLikes' => 0,
                'avgRetweets' => 0
            ],
            'topHashtags' => [],
            'topMentions' => [],
            'mostEngagedTweet' => null
        ];
        
        // Skip further analysis if no tweets
        if (empty($tweets)) {
            return $analysis;
        }
        
        // Process tweets for statistics
        $totalLikes = 0;
        $totalRetweets = 0;
        $hashtags = [];
        $mentions = [];
        $mostEngagement = 0;
        
        foreach ($tweets as $tweet) {
            // Count tweets with media
            if (!empty($tweet['mediaUrls'])) {
                $analysis['tweetActivity']['withMedia']++;
            }
            
            // Count tweets with links
            if (!empty($tweet['urls'])) {
                $analysis['tweetActivity']['withLinks']++;
            }
            
            // Count retweets
            if (isset($tweet['isRetweet']) && $tweet['isRetweet']) {
                $analysis['tweetActivity']['retweets']++;
            }
            
            // Track engagement for most engaged tweet
            $engagement = ($tweet['retweetCount'] ?? 0) + ($tweet['favoriteCount'] ?? 0);
            if ($engagement > $mostEngagement) {
                $mostEngagement = $engagement;
                $analysis['mostEngagedTweet'] = $tweet;
            }
            
            // Add to totals for averages
            $totalLikes += $tweet['favoriteCount'] ?? 0;
            $totalRetweets += $tweet['retweetCount'] ?? 0;
            
            // Extract hashtags
            if (!empty($tweet['hashtags'])) {
                foreach ($tweet['hashtags'] as $tag) {
                    $hashtags[strtolower($tag)] = ($hashtags[strtolower($tag)] ?? 0) + 1;
                }
                $analysis['tweetActivity']['withHashtags']++;
            }
            
            // Extract mentions
            if (!empty($tweet['mentionedUsers'])) {
                foreach ($tweet['mentionedUsers'] as $user) {
                    $mentions[strtolower($user)] = ($mentions[strtolower($user)] ?? 0) + 1;
                }
                $analysis['tweetActivity']['withMentions']++;
            }
        }
        
        // Calculate averages
        $tweetCount = count($tweets);
        $analysis['tweetActivity']['avgLikes'] = $tweetCount > 0 ? round($totalLikes / $tweetCount, 2) : 0;
        $analysis['tweetActivity']['avgRetweets'] = $tweetCount > 0 ? round($totalRetweets / $tweetCount, 2) : 0;
        
        // Sort and limit top hashtags and mentions
        arsort($hashtags);
        arsort($mentions);
        $analysis['topHashtags'] = array_slice($hashtags, 0, 10, true);
        $analysis['topMentions'] = array_slice($mentions, 0, 10, true);
        
        return $analysis;
    }
    
    /**
     * Run an APIFY task or actor
     * 
     * @param string $actorId APIFY actor ID
     * @param array $input Input parameters for the actor
     * @return array|null Results from the actor run
     */
    protected function runTask($actorId, $input = [])
    {
        if (empty($this->apiKey)) {
            throw new \Exception('APIFY API key is not set');
        }
        
        // Set up the cURL request to run the actor
        $ch = curl_init($this->baseUrl . 'actor-tasks/' . $actorId . '/run-sync?token=' . $this->apiKey);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['contentType' => 'application/json', 'body' => $input]));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode >= 200 && $httpCode < 300) {
            $data = json_decode($response, true);
            return $data['data']['items'] ?? null;
        } else {
            log_message('error', 'APIFY API error: HTTP code ' . $httpCode . ', Response: ' . $response);
            throw new \Exception('APIFY API error: HTTP code ' . $httpCode);
        }
    }
} 