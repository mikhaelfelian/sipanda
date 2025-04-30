<?php

namespace App\Libraries;

use \InstagramScraper\Instagram;
use \Phpfastcache\Helper\Psr16Adapter;

/**
 * InstaWrapper - extends Instagram PHP Scraper with safer methods
 * 
 * This class provides a wrapper around the Instagram PHP Scraper library
 * with additional error handling and safer implementations.
 */
class InstaWrapper extends Instagram
{
    /**
     * Static cache instance
     */
    private static $staticCache = null;
    
    /**
     * Instance for instance methods
     */
    private static $instance = null;
    
    /**
     * Cache for instance used in requests
     */
    private static $instanceCache = null;
    
    /**
     * API key for Instagram Meta API
     */
    private static $apiKey = null;

    /**
     * Constructor initializes the cache
     *
     * @param Psr16Adapter $cache
     */
    public function __construct(Psr16Adapter $cache = null)
    {
        parent::__construct($cache);
        self::$staticCache = $cache;
        self::$instance = $this;
        self::$instanceCache = $cache;
    }

    /**
     * Set the API key for Instagram Meta API
     *
     * @param string $apiKey The API key
     * @return void
     */
    public function setApiKey($apiKey)
    {
        self::$apiKey = $apiKey;
        log_message('info', 'Instagram Meta API key set via instance method');
    }
    
    /**
     * Static method to set the API key for Instagram Meta API
     *
     * @param string $apiKey The API key
     * @return void
     */
    public static function setApiKeyStatic($apiKey)
    {
        self::$apiKey = $apiKey;
        log_message('info', 'Instagram Meta API key set via static method');
    }

    /**
     * Get instance for non-static methods
     */
    public static function getInstance()
    {
        if (self::$instance === null) {
            // Create a new instance if needed
            self::$instance = new self(self::$staticCache);
        }
        return self::$instance;
    }

    /**
     * Safely search for accounts by username with added error handling
     * This overrides the parent method that has an issue with stdClass conversion
     *
     * @param string $username Username to search for
     * @param int $count Number of results to return
     * @return array Array of Account objects
     */
    public static function searchAccountsByUsername($username, $count = 10)
    {
        try {
            // If API key is available, try to use it for the search
            if (self::$apiKey) {
                try {
                    // Use the API key for the search
                    log_message('info', 'Using Instagram Meta API for username search: ' . $username);
                    
                    // Build the endpoint to use with API key
                    $endpoint = 'web/search/topsearch/?context=blended&query=' . $username . '&rank_token=0.1234567890&include_reel=true';
                    $response = self::requestWithApiKey($endpoint);
                    
                    // Process the response
                    if (isset($response->users) && is_array($response->users)) {
                        $accounts = [];
                        $usersCount = 0;
                        
                        foreach ($response->users as $user) {
                            if (isset($user->user) && $usersCount < $count) {
                                $accounts[] = self::mapUserFromSearch($user->user);
                                $usersCount++;
                            }
                        }
                        
                        return $accounts;
                    }
                    
                    // If no users found, fall back to the original method
                    log_message('info', 'No users found using API key, falling back to scraping');
                } catch (\Exception $apiException) {
                    // Log the API exception but continue with scraping fallback
                    log_message('error', 'Instagram Meta API error in searchAccountsByUsername: ' . $apiException->getMessage());
                }
            }
            
            // Try using the parent method as fallback
            try {
                return parent::searchAccountsByUsername($username, $count);
            } catch (\Error $e) {
                // Handle stdClass conversion error
                if (strpos($e->getMessage(), 'Object of class stdClass could not be converted to string') !== false) {
                    log_message('error', 'Instagram API Error (stdClass conversion) in searchAccountsByUsername: ' . $e->getMessage());
                    
                    // Try a custom implementation as a fallback
                    $instance = self::getInstance();
                    // Use the instance method we created
                    $endpoint = 'web/search/topsearch/?context=blended&query=' . $username . '&rank_token=0.1234567890&include_reel=true';
                    
                    try {
                        // Use our direct implementation of the request
                        $ch = curl_init();
                        $url = 'https://www.instagram.com/' . $endpoint;
                        
                        curl_setopt($ch, CURLOPT_URL, $url);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                        curl_setopt($ch, CURLOPT_HTTPHEADER, [
                            'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
                            'Accept: application/json'
                        ]);
                        
                        // Execute the request
                        $response = curl_exec($ch);
                        curl_close($ch);
                        
                        $response = json_decode($response);
                        
                        if (!isset($response->status) || $response->status !== 'ok') {
                            return [];
                        }
                        
                        $accounts = [];
                        $usersCount = 0;
                        
                        if (isset($response->users) && is_array($response->users)) {
                            foreach ($response->users as $user) {
                                if (isset($user->user) && $usersCount < $count) {
                                    $accounts[] = self::mapUserFromSearch($user->user);
                                    $usersCount++;
                                }
                            }
                        }
                        
                        return $accounts;
                    } catch (\Exception $requestException) {
                        log_message('error', 'Failed to search accounts: ' . $requestException->getMessage());
                        // Return empty array as last resort
                        return [];
                    }
                }
                
                // Re-throw if it's a different error
                throw $e;
            }
        } catch (\Exception $generalException) {
            // Handle any other exceptions
            log_message('error', 'General error in searchAccountsByUsername: ' . $generalException->getMessage());
            return [];
        }
    }
    /**
     * Helper method to map user data from search results to a simplified account object
     *
     * @param \stdClass $user User data from search
     * @return object Simple account object with basic properties
     */
    private static function mapUserFromSearch($user)
    {
        $account = new \stdClass();
        
        // Map basic properties
        $account->username = isset($user->username) ? $user->username : '';
        $account->fullName = isset($user->full_name) ? $user->full_name : '';
        $account->profilePicUrl = isset($user->profile_pic_url) ? $user->profile_pic_url : '';
        $account->isPrivate = isset($user->is_private) ? $user->is_private : false;
        $account->isVerified = isset($user->is_verified) ? $user->is_verified : false;
        $account->followersCount = isset($user->follower_count) ? $user->follower_count : 0;
        $account->followingCount = isset($user->following_count) ? $user->following_count : 0;
        $account->mediaCount = isset($user->media_count) ? $user->media_count : 0;
        
        // Add methods to access properties (to maintain compatibility with Account objects)
        $account->getUsername = function() use ($account) { return $account->username; };
        $account->getFullName = function() use ($account) { return $account->fullName; };
        $account->getProfilePicUrl = function() use ($account) { return $account->profilePicUrl; };
        $account->isPrivate = function() use ($account) { return $account->isPrivate; };
        $account->isVerified = function() use ($account) { return $account->isVerified; };
        $account->getFollowersCount = function() use ($account) { return $account->followersCount; };
        $account->getFollowingCount = function() use ($account) { return $account->followingCount; };
        $account->getMediaCount = function() use ($account) { return $account->mediaCount; };
        
        return $account;
    }
    
    /**
     * Static proxy for getHashtag - makes it easier to call without creating instance
     */
    public static function getHashtagStatic($hashtag)
    {
        return self::getInstance()->getHashtag($hashtag);
    }
    /**
     * Get hashtag information
     *
     * @param string $hashtag
     * @return \stdClass
     * @throws \Exception
     */
    public function getHashtag($hashtag)
    {
        try {
            // Since parent::getHashtag() doesn't exist, implement hashtag fetching directly
            $hashtag = trim(str_replace('#', '', $hashtag));
            $medias = $this->getMediasByTag($hashtag, 1);
            
            // Create a simple hashtag object with available information
            $hashtagObj = new \stdClass();
            $hashtagObj->name = $hashtag;
            $hashtagObj->mediaCount = count($medias) > 0 ? $medias[0]->getCommentsCount() : 0; // Approximation
            $hashtagObj->medias = $medias;
            
            // Add method to get media count for compatibility
            $hashtagObj->getMediaCount = function() use ($hashtagObj) {
                return $hashtagObj->mediaCount;
            };
            
            return $hashtagObj;
        } catch (\Exception $e) {
            log_message('error', 'Instagram API Error in getHashtag: ' . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Static proxy for getMediasByTag - makes it easier to call without creating instance
     */
    public static function getMediasByTagStatic($tag, $count = 12, $maxId = null, $minTimestamp = null, $maxTimestamp = null)
    {
        return self::getInstance()->getMediasByTag($tag, $count, $maxId, $minTimestamp, $maxTimestamp);
    }
    
    /**
     * Get media by tag name
     * 
     * @param string $tag Tag name
     * @param int $count Maximum number of media to return
     * @param string|null $maxId Instagram pagination ID
     * @param string|null $minTimestamp Minimum timestamp
     * @param string|null $maxTimestamp Maximum timestamp
     * @return array
     * @throws \Exception
     */
    public function getMediasByTag($tag, $count = 12, $maxId = null, $minTimestamp = null, $maxTimestamp = null)
    {
        try {
            return parent::getMediasByTag($tag, $count, $maxId, $minTimestamp, $maxTimestamp);
        } catch (\Exception $e) {
            log_message('error', 'Instagram API Error in getMediasByTag: ' . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Get account information
     *
     * @param string $username
     * @return \InstagramScraper\Model\Account
     * @throws \Exception
     */
    public static function getAccount($username)
    {
        try {
            return parent::getAccount($username);
        } catch (\Exception $e) {
            log_message('error', 'Instagram API Error in getAccount: ' . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Get account media
     *
     * @param string $username
     * @param int $count
     * @param string|null $maxId
     * @return array
     * @throws \Exception
     */
    public static function getMedias($username, $count = 20, $maxId = null)
    {
        try {
            return parent::getMedias($username, $count, $maxId);
        } catch (\Exception $e) {
            log_message('error', 'Instagram API Error in getMedias: ' . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Get media by URL
     *
     * @param string $url
     * @return \InstagramScraper\Model\Media
     * @throws \Exception
     */
    public static function getMediaByUrl($url)
    {
        try {
            return parent::getMediaByUrl($url);
        } catch (\Exception $e) {
            log_message('error', 'Instagram API Error in getMediaByUrl: ' . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Get media comments by shortcode
     *
     * @param string $shortcode
     * @param int $count
     * @param string|null $maxId
     * @return array
     * @throws \Exception
     */
    public static function getMediaCommentsByCode($shortcode, $count = 10, $maxId = null)
    {
        try {
            return parent::getMediaCommentsByCode($shortcode, $count, $maxId);
        } catch (\Exception $e) {
            log_message('error', 'Instagram API Error in getMediaCommentsByCode: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Override the parent request method to handle missing 'Set-Cookie' header
     * and use API key if available
     * 
     * @param string $endpoint
     * @param bool $session
     * @param string|null $username
     * @param string|null $password
     * @return \stdClass
     * @throws \Exception
     */
    public static function request($endpoint, $session = false, $username = null, $password = null)
    {
        try {
            // If we have an API key, use the Meta API instead
            if (self::$apiKey) {
                log_message('info', 'Using Instagram Meta API for request: ' . $endpoint);
                return self::requestWithApiKey($endpoint);
            }
            
            // Try to use a safer approach than parent::request
            // This avoids the Set-Cookie error by handling headers safely
            try {
                // Make the request directly to avoid parent class error
                $ch = curl_init();
                
                $url = 'https://www.instagram.com/';
                if (substr($endpoint, 0, 8) === 'https://') {
                    $url = $endpoint;
                } else if (substr($endpoint, 0, 3) === 'api') {
                    // If it's an API endpoint (starting with 'api'), keep it as is
                    $url = 'https://www.instagram.com/' . $endpoint;
                } else if (substr($endpoint, 0, 1) === '/') {
                    // If it starts with a slash, strip it
                    $url = 'https://www.instagram.com' . $endpoint;
                } else {
                    // Otherwise add api/v1/ prefix for Instagram API endpoints
                    $url = 'https://www.instagram.com/api/v1/' . $endpoint;
                }
                
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                curl_setopt($ch, CURLOPT_HEADER, true); // Get headers in response
                
                // Set user agent
                $userAgent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36';
                curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);
                
                // Set headers
                $headers = [
                    'Accept: application/json',
                    'Accept-Language: en-US,en;q=0.9',
                    'Origin: https://www.instagram.com',
                    'Referer: https://www.instagram.com/'
                ];
                
                // Set cookie if session required
                if ($session) {
                    $headers[] = 'Cookie: ig_cb=1; ig_did=BF4C1D83-0C43-45D0-8A51-5DCF7D5DB760;';
                }
                
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                
                // Execute request
                $response = curl_exec($ch);
                
                // Check for errors
                if (curl_errno($ch)) {
                    throw new \Exception('Error fetching data: ' . curl_error($ch));
                }
                
                // Get HTTP response code
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                
                // Get header size
                $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
                
                curl_close($ch);
                
                // Handle response based on HTTP code
                if ($httpCode !== 200) {
                    throw new \Exception("Request failed with HTTP code $httpCode");
                }
                
                // Split headers and body
                $headerContent = substr($response, 0, $headerSize);
                $body = substr($response, $headerSize);
                
                // Parse JSON response
                $obj = json_decode($body);
                if ($obj === null) {
                    throw new \Exception('Invalid JSON response');
                }
                
                return $obj;
            } catch (\Exception $requestException) {
                // Log the exception but try the failsafe method
                log_message('error', 'Error in safe request method: ' . $requestException->getMessage());
                
                // Don't fall through here - use the requestWithoutCookieProcessing method
                return self::requestWithoutCookieProcessing($endpoint, $session, $username, $password);
            }
            
        } catch (\ErrorException $e) {
            // Handle specifically the "Undefined array key 'Set-Cookie'" error
            if (strpos($e->getMessage(), "Undefined array key 'Set-Cookie'") !== false) {
                log_message('warning', 'Handling missing Set-Cookie header: ' . $e->getMessage());
                
                // Attempt to make the request without cookie processing
                return self::requestWithoutCookieProcessing($endpoint, $session, $username, $password);
            }
            
            // Re-throw any other errors
            throw $e;
        }
    }
    
    /**
     * Modified request method that doesn't rely on the 'Set-Cookie' header
     * 
     * @param string $endpoint
     * @param bool $session
     * @param string|null $username
     * @param string|null $password
     * @return \stdClass
     * @throws \Exception
     */
    private static function requestWithoutCookieProcessing($endpoint, $session = false, $username = null, $password = null)
    {
        // Use the staticCache instead of instanceCache
        if (!self::$staticCache) {
            self::$staticCache = new Psr16Adapter('Files');
        }
        
        $headers = [
            'user-agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
            'cookie' => 'ig_cb=1; ig_did=BF4C1D83-0C43-45D0-8A51-5DCF7D5DB760;',
            'referer' => 'https://www.instagram.com/',
            'x-instagram-ajax' => '1',
            'origin' => 'https://www.instagram.com'
        ];
        
        $ch = curl_init();
        
        // Process the URL in the same way as the main request method
        $url = 'https://www.instagram.com/';
        if (substr($endpoint, 0, 8) === 'https://') {
            $url = $endpoint;
        } else if (substr($endpoint, 0, 3) === 'api') {
            // If it's an API endpoint (starting with 'api'), keep it as is
            $url = 'https://www.instagram.com/' . $endpoint;
        } else if (substr($endpoint, 0, 1) === '/') {
            // If it starts with a slash, strip it
            $url = 'https://www.instagram.com' . $endpoint;
        } else {
            // Otherwise add api/v1/ prefix for Instagram API endpoints
            $url = 'https://www.instagram.com/api/v1/' . $endpoint;
        }
        
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array_map(function($k, $v) {
            return "$k: $v";
        }, array_keys($headers), $headers));
        
        // Execute the request
        $response = curl_exec($ch);
        
        // Check for errors
        if (curl_errno($ch)) {
            throw new \Exception('Error fetching data: ' . curl_error($ch));
        }
        
        // Get HTTP response code
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        curl_close($ch);
        
        // Handle response based on HTTP code
        if ($httpCode !== 200) {
            throw new \Exception("Request failed with HTTP code $httpCode");
        }
        
        $obj = json_decode($response);
        
        // Handle invalid JSON response
        if ($obj === null) {
            throw new \Exception('Invalid JSON response');
        }
        
        return $obj;
    }
    
    /**
     * Make a request using the Instagram Meta API key
     * 
     * @param string $endpoint
     * @return \stdClass
     * @throws \Exception
     */
    private static function requestWithApiKey($endpoint)
    {
        // Extract the request type from the endpoint
        $type = 'unknown';
        if (strpos($endpoint, 'search') !== false) {
            $type = 'search';
        } else if (strpos($endpoint, 'user') !== false || strpos($endpoint, 'username') !== false) {
            $type = 'user';
        } else if (strpos($endpoint, 'media') !== false) {
            $type = 'media';
        } else if (strpos($endpoint, 'tag') !== false || strpos($endpoint, 'hashtag') !== false) {
            $type = 'hashtag';
        }
        
        // Log the request type for debugging
        log_message('info', 'Instagram Meta API request type: ' . $type);
        
        // Parse the endpoint to build the Meta API URL
        // The Instagram Graph API endpoints are different from the web scraper endpoints
        // Here we're adapting our endpoint pattern to match the Meta API
        
        // Base URL for Instagram Graph API
        $baseUrl = 'https://graph.instagram.com/';
        
        // Add API version
        $apiVersion = 'v13.0/';
        
        // Build the full URL with the API key
        $url = $baseUrl . $apiVersion;
        
        // Determine the specific endpoint based on the request type
        switch ($type) {
            case 'search':
                // Extract the query from the endpoint (simplified for this example)
                $query = '';
                if (preg_match('/query=([^&]+)/', $endpoint, $matches)) {
                    $query = urldecode($matches[1]);
                }
                
                // For search, use the Facebook Graph API search endpoint
                $url .= 'ig_hashtag_search?q=' . urlencode($query) . '&access_token=' . self::$apiKey;
                break;
                
            case 'user':
                // Extract username if present
                $username = '';
                if (preg_match('/username=([^&]+)/', $endpoint, $matches)) {
                    $username = $matches[1];
                }
                
                // For user info, use the /me endpoint or search for the user
                if (empty($username)) {
                    $url .= 'me?fields=id,username,name,profile_picture_url,biography,followers_count,follows_count,media_count&access_token=' . self::$apiKey;
                } else {
                    // Note: Direct username lookup requires a Business/Creator account and permissions
                    // This is a simplified approach and may not work without proper permissions
                    $url .= 'ig_username/' . $username . '?fields=id,username,name,profile_picture_url,biography,followers_count,follows_count,media_count&access_token=' . self::$apiKey;
                }
                break;
                
            case 'media':
                // For media, use the /media endpoint
                // Extract the media ID or shortcode if present
                $mediaId = '';
                if (preg_match('/media\/([^\/]+)/', $endpoint, $matches)) {
                    $mediaId = $matches[1];
                }
                
                if (!empty($mediaId)) {
                    $url .= $mediaId . '?fields=id,caption,media_type,media_url,permalink,thumbnail_url,timestamp,username&access_token=' . self::$apiKey;
                } else {
                    // If no specific media ID, fetch user media
                    $url .= 'me/media?fields=id,caption,media_type,media_url,permalink,thumbnail_url,timestamp,username&access_token=' . self::$apiKey;
                }
                break;
                
            case 'hashtag':
                // Extract the hashtag from the endpoint
                $hashtag = '';
                if (preg_match('/tag\/([^\/]+)/', $endpoint, $matches)) {
                    $hashtag = $matches[1];
                }
                
                if (!empty($hashtag)) {
                    // First get the hashtag ID, then get the media
                    // This is a two-step process in the Graph API
                    $hashtagIdUrl = $baseUrl . $apiVersion . 'ig_hashtag_search?q=' . urlencode($hashtag) . '&access_token=' . self::$apiKey;
                    
                    $hashtagResponse = self::makeApiRequest($hashtagIdUrl);
                    
                    if (isset($hashtagResponse->data) && is_array($hashtagResponse->data) && count($hashtagResponse->data) > 0) {
                        $hashtagId = $hashtagResponse->data[0]->id;
                        
                        // Now get the media for this hashtag
                        $url = $baseUrl . $apiVersion . $hashtagId . '/recent_media?fields=id,caption,media_type,media_url,permalink,thumbnail_url,timestamp,username&access_token=' . self::$apiKey;
                    } else {
                        throw new \Exception("Hashtag not found: " . $hashtag);
                    }
                } else {
                    throw new \Exception("Hashtag parameter is required");
                }
                break;
                
            default:
                // For other endpoints, try to use the raw endpoint with the API key
                $url .= $endpoint;
                if (strpos($url, '?') !== false) {
                    $url .= '&access_token=' . self::$apiKey;
                } else {
                    $url .= '?access_token=' . self::$apiKey;
                }
                break;
        }
        
        // Make the actual API request
        return self::makeApiRequest($url);
    }
    
    /**
     * Make an API request to the given URL
     * 
     * @param string $url
     * @return \stdClass
     * @throws \Exception
     */
    private static function makeApiRequest($url)
    {
        $ch = curl_init();
        
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Accept: application/json',
            'User-Agent: SIPANDA Instagram Tool/1.0'
        ]);
        
        // Execute the request
        $response = curl_exec($ch);
        
        // Check for errors
        if (curl_errno($ch)) {
            throw new \Exception('Error fetching data: ' . curl_error($ch));
        }
        
        // Get HTTP response code
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        curl_close($ch);
        
        // Handle response based on HTTP code
        if ($httpCode !== 200) {
            throw new \Exception("API request failed with HTTP code $httpCode: $response");
        }
        
        $obj = json_decode($response);
        
        // Handle invalid JSON response
        if ($obj === null) {
            throw new \Exception('Invalid JSON response: ' . $response);
        }
        
        return $obj;
    }
} 