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
     * Constructor initializes the cache
     *
     * @param Psr16Adapter $cache
     */
    public function __construct(Psr16Adapter $cache = null)
    {
        parent::__construct($cache);
        self::$staticCache = $cache;
        self::$instance = $this;
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
            // Try using the parent method first
            return parent::searchAccountsByUsername($username, $count);
        } catch (\Error $e) {
            // Handle stdClass conversion error
            if (strpos($e->getMessage(), 'Object of class stdClass could not be converted to string') !== false) {
                log_message('error', 'Instagram API Error (stdClass conversion) in searchAccountsByUsername: ' . $e->getMessage());
                
                // Try a custom implementation as a fallback
                $response = self::request('web/search/topsearch/?context=blended&query=' . $username . '&rank_token=0.1234567890&include_reel=true');
                
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
            }
            
            // Re-throw if it's a different error
            throw $e;
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
     * @return \InstagramScraper\Model\Hashtag
     * @throws \Exception
     */
    public function getHashtag($hashtag)
    {
        try {
            return parent::getHashtag($hashtag);
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
} 