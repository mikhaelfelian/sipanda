<?php

namespace App\Libraries;

class OsintAnalyzer
{
    protected $serpApi;
    protected $keywordModel;
    protected $trustScore = 0;
    protected $minTrustScore = 70; // Minimum trust score threshold

    public function __construct()
    {
        $this->serpApi = new SerpApi();
        $this->keywordModel = new \App\Models\KeywordModel();
    }

    /**
     * Analyze social media trends with Zero Trust methodology
     * 
     * @param string $query
     * @return array
     */
    public function analyzeTrend(string $query): array
    {
        // Initialize trust verification
        $this->trustScore = 0;
        
        // 1. Source Verification
        $sources = $this->verifySources($query);
        $this->trustScore += $sources['trust_score'];

        // 2. Content Analysis
        $content = $this->analyzeContent($query);
        $this->trustScore += $content['trust_score'];

        // 3. Trend Validation
        $trends = $this->validateTrend($query);
        $this->trustScore += $trends['trust_score'];

        // 4. Threat Assessment
        $threats = $this->assessThreats($query);
        $this->trustScore += $threats['trust_score'];

        return [
            'query' => $query,
            'trust_score' => $this->trustScore,
            'is_trusted' => $this->trustScore >= $this->minTrustScore,
            'sources' => $sources,
            'content' => $content,
            'trends' => $trends,
            'threats' => $threats,
            'recommendations' => $this->generateRecommendations()
        ];
    }

    /**
     * Verify sources using Zero Trust methodology
     * 
     * @param string $query
     * @return array
     */
    protected function verifySources(string $query): array
    {
        $trustScore = 0;
        $verifiedSources = [];
        $unverifiedSources = [];

        // Search across multiple platforms
        $results = $this->serpApi->search($query, [
            'engine' => 'google',
            'gl' => 'id',
            'hl' => 'id',
            'num' => 20
        ]);

        foreach ($results['organic_results'] as $result) {
            $sourceScore = $this->calculateSourceTrustScore($result);
            
            if ($sourceScore >= 70) {
                $trustScore += 5;
                $verifiedSources[] = [
                    'url' => $result['link'],
                    'title' => $result['title'],
                    'trust_score' => $sourceScore
                ];
            } else {
                $unverifiedSources[] = [
                    'url' => $result['link'],
                    'title' => $result['title'],
                    'trust_score' => $sourceScore
                ];
            }
        }

        return [
            'trust_score' => min($trustScore, 25), // Max 25 points for sources
            'verified_sources' => $verifiedSources,
            'unverified_sources' => $unverifiedSources
        ];
    }

    /**
     * Analyze content for potential threats
     * 
     * @param string $query
     * @return array
     */
    protected function analyzeContent(string $query): array
    {
        $trustScore = 0;
        $threatIndicators = [];
        $safeContent = [];

        $results = $this->serpApi->search($query, [
            'engine' => 'google',
            'gl' => 'id',
            'hl' => 'id',
            'num' => 20
        ]);

        foreach ($results['organic_results'] as $result) {
            $contentScore = $this->analyzeContentTrust($result);
            
            if ($contentScore >= 70) {
                $trustScore += 5;
                $safeContent[] = [
                    'url' => $result['link'],
                    'title' => $result['title'],
                    'trust_score' => $contentScore
                ];
            } else {
                $threatIndicators[] = [
                    'url' => $result['link'],
                    'title' => $result['title'],
                    'trust_score' => $contentScore,
                    'indicators' => $this->identifyThreatIndicators($result)
                ];
            }
        }

        return [
            'trust_score' => min($trustScore, 25), // Max 25 points for content
            'safe_content' => $safeContent,
            'threat_indicators' => $threatIndicators
        ];
    }

    /**
     * Validate trend authenticity
     * 
     * @param string $query
     * @return array
     */
    protected function validateTrend(string $query): array
    {
        $trustScore = 0;
        $trendData = [];

        // Get historical data
        $historicalSearches = $this->keywordModel->where('keyword', $query)
            ->orderBy('created_at', 'DESC')
            ->findAll();

        if (count($historicalSearches) > 0) {
            $trustScore += 10; // Points for historical data
            $trendData['historical'] = $historicalSearches;
        }

        // Analyze trend patterns
        $trendPattern = $this->analyzeTrendPattern($historicalSearches);
        $trustScore += $trendPattern['trust_score'];
        $trendData['pattern'] = $trendPattern;

        return [
            'trust_score' => min($trustScore, 25), // Max 25 points for trend
            'data' => $trendData
        ];
    }

    /**
     * Assess potential threats
     * 
     * @param string $query
     * @return array
     */
    protected function assessThreats(string $query): array
    {
        $trustScore = 0;
        $threats = [];

        // Check for known threat patterns
        $threatPatterns = $this->checkThreatPatterns($query);
        if (empty($threatPatterns)) {
            $trustScore += 25; // Full points if no threats detected
        } else {
            $threats['patterns'] = $threatPatterns;
        }

        return [
            'trust_score' => $trustScore,
            'threats' => $threats
        ];
    }

    /**
     * Calculate source trust score
     * 
     * @param array $source
     * @return int
     */
    protected function calculateSourceTrustScore(array $source): int
    {
        $score = 0;
        
        // Check domain reputation
        if ($this->isTrustedDomain($source['link'])) {
            $score += 40;
        }
        
        // Check content quality
        if (strlen($source['snippet']) > 100) {
            $score += 30;
        }
        
        // Check for HTTPS
        if (strpos($source['link'], 'https://') === 0) {
            $score += 30;
        }
        
        return $score;
    }

    /**
     * Analyze content trust
     * 
     * @param array $content
     * @return int
     */
    protected function analyzeContentTrust(array $content): int
    {
        $score = 0;
        
        // Check for suspicious keywords
        if (!$this->containsSuspiciousKeywords($content['title'] . ' ' . $content['snippet'])) {
            $score += 50;
        }
        
        // Check content length
        if (strlen($content['snippet']) > 150) {
            $score += 30;
        }
        
        // Check for professional language
        if ($this->isProfessionalLanguage($content['snippet'])) {
            $score += 20;
        }
        
        return $score;
    }

    /**
     * Generate security recommendations
     * 
     * @return array
     */
    protected function generateRecommendations(): array
    {
        $recommendations = [];

        if ($this->trustScore < $this->minTrustScore) {
            $recommendations[] = [
                'level' => 'high',
                'message' => 'Low trust score detected. Exercise caution with this trend.',
                'actions' => [
                    'Verify sources independently',
                    'Check for official statements',
                    'Monitor for updates'
                ]
            ];
        }

        return $recommendations;
    }

    /**
     * Check if domain is trusted
     * 
     * @param string $url
     * @return bool
     */
    protected function isTrustedDomain(string $url): bool
    {
        $trustedDomains = [
            'twitter.com',
            'facebook.com',
            'instagram.com',
            'linkedin.com',
            'medium.com',
            'wikipedia.org'
        ];

        $domain = parse_url($url, PHP_URL_HOST);
        return in_array($domain, $trustedDomains);
    }

    /**
     * Check for suspicious keywords
     * 
     * @param string $text
     * @return bool
     */
    protected function containsSuspiciousKeywords(string $text): bool
    {
        $suspiciousKeywords = [
            'hack', 'attack', 'breach', 'leak', 'vulnerability',
            'exploit', 'malware', 'phishing', 'scam', 'fraud'
        ];

        foreach ($suspiciousKeywords as $keyword) {
            if (stripos($text, $keyword) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if language is professional
     * 
     * @param string $text
     * @return bool
     */
    protected function isProfessionalLanguage(string $text): bool
    {
        $unprofessionalPatterns = [
            '/\b(omg|wtf|lol|rofl)\b/i',
            '/\!{3,}/',
            '/\?{3,}/',
            '/\b(shit|fuck|damn)\b/i'
        ];

        foreach ($unprofessionalPatterns as $pattern) {
            if (preg_match($pattern, $text)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Analyze trend patterns from historical data
     * 
     * @param array $historicalSearches
     * @return array
     */
    protected function analyzeTrendPattern(array $historicalSearches): array
    {
        $trustScore = 0;
        $patternAnalysis = [
            'is_consistent' => false,
            'trend_direction' => 'stable',
            'spike_detected' => false,
            'anomalies' => []
        ];

        if (empty($historicalSearches)) {
            return [
                'trust_score' => 0,
                'analysis' => $patternAnalysis
            ];
        }

        $searchCount = count($historicalSearches);
        
        // Handle case with only one search
        if ($searchCount === 1) {
            $patternAnalysis['is_consistent'] = true;
            $trustScore += 15; // Give points for having at least one search
            return [
                'trust_score' => $trustScore,
                'analysis' => $patternAnalysis
            ];
        }

        // Calculate average time between searches
        $totalTime = 0;
        
        for ($i = 1; $i < $searchCount; $i++) {
            $timeDiff = strtotime($historicalSearches[$i-1]['created_at']) - strtotime($historicalSearches[$i]['created_at']);
            $totalTime += $timeDiff;
        }
        
        $avgTimeBetweenSearches = $totalTime / ($searchCount - 1);
        
        // Check for consistency
        $isConsistent = true;
        $lastSearchTime = strtotime($historicalSearches[0]['created_at']);
        
        for ($i = 1; $i < $searchCount; $i++) {
            $currentSearchTime = strtotime($historicalSearches[$i]['created_at']);
            $timeDiff = $lastSearchTime - $currentSearchTime;
            
            // If time difference is more than 2x the average, mark as inconsistent
            if ($timeDiff > ($avgTimeBetweenSearches * 2)) {
                $isConsistent = false;
                $patternAnalysis['anomalies'][] = [
                    'index' => $i,
                    'time_diff' => $timeDiff,
                    'expected_diff' => $avgTimeBetweenSearches
                ];
            }
            
            $lastSearchTime = $currentSearchTime;
        }
        
        $patternAnalysis['is_consistent'] = $isConsistent;
        
        // Calculate trend direction
        $firstCount = $historicalSearches[$searchCount-1]['search_count'] ?? 1;
        $lastCount = $historicalSearches[0]['search_count'] ?? 1;
        
        if ($lastCount > ($firstCount * 1.5)) {
            $patternAnalysis['trend_direction'] = 'increasing';
            $trustScore += 5;
        } elseif ($lastCount < ($firstCount * 0.5)) {
            $patternAnalysis['trend_direction'] = 'decreasing';
        } else {
            $patternAnalysis['trend_direction'] = 'stable';
            $trustScore += 10;
        }
        
        // Check for spikes
        $avgCount = array_sum(array_column($historicalSearches, 'search_count')) / $searchCount;
        foreach ($historicalSearches as $search) {
            if (($search['search_count'] ?? 0) > ($avgCount * 2)) {
                $patternAnalysis['spike_detected'] = true;
                break;
            }
        }
        
        if ($patternAnalysis['is_consistent']) {
            $trustScore += 10;
        }
        
        if (!$patternAnalysis['spike_detected']) {
            $trustScore += 5;
        }

        return [
            'trust_score' => $trustScore,
            'analysis' => $patternAnalysis
        ];
    }

    /**
     * Check for known threat patterns in the query
     * 
     * @param string $query
     * @return array
     */
    protected function checkThreatPatterns(string $query): array
    {
        $threatPatterns = [];
        
        // Define threat patterns
        $patterns = [
            'sql_injection' => [
                'patterns' => ['select', 'union', 'drop', 'delete', 'insert', 'update', '--', ';', '/*', '*/'],
                'description' => 'Potential SQL injection attempt'
            ],
            'xss' => [
                'patterns' => ['<script>', 'javascript:', 'onerror=', 'onload=', 'eval(', 'document.cookie'],
                'description' => 'Potential XSS attack attempt'
            ],
            'command_injection' => [
                'patterns' => ['&&', '||', ';', '|', '`', '$', '(', ')'],
                'description' => 'Potential command injection attempt'
            ],
            'directory_traversal' => [
                'patterns' => ['../', '..\\', '/etc/passwd', 'c:\\windows\\'],
                'description' => 'Potential directory traversal attempt'
            ],
            'sensitive_data' => [
                'patterns' => ['password', 'credit card', 'ssn', 'social security', 'bank account'],
                'description' => 'Search for sensitive data'
            ],
            'malware' => [
                'patterns' => ['virus', 'trojan', 'ransomware', 'keylogger', 'spyware'],
                'description' => 'Search for malware-related content'
            ],
            'exploit' => [
                'patterns' => ['exploit', 'vulnerability', '0day', 'zero-day', 'hack'],
                'description' => 'Search for exploit-related content'
            ]
        ];

        // Check each pattern
        foreach ($patterns as $type => $pattern) {
            foreach ($pattern['patterns'] as $needle) {
                if (stripos($query, $needle) !== false) {
                    $threatPatterns[] = [
                        'type' => $type,
                        'pattern' => $needle,
                        'description' => $pattern['description']
                    ];
                    break; // Stop checking this pattern type once found
                }
            }
        }

        // Check for suspicious combinations
        $suspiciousCombinations = [
            ['admin', 'password'],
            ['root', 'access'],
            ['bypass', 'security'],
            ['crack', 'software'],
            ['hack', 'tool']
        ];

        foreach ($suspiciousCombinations as $combination) {
            $foundAll = true;
            foreach ($combination as $word) {
                if (stripos($query, $word) === false) {
                    $foundAll = false;
                    break;
                }
            }
            if ($foundAll) {
                $threatPatterns[] = [
                    'type' => 'suspicious_combination',
                    'pattern' => implode(' + ', $combination),
                    'description' => 'Suspicious combination of terms detected'
                ];
            }
        }

        return $threatPatterns;
    }
} 