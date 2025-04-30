<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class SentimentAnalysis extends BaseController
{
    public function index()
    {
        // Memuat library dan model yang diperlukan
        helper(['form', 'url', 'theme']);

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
        // Memeriksa apakah permintaan adalah AJAX
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid request method'
            ]);
        }

        // Mendapatkan teks dari permintaan
        $text = $this->request->getPost('text');

        if (empty($text)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Text is required'
            ]);
        }

        try {
            // Melakukan analisis sentimen
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
     * Melakukan analisis sentimen pada teks yang diberikan
     * 
     * @param string $text Teks yang akan dianalisis
     * @return array Hasil analisis
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
     * Data sentimen negatif dan positif
     * 
     * @param string $type bank kata ('positif' atau 'negatif')
     * @return array kata positif atau negatif
     */
    private function loadDictionary($type)
    {
        // Inisialisasi model kata
        $wordModel = new \App\Models\WordModel();
        
        // Tentukan status berdasarkan tipe
        $status = ($type === 'positive') ? 1 : 2;
        
        // Ambil kata-kata dari database berdasarkan status
        $words = $wordModel->where('status', $status)->findAll();
        
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