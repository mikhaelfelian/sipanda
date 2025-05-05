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
     * X API key
     *
     * @var string
     */
    protected $xApiKey;
    
    /**
     * X API secret
     *
     * @var string
     */
    protected $xApiSecret;
    
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
            $tokenModel = new \App\Models\ApiTokenModel();
            return $tokenModel->getActiveToken('apify');
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
            $tokenModel = new \App\Models\ApiTokenModel();
            return $tokenModel->storeToken(
                'apify', 
                $token, 
                'APIFY API token for X.com scraping'
            );
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
     * Set X API credentials
     * 
     * @param string $apiKey X API key
     * @param string $apiSecret X API secret
     * @return $this
     */
    public function setXApiCredentials($apiKey, $apiSecret)
    {
        $this->xApiKey = $apiKey;
        $this->xApiSecret = $apiSecret;
        return $this;
    }
    
    /**
     * Check if X API credentials are set
     * 
     * @return bool
     */
    public function hasXApiCredentials()
    {
        return !empty($this->xApiKey) && !empty($this->xApiSecret);
    }
    
    /**
     * Get user profile directly from X API if credentials are available
     * Falls back to APIFY if not
     * 
     * @param string $username X.com username (without @)
     * @return array|null User profile data or null on error
     */
    public function getUserProfile($username)
    {
        // Try to get profile using X API if credentials are available
        if ($this->hasXApiCredentials()) {
            $profile = $this->getUserProfileFromXApi($username);
            if ($profile !== null) {
                return $profile;
            }
        }
        
        // Fall back to APIFY if X API fails or credentials not available
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
     * Get user profile using X API v2
     * 
     * @param string $username X.com username (without @)
     * @return array|null User profile data or null on error
     */
    protected function getUserProfileFromXApi($username)
    {
        try {
            // Sanitize username (remove @ if present)
            $username = ltrim($username, '@');
            
            // Bearer token is required for X API
            $bearerToken = $this->getXBearerToken();
            if (empty($bearerToken)) {
                log_message('error', 'Failed to obtain X API bearer token');
                return $this->getDefaultUserProfile($username);
            }
            
            // Endpoint for user lookup by username
            $url = "https://api.twitter.com/2/users/by/username/{$username}?user.fields=description,created_at,location,profile_image_url,public_metrics,url,verified";
            
            // Set up cURL request
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: Bearer ' . $bearerToken,
                'Content-Type: application/json'
            ]);
            
            // Execute request
            $response = curl_exec($ch);
            $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            // Check for successful response
            if ($statusCode == 200) {
                $data = json_decode($response, true);
                
                // Map X API response to our expected format
                if (isset($data['data'])) {
                    $userData = $data['data'];
                    
                    return [
                        'username' => $userData['username'],
                        'displayName' => $userData['name'],
                        'description' => $userData['description'] ?? '',
                        'verified' => $userData['verified'] ?? false,
                        'profileImageUrl' => $userData['profile_image_url'] ?? '',
                        'location' => $userData['location'] ?? '',
                        'url' => $userData['url'] ?? '',
                        'followersCount' => $userData['public_metrics']['followers_count'] ?? 0,
                        'followingCount' => $userData['public_metrics']['following_count'] ?? 0,
                        'statusesCount' => $userData['public_metrics']['tweet_count'] ?? 0,
                        'createdAt' => $userData['created_at'] ?? '',
                    ];
                }
            } else {
                log_message('error', 'X API error: ' . $response);
            }
            
            // Return default profile if user not found
            return $this->getDefaultUserProfile($username);
        } catch (\Exception $e) {
            log_message('error', 'Error fetching X profile via API: ' . $e->getMessage());
            return $this->getDefaultUserProfile($username);
        }
    }
    
    /**
     * Get default user profile when real profile can't be fetched
     * 
     * @param string $username X.com username (without @)
     * @return array Default profile data
     */
    protected function getDefaultUserProfile($username)
    {
        return [
            'username' => $username,
            'displayName' => 'Unknown User',
            'description' => 'This profile temporarily could not be accessed. The profile may exist on X.com but our system is currently unable to retrieve the data.',
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
    }
    
    /**
     * Get bearer token for X API using app credentials
     * 
     * @return string|null Bearer token or null on error
     */
    protected function getXBearerToken()
    {
        try {
            // Check if we have API credentials
            if (!$this->hasXApiCredentials()) {
                return null;
            }
            
            // Endpoint for obtaining bearer token
            $url = 'https://api.twitter.com/oauth2/token';
            
            // Create credentials string and encode
            $credentials = base64_encode($this->xApiKey . ':' . $this->xApiSecret);
            
            // Set up cURL request
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, 'grant_type=client_credentials');
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: Basic ' . $credentials,
                'Content-Type: application/x-www-form-urlencoded;charset=UTF-8'
            ]);
            
            // Execute request
            $response = curl_exec($ch);
            $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            // Check for successful response
            if ($statusCode == 200) {
                $data = json_decode($response, true);
                
                if (isset($data['access_token'])) {
                    return $data['access_token'];
                }
            } else {
                log_message('error', 'Failed to get X API bearer token: ' . $response);
            }
            
            return null;
        } catch (\Exception $e) {
            log_message('error', 'Error getting X API bearer token: ' . $e->getMessage());
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
     * Run an APIFY actor with the given input
     * 
     * @param string $actorId APIFY actor ID
     * @param array $input Input parameters for the actor
     * @return array|null Result of the APIFY actor run
     * @throws \Exception If an error occurs
     */
    protected function runTask($actorId, $input = [])
    {
        try {
            // Check if API key is set
            if (empty($this->apiKey)) {
                log_message('error', 'APIFY API key is not set. Cannot run task.');
                throw new \Exception('API key not set. Please set up your APIFY API key.');
            }
            
            // Prepare URL for the APIFY actor run
            $url = $this->baseUrl . "acts/{$actorId}/runs";
            
            // Prepare input data
            $data = [
                'token' => $this->apiKey,
                'json' => json_encode($input)
            ];
            
            // Set up cURL request
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
            
            // Execute request
            $response = curl_exec($ch);
            $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            
            // Check for cURL errors
            if (curl_errno($ch)) {
                $error = curl_error($ch);
                curl_close($ch);
                log_message('error', "APIFY cURL error: {$error}");
                throw new \Exception("Network error: {$error}");
            }
            
            curl_close($ch);
            
            // Check for successful response
            if ($statusCode != 200 && $statusCode != 201) {
                log_message('error', "APIFY API error: Status code {$statusCode}, Response: {$response}");
                
                // Try to extract a meaningful error message
                $errorData = json_decode($response, true);
                $errorMessage = isset($errorData['error']['message']) 
                    ? $errorData['error']['message'] 
                    : "API returned status code {$statusCode}";
                    
                throw new \Exception("API error: {$errorMessage}");
            }
            
            // Decode response
            $data = json_decode($response, true);
            
            // Check if the run was started successfully
            if (!isset($data['data']['id'])) {
                log_message('error', "APIFY run not started: " . json_encode($data));
                throw new \Exception('Failed to start APIFY task.');
            }
            
            // Get run ID
            $runId = $data['data']['id'];
            
            // Wait for the run to complete
            return $this->waitForTaskCompletion($runId);
        } catch (\Exception $e) {
            log_message('error', 'Error running APIFY task: ' . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Wait for an APIFY run to complete and fetch the results
     * 
     * @param string $runId APIFY run ID
     * @return array|null Results from the run
     * @throws \Exception If an error occurs
     */
    protected function waitForTaskCompletion($runId)
    {
        // Maximum number of attempts to check status
        $maxAttempts = 30;
        
        // Interval between status checks in seconds
        $checkInterval = 3;
        
        // URL for checking run status
        $statusUrl = $this->baseUrl . "actor-runs/{$runId}?token={$this->apiKey}";
        
        // URL for getting run results
        $datasetUrl = $this->baseUrl . "actor-runs/{$runId}/dataset/items?token={$this->apiKey}";
        
        // Loop to check run status
        for ($attempt = 0; $attempt < $maxAttempts; $attempt++) {
            // Wait before next check
            if ($attempt > 0) {
                sleep($checkInterval);
            }
            
            // Check run status
            $ch = curl_init($statusUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($ch);
            $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            // Check for successful response
            if ($statusCode != 200) {
                log_message('error', "Error checking APIFY run status: Status code {$statusCode}, Response: {$response}");
                continue;
            }
            
            // Parse status data
            $statusData = json_decode($response, true);
            
            // Check if run has finished
            if (isset($statusData['data']['status']) && in_array($statusData['data']['status'], ['SUCCEEDED', 'FAILED', 'TIMED_OUT', 'ABORTED'])) {
                // If run failed, log error and return null
                if ($statusData['data']['status'] !== 'SUCCEEDED') {
                    log_message('error', "APIFY run {$runId} failed with status: " . $statusData['data']['status']);
                    throw new \Exception("Task failed with status: " . $statusData['data']['status']);
                }
                
                // Get results
                $ch = curl_init($datasetUrl);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $response = curl_exec($ch);
                $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);
                
                // Check for successful response
                if ($statusCode != 200) {
                    log_message('error', "Error fetching APIFY run results: Status code {$statusCode}, Response: {$response}");
                    throw new \Exception("Error fetching results: Status code {$statusCode}");
                }
                
                // Parse and return results
                $results = json_decode($response, true);
                return $results ?? [];
            }
        }
        
        // If we've reached the maximum number of attempts, log error and throw exception
        log_message('error', "APIFY run {$runId} did not complete within the maximum wait time");
        throw new \Exception("Task did not complete within the maximum wait time");
    }
} 