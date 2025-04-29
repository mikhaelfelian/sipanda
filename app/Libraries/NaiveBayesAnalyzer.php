<?php
namespace App\Libraries;

class NaiveBayesAnalyzer
{
    // Dummy word lists for demonstration
    protected $positiveWords = ['good', 'great', 'excellent', 'positive', 'success', 'win', 'happy', 'growth'];
    protected $negativeWords = ['bad', 'poor', 'fail', 'negative', 'loss', 'sad', 'decline', 'crisis'];

    public function analyzeSentiment($text)
    {
        $text = strtolower($text);
        $pos = 0; $neg = 0;
        foreach ($this->positiveWords as $word) {
            if (strpos($text, $word) !== false) $pos++;
        }
        foreach ($this->negativeWords as $word) {
            if (strpos($text, $word) !== false) $neg++;
        }
        if ($pos > $neg) return 'positive';
        if ($neg > $pos) return 'negative';
        return 'neutral';
    }

    public function predictViral($text)
    {
        // Simple heuristic: if text contains 'viral', 'trending', or is long, predict viral
        $viralWords = ['viral', 'trending', 'breaking', 'hot'];
        foreach ($viralWords as $word) {
            if (strpos(strtolower($text), $word) !== false) return true;
        }
        if (str_word_count($text) > 30) return true;
        return false;
    }
}
