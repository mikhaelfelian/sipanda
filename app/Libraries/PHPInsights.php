<?php
/**
 * PHPInsights Library
 * 
 * This library provides sentiment analysis and word classification functions
 * using positive and negative word dictionaries
 * 
 * @author    Mikhael Felian Waskito <mikhaelfelian@gmail.com>
 * @created   2025-04-30
 */

namespace App\Libraries;

use App\Models\WordModel;

class PHPInsights
{
    /**
     * WordModel instance
     *
     * @var WordModel
     */
    protected $wordModel;
    
    /**
     * Cache for positive words
     *
     * @var array
     */
    protected $positiveWords = [];
    
    /**
     * Cache for negative words
     *
     * @var array
     */
    protected $negativeWords = [];
    
    /**
     * Constructor initializes dependencies
     */
    public function __construct()
    {
        $this->wordModel = new WordModel();
    }
    
    /**
     * Analyze sentiment of a text
     * 
     * @param string $text Text to analyze
     * @param string $language Language code (default: 'en')
     * @return array Analysis results with score and classification
     */
    public function analyzeSentiment($text, $language = 'en')
    {
        // Load words from database if not cached
        if (empty($this->positiveWords[$language])) {
            $this->positiveWords[$language] = $this->wordModel->getPositiveWords($language);
        }
        
        if (empty($this->negativeWords[$language])) {
            $this->negativeWords[$language] = $this->wordModel->getNegativeWords($language);
        }
        
        // Extract words from text
        $words = $this->extractWords($text);
        
        // Initialize counters
        $positiveScore = 0;
        $negativeScore = 0;
        $foundPositive = [];
        $foundNegative = [];
        
        // Check each word against dictionaries
        foreach ($words as $word) {
            // Check positive words
            foreach ($this->positiveWords[$language] as $positiveWord) {
                if (strtolower($word) === strtolower($positiveWord['word'])) {
                    $weight = $positiveWord['weight'] ?? 1;
                    $positiveScore += $weight;
                    $foundPositive[] = [
                        'word' => $positiveWord['word'],
                        'weight' => $weight
                    ];
                    break;
                }
            }
            
            // Check negative words
            foreach ($this->negativeWords[$language] as $negativeWord) {
                if (strtolower($word) === strtolower($negativeWord['word'])) {
                    $weight = $negativeWord['weight'] ?? 1;
                    $negativeScore += $weight;
                    $foundNegative[] = [
                        'word' => $negativeWord['word'],
                        'weight' => $weight
                    ];
                    break;
                }
            }
        }
        
        // Calculate final score
        $totalScore = $positiveScore - $negativeScore;
        
        // Determine sentiment classification
        $classification = 'neutral';
        if ($totalScore > 0) {
            $classification = 'positive';
        } elseif ($totalScore < 0) {
            $classification = 'negative';
        }
        
        // Return analysis results
        return [
            'text' => $text,
            'positive_score' => $positiveScore,
            'negative_score' => $negativeScore,
            'total_score' => $totalScore,
            'classification' => $classification,
            'positive_words' => $foundPositive,
            'negative_words' => $foundNegative,
            'word_count' => count($words)
        ];
    }
    
    /**
     * Extract words from text
     * 
     * @param string $text Text to extract words from
     * @return array List of words
     */
    protected function extractWords($text)
    {
        // Remove special characters and convert to lowercase
        $text = strtolower($text);
        $text = preg_replace('/[^\p{L}\p{N}\s]/u', ' ', $text);
        
        // Split text into words
        $words = preg_split('/\s+/', $text, -1, PREG_SPLIT_NO_EMPTY);
        
        return $words;
    }
    
    /**
     * Get word classification by category
     * 
     * @param string $category Category to filter by
     * @param string $language Language code (default: 'en')
     * @return array Words in the specified category
     */
    public function getWordsByCategory($category, $language = 'en')
    {
        return $this->wordModel->getWordsByCategory($category, $language);
    }
    
    /**
     * Analyze text for specific categories of words
     * 
     * @param string $text Text to analyze
     * @param array $categories Categories to look for
     * @param string $language Language code (default: 'en')
     * @return array Analysis results by category
     */
    public function analyzeByCategoriesNEW($text, array $categories, $language = 'en')
    {
        $results = [];
        $words = $this->extractWords($text);
        
        foreach ($categories as $category) {
            $categoryWords = $this->wordModel->getWordsByCategory($category, $language);
            $found = [];
            
            foreach ($words as $word) {
                foreach ($categoryWords as $categoryWord) {
                    if (strtolower($word) === strtolower($categoryWord['word'])) {
                        $found[] = [
                            'word' => $categoryWord['word'],
                            'status' => $categoryWord['status_word'] == 1 ? 'positive' : 'negative',
                            'weight' => $categoryWord['weight'] ?? 1
                        ];
                        break;
                    }
                }
            }
            
            $results[$category] = [
                'count' => count($found),
                'words' => $found
            ];
        }
        
        return $results;
    }
    
    /**
     * Compare sentiment between two texts
     * 
     * @param string $text1 First text to compare
     * @param string $text2 Second text to compare
     * @param string $language Language code (default: 'en')
     * @return array Comparison results
     */
    public function compareSentiment($text1, $text2, $language = 'en')
    {
        $analysis1 = $this->analyzeSentiment($text1, $language);
        $analysis2 = $this->analyzeSentiment($text2, $language);
        
        $scoreDifference = $analysis1['total_score'] - $analysis2['total_score'];
        
        return [
            'text1' => [
                'text' => $text1,
                'score' => $analysis1['total_score'],
                'classification' => $analysis1['classification']
            ],
            'text2' => [
                'text' => $text2,
                'score' => $analysis2['total_score'],
                'classification' => $analysis2['classification']
            ],
            'difference' => $scoreDifference,
            'more_positive' => $scoreDifference > 0 ? 'text1' : ($scoreDifference < 0 ? 'text2' : 'equal')
        ];
    }
    
    /**
     * Find most common categories in a text
     * 
     * @param string $text Text to analyze
     * @param string $language Language code (default: 'en')
     * @return array Category frequencies
     */
    public function findTopCategories($text, $language = 'en')
    {
        // Get all words with their categories
        $positiveWords = $this->wordModel->getPositiveWords($language);
        $negativeWords = $this->wordModel->getNegativeWords($language);
        $allWords = array_merge($positiveWords, $negativeWords);
        
        // Extract words from text
        $textWords = $this->extractWords($text);
        
        // Track category frequencies
        $categories = [];
        
        foreach ($textWords as $textWord) {
            foreach ($allWords as $word) {
                if (strtolower($textWord) === strtolower($word['word']) && !empty($word['category'])) {
                    $category = $word['category'];
                    if (!isset($categories[$category])) {
                        $categories[$category] = 0;
                    }
                    $categories[$category]++;
                    break;
                }
            }
        }
        
        // Sort by frequency
        arsort($categories);
        
        // Format results
        $result = [];
        foreach ($categories as $category => $count) {
            $result[] = [
                'category' => $category,
                'count' => $count
            ];
        }
        
        return $result;
    }
} 