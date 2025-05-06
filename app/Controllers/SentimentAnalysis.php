<?php
/**
 * SentimentAnalysis Controller
 * 
 * Controller for analyzing sentiment of text inputs
 * Provides functionality for text sentiment analysis
 * 
 * @author    Mikhael Felian Waskito <mikhaelfelian@gmail.com>
 * @date      2025-05-15
 */

namespace App\Controllers;

use App\Controllers\BaseController;

class SentimentAnalysis extends BaseController
{
    public function index()
    {
        // Load necessary libraries and models
        helper(['form', 'url', 'theme', 'pdf']);

        $data = [
            'title' => 'Sentiment Analysis',
            'active_menu' => 'sentiment',
            'Pengaturan' => $this->pengaturan,
            'user' => $this->ionAuth->user()->row(),
            'isMenuActive' => isMenuActive('serp/sentiment') ? 'active' : ''
        ];

        return view($this->theme->getThemePath() . '/serp/sentiment', $data);
    }

    public function analyze()
    {
        // Check if request is AJAX
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid request method'
            ]);
        }

        // Get text from the request
        $text = $this->request->getPost('text');

        if (empty($text)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Text is required'
            ]);
        }

        try {
            // Perform sentiment analysis
            $result = $this->analyzeSentiment($text);

            return $this->response->setJSON([
                'success' => true,
                'sentiment' => $result['sentiment'],
                'positiveScore' => $result['positiveScore'],
                'negativeScore' => $result['negativeScore'],
                'positiveWords' => $result['positiveWords'],
                'negativeWords' => $result['negativeWords']
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Sentiment analysis error: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error analyzing text: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Export sentiment analysis results to PDF
     */
    public function exportPdf()
    {
        // Load PDF helper
        helper('pdf');
        
        // Check if request is AJAX
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid request method'
            ]);
        }

        // Get data from request
        $text = $this->request->getPost('text');
        $sentiment = $this->request->getPost('sentiment');
        $positiveScore = $this->request->getPost('positiveScore');
        $negativeScore = $this->request->getPost('negativeScore');
        $positiveWords = json_decode($this->request->getPost('positiveWords'), true);
        $negativeWords = json_decode($this->request->getPost('negativeWords'), true);
        
        if (empty($text)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Text is required'
            ]);
        }

        try {
            // Create sentiment analysis data array
            $data = [
                'text' => $text,
                'sentiment' => $sentiment,
                'positiveScore' => $positiveScore,
                'negativeScore' => $negativeScore,
                'positiveWords' => $positiveWords,
                'negativeWords' => $negativeWords
            ];
            
            // Generate PDF
            $pdfString = sentiment_analysis_pdf($data);
            
            // Return base64 encoded PDF
            return $this->response->setJSON([
                'success' => true,
                'pdf' => base64_encode($pdfString)
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'PDF generation error: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error generating PDF: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Performs sentiment analysis on the given text
     * 
     * @param string $text The text to analyze
     * @return array Analysis results
     */
    private function analyzeSentiment($text)
    {
        // Memuat kamus kata positif dan negatif
        $positiveWords = $this->loadDictionary('positive');
        $negativeWords = $this->loadDictionary('negative');

        // Mengubah teks menjadi huruf kecil untuk pencocokan yang tidak peka huruf besar/kecil
        $text = strtolower($text);
        
        // Menghapus tanda baca dan menormalkan spasi
        $text = preg_replace('/[^\p{L}\p{N}\s]/u', ' ', $text);
        $text = preg_replace('/\s+/', ' ', $text);
        
        // Memisahkan teks menjadi kata-kata
        $words = explode(' ', trim($text));
        
        // Inisialisasi penghitung
        $positiveCount = 0;
        $negativeCount = 0;
        $foundPositiveWords = [];
        $foundNegativeWords = [];
        
        // Menghitung kata-kata positif dan negatif
        foreach ($words as $word) {
            $word = trim($word);
            if (empty($word)) continue;
            
            if (in_array($word, $positiveWords)) {
                $positiveCount++;
                if (!in_array($word, $foundPositiveWords)) {
                    $foundPositiveWords[] = $word;
                }
            }
            
            if (in_array($word, $negativeWords)) {
                $negativeCount++;
                if (!in_array($word, $foundNegativeWords)) {
                    $foundNegativeWords[] = $word;
                }
            }
        }
        
        // Menentukan sentimen
        $totalWords = count($words);
        $positiveScore = $totalWords > 0 ? round(($positiveCount / $totalWords) * 100, 2) : 0;
        $negativeScore = $totalWords > 0 ? round(($negativeCount / $totalWords) * 100, 2) : 0;
        
        if ($positiveScore > $negativeScore) {
            $sentiment = 'positive';
        } else if ($negativeScore > $positiveScore) {
            $sentiment = 'negative';
        } else {
            $sentiment = 'neutral';
        }
        
        return [
            'sentiment'     => $sentiment,
            'positiveScore' => $positiveScore,
            'negativeScore' => $negativeScore,
            'positiveWords' => $foundPositiveWords,
            'negativeWords' => $foundNegativeWords
        ];
    }
    
    /**
     * Loads sentiment dictionary
     * 
     * @param string $type Dictionary type ('positive' or 'negative')
     * @return array Array of words
     */
    private function loadDictionary($type)
    {
        // Inisialisasi model kata
        $wordModel = new \App\Models\WordModel();
        
        // Tentukan status berdasarkan tipe
        $status = ($type === 'positive') ? 1 : 2;
        
        // Ambil kata-kata dari database berdasarkan status
        $words = $wordModel->where('status_word', $status)->findAll();
        
        // Jika tidak ada kata yang ditemukan, gunakan kata sampel untuk demo
        if (empty($words)) {
            if ($type === 'positive') {
                return ['bagus', 'luar biasa', 'hebat', 'menakjubkan', 'indah', 'senang', 'gembira', 'cinta', 
                        'keren', 'fantastis', 'cantik', 'bermanfaat', 'terbaik', 'lebih baik', 'cerah',
                        'brilian', 'nyaman', 'percaya diri', 'puas', 'bahagia', 'mudah', 'efektif',
                        'efisien', 'menikmati', 'bersemangat', 'favorit', 'menyenangkan', 'senang', 'sehat', 'membantu',
                        'ideal', 'mengesankan', 'meningkatkan', 'menarik', 'suka', 'bagus', 'luar biasa',
                        'sempurna', 'menyenangkan', 'puas', 'positif', 'produktif', 'merekomendasikan', 'andal',
                        'kepuasan', 'lancar', 'sukses', 'hebat', 'luar biasa', 'terima kasih', 'berharga'];
            } else {
                return ['buruk', 'mengerikan', 'jelek', 'menakutkan', 'sedih', 'marah', 'benci', 'miskin', 'menjengkelkan',
                        'kesal', 'cemas', 'sombong', 'malu', 'mengerikan', 'buruk', 'membosankan', 'rusak',
                        'canggung', 'bingung', 'gila', 'menyeramkan', 'kejam', 'berbahaya', 'mati', 'cacat',
                        'depresi', 'sulit', 'kotor', 'kecewa', 'menjijikkan', 'tidak suka', 'jahat',
                        'gagal', 'takut', 'kotor', 'busuk', 'penipuan', 'bersalah', 'benci', 'berbahaya', 'kasar',
                        'sakit', 'tidak memadai', 'rendah', 'menjengkelkan', 'malas', 'payah', 'tidak berarti', 
                        'berantakan', 'negatif', 'tidak pernah', 'tidak ada', 'omong kosong', 'sakit', 'masalah', 'menolak',
                        'menolak', 'kasar', 'hancur', 'takut', 'parah', 'bodoh', 'mencurigakan', 'mengerikan',
                        'jelek', 'tidak adil', 'tidak bahagia', 'tidak berguna', 'khawatir', 'terburuk', 'tidak berharga', 'salah'];
            }
        }
        
        // Ekstrak kata dari hasil query
        $wordList = array_column($words, 'word');
        return array_map('trim', $wordList);
    }
} 