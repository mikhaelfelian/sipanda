<?php

namespace App\Controllers;

use App\Libraries\SerpApi;
use App\Libraries\OsintAnalyzer;
use App\Models\KeywordModel;

class Serp extends BaseController
{
    protected $serpApi;
    protected $keywordModel;
    protected $osintAnalyzer;

    public function __construct()
    {
        $this->serpApi = new SerpApi();
        $this->keywordModel = new KeywordModel();
        $this->osintAnalyzer = new OsintAnalyzer();
    }

    public function index()
    {
        // Check if user is logged in
        if (!$this->ionAuth->loggedIn()) {
            return redirect()->to('/auth/login');
        }

        $userId = $this->ionAuth->user()->row()->id;
        $recentSearches = $this->keywordModel->getUserHistory($userId, 5);
        $popularKeywords = $this->keywordModel->getPopularKeywords(5);
        
        // Get trending searches from Google Trends with debug info
        $this->serpApi->setApiKey(config('Serp')->apiKey);
        log_message('info', 'Serp Controller: Fetching trending searches');
        $trendingSearches = $this->serpApi->getTrendingSearches('ID', 10);
        log_message('info', 'Serp Controller: Received ' . count($trendingSearches) . ' trending searches');

        $data = [
            'title'           => 'Google Search Analysis',
            'Pengaturan'      => $this->pengaturan,
            'user'            => $this->ionAuth->user()->row(),
            'isMenuActive'    => isMenuActive('serp') ? 'active' : '',
            'searchResults'   => [],
            'recentSearches'  => $recentSearches,
            'popularKeywords' => $popularKeywords,
            'trendingSearches' => $trendingSearches
        ];

        return view($this->theme->getThemePath() . '/serp/index', $data);
    }

    public function search()
    {
        // Check if user is logged in
        if (!$this->ionAuth->loggedIn()) {
            return redirect()->to('/auth/login');
        }
        
        // Check if this is a POST request
        if ($this->request->getMethod() === 'post') {
            // Get form data
            $query = $this->request->getPost('query');
            $engine = $this->request->getPost('engine') ?? 'google';
            $deepSearch = $this->request->getPost('deep_search') === 'on';
            $useAI = $this->request->getPost('use_ai') === 'on';
            
            // Validate query
            if (empty($query) || strlen($query) < 3) {
                return redirect()->to('/serp')->with('error', 'Kata kunci pencarian diperlukan (minimal 3 karakter)');
            }
            
            // Store search in user history
            $userId = $this->ionAuth->user()->row()->id;
            $this->searchHistoryModel->addSearchHistory($userId, 'search', $query, json_encode([
                'engine' => $engine,
                'deep_search' => $deepSearch,
                'use_ai' => $useAI
            ]));
            
            // Prepare URL parameters
            $params = [
                'q' => $query,
                'engine' => $engine,
                'deep_search' => $deepSearch ? '1' : '0',
                'use_ai' => $useAI ? '1' : '0'
            ];
            
            // Redirect to results page with parameters
            return redirect()->to('/serp/result?' . http_build_query($params));
        }
        
        // If not a POST request, redirect to search form
        return redirect()->to('/serp');
    }

    public function analyzeNews()
    {
        $text = $this->request->getPost('text');
        $analyzer = new \App\Libraries\NaiveBayesAnalyzer();
        $sentiment = $analyzer->analyzeSentiment($text);
        $viral = $analyzer->predictViral($text);
        return $this->response->setJSON([
            'sentiment' => $sentiment,
            'viral' => $viral ? 'Likely Viral' : 'Not Likely Viral'
        ]);
    }

    /**
     * Export search result analysis to PDF
     */
    public function exportSearchResultPdf()
    {
        // Load PDF helper
        helper('pdf');
        
        // Check if request is AJAX
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Metode permintaan tidak valid'
            ]);
        }

        // Get data from request
        $title = $this->request->getPost('title');
        $text = $this->request->getPost('text');
        $sentiment = $this->request->getPost('sentiment');
        $viral = $this->request->getPost('viral');
        
        if (empty($text)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Teks diperlukan'
            ]);
        }

        try {
            // Create PDF document
            $pdf = new \TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
            
            // Set document information
            $pdf->SetCreator(PDF_CREATOR);
            $pdf->SetAuthor('SIPANDA');
            $pdf->SetTitle('Analisis Hasil Pencarian');
            $pdf->SetSubject('Laporan Analisis Hasil Pencarian');
            $pdf->SetKeywords('Pencarian, Analisis, Laporan');
            
            // Set default header data
            $pdf->SetHeaderData('', 0, 'Analisis Hasil Pencarian', 'Dibuat pada: ' . date('Y-m-d H:i:s'));
            
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
            
            // Set image scale factor
            $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
            
            // Add a page
            $pdf->AddPage();
            
            // Set font
            $pdf->SetFont('dejavusans', '', 10);
            
            // Title
            $pdf->SetFont('dejavusans', 'B', 16);
            $pdf->Cell(0, 10, 'Analisis Hasil Pencarian', 0, 1, 'C');
            $pdf->Ln(5);
            
            // Result title
            $pdf->SetFont('dejavusans', 'B', 14);
            $pdf->Cell(0, 10, $title, 0, 1, 'C');
            $pdf->Ln(5);
            
            // Analysis text
            $pdf->SetFont('dejavusans', 'B', 12);
            $pdf->Cell(0, 10, 'Konten Hasil:', 0, 1);
            $pdf->SetFont('dejavusans', '', 10);
            $pdf->MultiCell(0, 10, $text, 0, 'L');
            $pdf->Ln(5);
            
            // Analysis results
            $pdf->SetFont('dejavusans', 'B', 12);
            $pdf->Cell(0, 10, 'Hasil Analisis:', 0, 1);
            
            // Translate sentiment for display
            $sentimentDisplay = $sentiment;
            if ($sentiment == 'positive') $sentimentDisplay = 'POSITIF';
            if ($sentiment == 'negative') $sentimentDisplay = 'NEGATIF';
            if ($sentiment == 'neutral') $sentimentDisplay = 'NETRAL';
            
            // Analysis results table
            $pdf->SetFont('dejavusans', 'B', 10);
            
            // Sentiment row
            $pdf->Cell(40, 10, 'Sentimen:', 1, 0, 'L');
            $pdf->SetFont('dejavusans', '', 10);
            
            // Set color based on sentiment
            if ($sentiment == 'positive') {
                $pdf->SetTextColor(0, 128, 0); // Green
            } elseif ($sentiment == 'negative') {
                $pdf->SetTextColor(255, 0, 0); // Red
            } else {
                $pdf->SetTextColor(255, 165, 0); // Orange
            }
            
            $pdf->Cell(0, 10, $sentimentDisplay, 1, 1, 'L');
            $pdf->SetTextColor(0, 0, 0); // Reset to black
            
            // Viral prediction row
            $pdf->SetFont('dejavusans', 'B', 10);
            $pdf->Cell(40, 10, 'Prediksi Viral:', 1, 0, 'L');
            $pdf->SetFont('dejavusans', '', 10);
            $pdf->Cell(0, 10, $viral . '%', 1, 1, 'L');
            
            $pdf->Ln(5);
            
            // Explanation section
            $pdf->SetFont('dejavusans', 'B', 12);
            $pdf->Cell(0, 10, 'Apa artinya ini?', 0, 1);
            $pdf->SetFont('dejavusans', '', 10);
            
            $pdf->MultiCell(0, 10, "Analisis sentimen mengidentifikasi nada emosional dalam teks. Sentimen positif menunjukkan konten yang menguntungkan, sedangkan sentimen negatif menunjukkan konten yang tidak menguntungkan. Sentimen netral menunjukkan konten yang seimbang atau objektif.", 0, 'L');
            $pdf->Ln(2);
            
            $pdf->MultiCell(0, 10, "Prediksi viral mencoba memperkirakan kemungkinan konten ini menjadi viral berdasarkan karakteristiknya. Persentase yang lebih tinggi menunjukkan konten yang memiliki fitur yang umumnya ditemukan dalam konten viral.", 0, 'L');
            
            // Output PDF as string
            $pdfString = $pdf->Output('analisis_pencarian.pdf', 'S');
            
            // Return base64 encoded PDF
            return $this->response->setJSON([
                'success' => true,
                'pdf' => base64_encode($pdfString)
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Kesalahan pembuatan PDF: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Kesalahan pembuatan PDF: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Export all search results to a single PDF
     */
    public function exportAllResultsPdf()
    {
        // Load PDF helper
        helper('pdf');
        
        // Check if request is AJAX
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Metode permintaan tidak valid'
            ]);
        }

        // Get data from request
        $query = $this->request->getPost('query');
        $engine = $this->request->getPost('engine', FILTER_SANITIZE_STRING) ?? 'google';
        $resultsJson = $this->request->getPost('results');
        $results = json_decode($resultsJson, true);
        
        if (empty($results)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Tidak ada hasil untuk diekspor'
            ]);
        }

        try {
            // Create PDF document
            $pdf = new \TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
            
            // Set document information
            $pdf->SetCreator(PDF_CREATOR);
            $pdf->SetAuthor('SIPANDA');
            $pdf->SetTitle('Analisis Hasil Pencarian ' . strtoupper(str_replace('_', ' ', $engine)));
            $pdf->SetSubject('Laporan Analisis Hasil Pencarian');
            $pdf->SetKeywords('Pencarian, Hasil, Analisis, Laporan');
            
            // Set default header data
            $pdf->SetHeaderData('', 0, 'Analisis Hasil Pencarian ' . strtoupper(str_replace('_', ' ', $engine)), 'Dibuat pada: ' . date('Y-m-d H:i:s'));
            
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
            
            // Set image scale factor
            $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
            
            // Add a page
            $pdf->AddPage();
            
            // Set font
            $pdf->SetFont('dejavusans', '', 10);
            
            // Title
            $pdf->SetFont('dejavusans', 'B', 16);
            $pdf->Cell(0, 10, 'Analisis Hasil Pencarian ' . strtoupper(str_replace('_', ' ', $engine)), 0, 1, 'C');
            $pdf->Ln(5);
            
            // Search query
            $pdf->SetFont('dejavusans', 'B', 12);
            $pdf->Cell(0, 10, 'Kata Kunci Pencarian: ' . $query, 0, 1);
            $pdf->Ln(5);
            
            // Summary section
            $pdf->SetFont('dejavusans', 'B', 14);
            $pdf->Cell(0, 10, 'Ringkasan', 0, 1);
            
            // Count analysis results
            $analysisCount = 0;
            $positiveCount = 0;
            $negativeCount = 0;
            $neutralCount = 0;
            $viralCount = 0;
            
            foreach ($results as $result) {
                if (!empty($result['sentiment'])) {
                    $analysisCount++;
                    
                    if ($result['sentiment'] == 'positive') {
                        $positiveCount++;
                    } elseif ($result['sentiment'] == 'negative') {
                        $negativeCount++;
                    } else {
                        $neutralCount++;
                    }
                    
                    if (strpos($result['viral'] ?? '', 'Likely') !== false) {
                        $viralCount++;
                    }
                }
            }
            
            // Summary statistics
            $pdf->SetFont('dejavusans', '', 10);
            $pdf->Cell(0, 8, 'Total Hasil: ' . count($results), 0, 1);
            $pdf->Cell(0, 8, 'Hasil Teranalisis: ' . $analysisCount, 0, 1);
            $pdf->Cell(0, 8, 'Hasil Positif: ' . $positiveCount, 0, 1);
            $pdf->Cell(0, 8, 'Hasil Negatif: ' . $negativeCount, 0, 1);
            $pdf->Cell(0, 8, 'Hasil Netral: ' . $neutralCount, 0, 1);
            $pdf->Cell(0, 8, 'Hasil Berpotensi Viral: ' . $viralCount, 0, 1);
            
            $pdf->Ln(5);
            
            // Detailed results section
            $pdf->SetFont('dejavusans', 'B', 14);
            $pdf->Cell(0, 10, 'Hasil Detail', 0, 1);
            
            // Different headers based on engine type
            $pdf->SetFont('dejavusans', 'B', 10);
            $pdf->SetFillColor(230, 230, 230);
            
            // Common column: row number
            $pdf->Cell(10, 10, '#', 1, 0, 'C', 1);
            
            // Adjust columns based on engine type
            if ($engine === 'google_images') {
                $pdf->Cell(80, 10, 'Judul', 1, 0, 'C', 1);
                $pdf->Cell(50, 10, 'Sumber', 1, 0, 'C', 1);
                $pdf->Cell(50, 10, 'Sentimen', 1, 1, 'C', 1);
            } elseif ($engine === 'youtube') {
                $pdf->Cell(60, 10, 'Judul Video', 1, 0, 'C', 1);
                $pdf->Cell(40, 10, 'Channel', 1, 0, 'C', 1);
                $pdf->Cell(40, 10, 'Tanggal', 1, 0, 'C', 1);
                $pdf->Cell(40, 10, 'Sentimen', 1, 1, 'C', 1);
            } elseif ($engine === 'twitter') {
                $pdf->Cell(60, 10, 'Tweet', 1, 0, 'C', 1);
                $pdf->Cell(40, 10, 'User', 1, 0, 'C', 1);
                $pdf->Cell(40, 10, 'Tanggal', 1, 0, 'C', 1);
                $pdf->Cell(40, 10, 'Sentimen', 1, 1, 'C', 1);
            } elseif ($engine === 'reddit') {
                $pdf->Cell(60, 10, 'Judul Post', 1, 0, 'C', 1);
                $pdf->Cell(40, 10, 'Subreddit', 1, 0, 'C', 1);
                $pdf->Cell(40, 10, 'Tanggal', 1, 0, 'C', 1);
                $pdf->Cell(40, 10, 'Sentimen', 1, 1, 'C', 1);
            } else {
                // Default columns for Google, Bing, News, Scholar
                $pdf->Cell(60, 10, 'Judul', 1, 0, 'C', 1);
                $pdf->Cell(40, 10, 'Tanggal', 1, 0, 'C', 1);
                $pdf->Cell(40, 10, 'Sentimen', 1, 0, 'C', 1);
                $pdf->Cell(40, 10, 'Prediksi Viral', 1, 1, 'C', 1);
            }
            
            // Results table content
            $pdf->SetFont('dejavusans', '', 10);
            foreach ($results as $i => $result) {
                // If result is too long, add a new page
                if ($pdf->getY() > 250) {
                    $pdf->AddPage();
                }
                
                // Row number
                $pdf->Cell(10, 10, ($i + 1), 1, 0, 'C');
                
                // Title (shortened if needed)
                $title = $result['title'] ?? 'No Title';
                if (strlen($title) > 30) {
                    $title = substr($title, 0, 27) . '...';
                }
                
                // Display content based on engine type
                if ($engine === 'google_images') {
                    $pdf->Cell(80, 10, $title, 1, 0);
                    $pdf->Cell(50, 10, $result['source'] ?? '', 1, 0, 'C');
                    
                    // Sentiment with color
                    $sentiment = !empty($result['sentiment']) ? $result['sentiment'] : 'Belum dianalisis';
                    $this->addSentimentCell($pdf, $sentiment, 50);
                    $pdf->Ln();
                } elseif ($engine === 'youtube') {
                    $pdf->Cell(60, 10, $title, 1, 0);
                    $pdf->Cell(40, 10, $result['source'] ?? '', 1, 0, 'C');
                    $pdf->Cell(40, 10, $result['date'] ?? '', 1, 0, 'C');
                    
                    // Sentiment with color
                    $sentiment = !empty($result['sentiment']) ? $result['sentiment'] : 'Belum dianalisis';
                    $this->addSentimentCell($pdf, $sentiment, 40);
                    $pdf->Ln();
                } elseif ($engine === 'twitter') {
                    $pdf->Cell(60, 10, $title, 1, 0);
                    $pdf->Cell(40, 10, $result['source'] ?? '', 1, 0, 'C');
                    $pdf->Cell(40, 10, $result['date'] ?? '', 1, 0, 'C');
                    
                    // Sentiment with color
                    $sentiment = !empty($result['sentiment']) ? $result['sentiment'] : 'Belum dianalisis';
                    $this->addSentimentCell($pdf, $sentiment, 40);
                    $pdf->Ln();
                } elseif ($engine === 'reddit') {
                    $pdf->Cell(60, 10, $title, 1, 0);
                    $pdf->Cell(40, 10, $result['source'] ?? '', 1, 0, 'C');
                    $pdf->Cell(40, 10, $result['date'] ?? '', 1, 0, 'C');
                    
                    // Sentiment with color
                    $sentiment = !empty($result['sentiment']) ? $result['sentiment'] : 'Belum dianalisis';
                    $this->addSentimentCell($pdf, $sentiment, 40);
                    $pdf->Ln();
                } else {
                    // Default Google, Bing, News, Scholar display
                    $pdf->Cell(60, 10, $title, 1, 0);
                    
                    // Published date
                    $publishedDate = $result['date'] ?? '';
                    $pdf->Cell(40, 10, $publishedDate, 1, 0, 'C');
                    
                    // Sentiment with color
                    $sentiment = !empty($result['sentiment']) ? $result['sentiment'] : 'Belum dianalisis';
                    $this->addSentimentCell($pdf, $sentiment, 40);
                    
                    // Viral prediction
                    $viral = !empty($result['viral']) ? $result['viral'] : 'Belum dianalisis';
                    if ($viral == 'Likely Viral') {
                        $viral = 'Berpotensi Viral';
                    } else if ($viral == 'Not Likely Viral') {
                        $viral = 'Tidak Berpotensi Viral';
                    }
                    $pdf->Cell(40, 10, $viral, 1, 1, 'C');
                }
            }
            
            $pdf->Ln(10);
            
            // Add detailed pages for each result
            $pdf->SetFont('dejavusans', 'B', 14);
            $pdf->AddPage();
            $pdf->Cell(0, 10, 'Detail Hasil Individual', 0, 1);
            
            foreach ($results as $i => $result) {
                // If we're not at the start of the page and there's not enough space, add a new page
                if ($pdf->getY() > 120 && $pdf->getY() != $pdf->getHeaderMargin() + 30) {
                    $pdf->AddPage();
                }
                
                // Result title
                $pdf->SetFont('dejavusans', 'B', 12);
                $pdf->Cell(0, 10, 'Hasil #' . ($i + 1) . ': ' . ($result['title'] ?? 'No Title'), 0, 1);
                
                // Link
                $pdf->SetFont('dejavusans', '', 10);
                $pdf->SetTextColor(0, 0, 255); // Blue
                $pdf->Cell(20, 8, 'URL:', 0, 0);
                $pdf->Cell(0, 8, $result['link'] ?? 'No Link', 0, 1);
                $pdf->SetTextColor(0, 0, 0); // Reset to black
                
                // Show source if available
                if (!empty($result['source'])) {
                    $pdf->Cell(20, 8, 'Sumber:', 0, 0);
                    $pdf->Cell(0, 8, $result['source'], 0, 1);
                }
                
                // Snippet
                if (!empty($result['snippet'])) {
                    $pdf->SetFont('dejavusans', 'B', 10);
                    $pdf->Cell(0, 8, 'Cuplikan Konten:', 0, 1);
                    $pdf->SetFont('dejavusans', '', 10);
                    $pdf->MultiCell(0, 8, $result['snippet'], 0, 'L');
                }
                
                // Analysis results if available
                if (!empty($result['sentiment'])) {
                    $pdf->SetFont('dejavusans', 'B', 10);
                    $pdf->Cell(0, 8, 'Hasil Analisis:', 0, 1);
                    
                    // Sentiment
                    $pdf->SetFont('dejavusans', '', 10);
                    $pdf->Cell(40, 8, 'Sentimen:', 0, 0);
                    
                    // Set color based on sentiment
                    if ($result['sentiment'] == 'positive') {
                        $pdf->SetTextColor(0, 128, 0); // Green
                        $sentimentDisplay = 'POSITIF';
                    } elseif ($result['sentiment'] == 'negative') {
                        $pdf->SetTextColor(255, 0, 0); // Red
                        $sentimentDisplay = 'NEGATIF';
                    } else {
                        $pdf->SetTextColor(255, 165, 0); // Orange
                        $sentimentDisplay = 'NETRAL';
                    }
                    
                    $pdf->Cell(0, 8, $sentimentDisplay, 0, 1);
                    $pdf->SetTextColor(0, 0, 0); // Reset to black
                    
                    // Viral prediction
                    if (!empty($result['viral'])) {
                        $pdf->Cell(40, 8, 'Prediksi Viral:', 0, 0);
                        $viral = $result['viral'];
                        if ($viral == 'Likely Viral') {
                            $viral = 'Berpotensi Viral';
                        } else if ($viral == 'Not Likely Viral') {
                            $viral = 'Tidak Berpotensi Viral';
                        }
                        $pdf->Cell(0, 8, $viral, 0, 1);
                    }
                }
                
                // Add separator
                $pdf->Ln(5);
                $pdf->Cell(0, 0, '', 'T', 1); // Horizontal line
                $pdf->Ln(5);
            }
            
            // Output PDF as string
            $pdfString = $pdf->Output('analisis_hasil_pencarian.pdf', 'S');
            
            // Return base64 encoded PDF
            return $this->response->setJSON([
                'success' => true,
                'pdf' => base64_encode($pdfString)
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Kesalahan pembuatan PDF: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Kesalahan pembuatan PDF: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Helper method to add sentiment cell with appropriate color
     */
    private function addSentimentCell($pdf, $sentiment, $width = 40)
    {
        // Translate sentiment for display
        $sentimentDisplay = $sentiment;
        if ($sentiment == 'positive') {
            $sentimentDisplay = 'POSITIF';
            $pdf->SetTextColor(0, 128, 0); // Green
        } else if ($sentiment == 'negative') {
            $sentimentDisplay = 'NEGATIF';
            $pdf->SetTextColor(255, 0, 0); // Red
        } else if ($sentiment == 'neutral') {
            $sentimentDisplay = 'NETRAL';
            $pdf->SetTextColor(255, 165, 0); // Orange
        }
        
        $pdf->Cell($width, 10, $sentimentDisplay, 1, 0, 'C');
        $pdf->SetTextColor(0, 0, 0); // Reset to black
    }
    
    /**
     * Handle direct URL access to search results via GET request
     * 
     * @return mixed
     */
    public function result()
    {
        // Check if user is logged in
        if (!$this->ionAuth->loggedIn()) {
            return redirect()->to('/auth/login');
        }

        // Get query from URL parameter
        $query = $this->request->getGet('q');
        $engine = $this->request->getGet('engine', FILTER_SANITIZE_STRING) ?? 'google';
        $deepSearch = (bool)$this->request->getGet('deep_search', FILTER_VALIDATE_BOOLEAN);
        $useAI = (bool)$this->request->getGet('use_ai', FILTER_VALIDATE_BOOLEAN);
        
        // Validate query
        if (empty($query) || strlen($query) < 3) {
            return redirect()->to('/serp')->with('error', 'Kata kunci pencarian diperlukan (minimal 3 karakter)');
        }
        
        // Perform OSINT analysis
        $osintAnalysis = $this->osintAnalyzer->analyzeTrend($query);

        // If AI analysis is requested but not provided in the session, fetch it now
        $aiAnalysis = [];
        if ($useAI && !session()->has('ai_analysis')) {
            // Load ChatGPT service
            $chatGPTService = new \App\Libraries\ChatGPTService();
            
            try {
                // Create detailed prompt for search context analysis
                $systemPrompt = "Anda adalah asisten analisis pencarian yang ahli. Analisis konteks pencarian berikut dan berikan insight mengenai:
                1. Topik utama dan sub-topik yang mungkin relevan
                2. Kata kunci tambahan yang disarankan untuk eksplorasi lebih lanjut
                3. Kemungkinan maksud/intent dari pengguna yang melakukan pencarian
                4. Kategori/domain pengetahuan yang terkait (misalnya: berita, akademik, teknologi, kesehatan, dll)
                5. Potensi bias atau sudut pandang yang perlu diperhatikan
                
                Jawaban harus singkat dan terstruktur dalam format JSON. Gunakan bahasa Indonesia.";
                
                // Get AI analysis
                $aiResponse = $chatGPTService->askWithSystemInstruction(
                    "Analisis kueri pencarian: \"$query\"", 
                    $systemPrompt,
                    'gpt-3.5-turbo',
                    0.7,
                    500
                );
                
                // Try to parse JSON response
                $parsedResponse = json_decode($aiResponse, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($parsedResponse)) {
                    $aiAnalysis = $parsedResponse;
                } else {
                    // If not valid JSON, use text as-is
                    $aiAnalysis = ['analysis' => $aiResponse];
                }
                
                // Store in session for later use
                session()->set('ai_analysis', $aiAnalysis);
            } catch (\Exception $e) {
                log_message('error', 'ChatGPT analysis error in result(): ' . $e->getMessage());
                $aiAnalysis = ['error' => 'Analisis AI tidak tersedia saat ini.'];
            }
        } else if (session()->has('ai_analysis')) {
            // Retrieve from session if available
            $aiAnalysis = session()->get('ai_analysis');
        }

        // Save keyword to database
        $this->keywordModel->save([
            'keyword' => $query,
            'search_count' => 1,
            'last_searched' => date('Y-m-d H:i:s')
        ]);
        
        // Prepare search parameters based on selected engine
        $searchParams = $this->getSearchParamsByEngine($engine, $deepSearch);
        
        try {
            // Perform search
            $results = $this->serpApi->search($query, $searchParams);
            
            // Process results based on engine type
            $processedResults = $this->processSearchResults($results, $engine);
            
            // Get user's recent searches
            $userId = $this->ionAuth->user()->row()->id;
            $recentSearches = $this->keywordModel->getUserHistory($userId, 5);
            
            // Prepare data for view
            $data = [
                'title' => 'Search Results: ' . $query,
                'query' => $query,
                'engine' => $engine,
                'results' => $processedResults,
                'osintAnalysis' => $osintAnalysis,
                'aiAnalysis' => $aiAnalysis,
                'deepSearch' => $deepSearch,
                'useAI' => $useAI,
                'Pengaturan' => $this->pengaturan,
                'user' => $this->ionAuth->user()->row(),
                'isMenuActive' => isMenuActive('serp') ? 'active' : '',
                'recentSearches' => $recentSearches
            ];
            
            return view($this->theme->getThemePath() . '/serp/results', $data);
        } catch (\RuntimeException $e) {
            if (strpos($e->getMessage(), 'timed out') !== false) {
                return redirect()->to('/serp')
                    ->with('error', 'Timeout koneksi ke API. Silakan coba lagi nanti.');
            } else if (strpos($e->getMessage(), 'SERP API Error') !== false) {
                return redirect()->to('/serp')
                    ->with('error', 'Koneksi ke API gagal: ' . $e->getMessage());
            }
            
            // For other unexpected errors, rethrow
            throw $e;
        }
    }

    /**
     * Get search parameters based on engine type
     *
     * @param string $engine Engine type (google, youtube, twitter, etc)
     * @param bool $deepSearch Whether to perform deep search
     * @return array Search parameters
     */
    private function getSearchParamsByEngine($engine, $deepSearch = false)
    {
        $params = [
            'google_domain' => 'google.co.id',
            'gl' => 'id',
            'hl' => 'id',
            'num' => 20
        ];
        
        switch ($engine) {
            case 'google_news':
                $params['engine'] = 'google';
                $params['tbm'] = 'nws';       // Search for news results
                $params['tbs'] = $deepSearch ? 'qdr:y,sbd:1' : 'qdr:d';  // Past day or year
                break;
            
            case 'google_images':
                $params['engine'] = 'google';
                $params['tbm'] = 'isch';      // Search for images
                break;
            
            case 'youtube':
                $params['engine'] = 'youtube';
                $params['sp'] = $deepSearch ? 'CAISAhAB' : 'CAISAggB';  // Date filter
                break;
            
            case 'twitter':
                $params['engine'] = 'twitter';
                if ($deepSearch) {
                    $params['min_retweets'] = 5;  // Min retweets for deep search
                }
                break;
                
            case 'reddit':
                $params['engine'] = 'reddit';
                $params['time_frame'] = $deepSearch ? 'year' : 'month';
                break;
                
            case 'bing':
                $params['engine'] = 'bing';
                break;
                
            case 'google_scholar':
                $params['engine'] = 'google_scholar';
                break;
                
            default:
                // Default Google search
                $params['engine'] = 'google';
                $params['tbs'] = $deepSearch ? 'qdr:y' : '';  // Past year for deep search
                break;
        }
        
        return $params;
    }

    /**
     * Process search results based on engine type
     *
     * @param array $results Raw search results
     * @param string $engine Engine type
     * @return array Processed results
     */
    private function processSearchResults($results, $engine)
    {
        $processedResults = [];
        
        switch ($engine) {
            case 'google_news':
                $sourceResults = $results['news_results'] ?? [];
                foreach ($sourceResults as $result) {
                    // Process date format
                    if (!empty($result['date'])) {
                        try {
                            $dateObj = new \DateTime($result['date']);
                            $result['formatted_date'] = $dateObj->format('Y-m-d H:i:s');
                        } catch (\Exception $e) {
                            $result['formatted_date'] = $result['date'];
                        }
                    }
                    $processedResults[] = $result;
                }
                break;
            
            case 'youtube':
                $sourceResults = $results['video_results'] ?? [];
                foreach ($sourceResults as $result) {
                    // Additional processing for YouTube results
                    $result['type'] = 'video';
                    $processedResults[] = $result;
                }
                break;
            
            case 'twitter':
                $sourceResults = $results['tweets'] ?? [];
                foreach ($sourceResults as $result) {
                    // Additional processing for Twitter results
                    $result['type'] = 'tweet';
                    if (!empty($result['published_date'])) {
                        try {
                            $dateObj = new \DateTime($result['published_date']);
                            $result['formatted_date'] = $dateObj->format('Y-m-d H:i:s');
                        } catch (\Exception $e) {
                            $result['formatted_date'] = $result['published_date'];
                        }
                    }
                    $processedResults[] = $result;
                }
                break;
                
            case 'reddit':
                $sourceResults = $results['posts'] ?? [];
                foreach ($sourceResults as $result) {
                    // Additional processing for Reddit results
                    $result['type'] = 'reddit';
                    $processedResults[] = $result;
                }
                break;
                
            case 'google_images':
                $sourceResults = $results['images_results'] ?? [];
                foreach ($sourceResults as $result) {
                    $result['type'] = 'image';
                    $processedResults[] = $result;
                }
                break;
                
            case 'google_scholar':
                $sourceResults = $results['organic_results'] ?? [];
                foreach ($sourceResults as $result) {
                    $result['type'] = 'scholar';
                    $processedResults[] = $result;
                }
                break;
                
            default:
                // Default Google search results
                $sourceResults = $results['organic_results'] ?? [];
                foreach ($sourceResults as $result) {
                    $result['type'] = 'web';
                    $processedResults[] = $result;
                }
                break;
        }
        
        return $processedResults;
    }

    /**
     * Export search results to formatted text for patrol report
     */
    public function exportToText()
    {
        // Check if request is AJAX
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Metode permintaan tidak valid'
            ]);
        }

        // Get data from request
        $query = $this->request->getPost('query');
        $engine = $this->request->getPost('engine', FILTER_SANITIZE_STRING) ?? 'google';
        $resultsJson = $this->request->getPost('results');
        $results = json_decode($resultsJson, true);
        
        if (empty($results)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Tidak ada hasil untuk diekspor'
            ]);
        }

        try {
            // Get current date and time
            $hari_laporan = date('l, d F Y');
            
            // Prepare contents for the formatted text
            $contentList = '';
            $sentimentAnalysis = '';
            $keywordPrediction = '';
            
            // Create a title for the report based on engine type
            $engineTitle = strtoupper(str_replace('_', ' ', $engine));
            
            foreach ($results as $i => $result) {
                // Format content list based on engine type
                if ($engine === 'twitter') {
                    $contentList .= ($i + 1) . '. Tweet: ';
                    $contentList .= '@' . ($result['source'] ?? '') . ' - ';
                } else if ($engine === 'youtube') {
                    $contentList .= ($i + 1) . '. Video: ';
                    $contentList .= ($result['source'] ?? '') . ' - ';
                } else if ($engine === 'reddit') {
                    $contentList .= ($i + 1) . '. Reddit: ';
                    $contentList .= ($result['source'] ?? '') . ' - ';
                } else if ($engine === 'google_images') {
                    $contentList .= ($i + 1) . '. Gambar: ';
                } else {
                    $contentList .= ($i + 1) . '. ' . ($i < 1 ? 'Akun media sosial: ' : 'Website: ');
                }
                
                // Use published date if available
                $publishedDate = date('Y-m-d');
                if (!empty($result['date'])) {
                    $publishedDate = date('Y-m-d', strtotime($result['date']));
                } elseif (!empty($result['published_date'])) {
                    $publishedDate = date('Y-m-d', strtotime($result['published_date']));
                }
                
                $contentList .= $publishedDate . ' ' . ($result['link'] ?? 'No link') . ' - ' . ($result['snippet'] ?? ($result['title'] ?? 'No content')) . "\n";
                
                if (!empty($result['sentiment'])) {
                    $sentimentAnalysis .= $result['sentiment'] . "\n";
                }
                
                // Extract keywords from title
                if (!empty($result['title'])) {
                    $words = explode(' ', $result['title']);
                    $keywords = array_slice($words, 0, min(3, count($words)));
                    $keywordPrediction .= implode(', ', $keywords) . ', ';
                }
            }
            
            // Format the text template
            $formattedText = "Kepada Yth.\n<kepala>\n\nDari : \n<kepala>\n\n";
            // Determine greeting based on current time
            $hour = (int)date('H');
            $greeting = 'malam';
            if ($hour >= 5 && $hour < 12) {
                $greeting = 'pagi';
            } elseif ($hour >= 12 && $hour < 18) {
                $greeting = 'siang';
            }
            
            $formattedText .= "Selamat {$greeting} Komandan, Mohon ijin melaporkan pada hari {$hari_laporan} <division> melaksanakan Patroli Cyber di {$engineTitle} ";
            $formattedText .= "terkait Issue : " . $keywordPrediction . " serta yang mendiskriditkan ";
            $formattedText .= "Pemerintahan dan Polri dengan link sebagai berikut :\n\n";
            
            $formattedText .= "Fakta - fakta :\n{$contentList}\n";
            
            $formattedText .= "Analisa :\n";
            // Analyze sentiment patterns from results
            $sentimentCounts = ['positive' => 0, 'negative' => 0, 'neutral' => 0];
            $topIssues = [];
            
            foreach ($results as $result) {
                if (!empty($result['sentiment'])) {
                    $sentiment = strtolower($result['sentiment']);
                    if (strpos($sentiment, 'positive') !== false) {
                        $sentimentCounts['positive']++;
                    } elseif (strpos($sentiment, 'negative') !== false) {
                        $sentimentCounts['negative']++;
                    } else {
                        $sentimentCounts['neutral']++;
                    }
                    
                    // Extract potential issues from snippets
                    $snippet = strtolower($result['snippet'] ?? '');
                    $issueKeywords = ['pemerintah', 'uu tni', 'ruu polri', 'indonesia gelap', 'ijazah', 'mayday'];
                    foreach ($issueKeywords as $issue) {
                        if (strpos($snippet, $issue) !== false) {
                            if (!isset($topIssues[$issue])) {
                                $topIssues[$issue] = 0;
                            }
                            $topIssues[$issue]++;
                        }
                    }
                }
            }
            
            // Sort issues by frequency
            arsort($topIssues);
            $issuesList = array_slice(array_keys($topIssues), 0, 3);
            $issuesText = !empty($issuesList) ? implode(', ', $issuesList) : 'tidak ada isu spesifik yang terdeteksi';
            
            // Generate analysis based on sentiment data
            $formattedText .= "A. Berdasarkan analisis sentimen, terdapat " . $sentimentCounts['negative'] . " konten dengan sentimen negatif";
            if ($sentimentCounts['negative'] > 0) {
                $formattedText .= " terkait isu " . $issuesText . ". Konten-konten ini berpotensi mendiskreditkan pemerintah dan institusi negara serta menciptakan opini negatif di masyarakat.\n";
            } else {
                $formattedText .= ". Tidak ditemukan konten yang secara signifikan mendiskreditkan pemerintah atau institusi negara.\n";
            }
            
            $formattedText .= "B. " . ($sentimentCounts['negative'] > $sentimentCounts['positive'] ? "Mayoritas" : "Sebagian") . " konten dengan sentimen negatif berpotensi mempengaruhi pandangan publik";
            if (!empty($issuesList)) {
                $formattedText .= " terutama terkait isu " . $issuesList[0] . ", yang dapat berdampak pada kredibilitas institusi terkait.\n";
            } else {
                $formattedText .= " meskipun belum teridentifikasi isu spesifik yang dominan.\n";
            }
            
            $formattedText .= "C. Terdapat " . $sentimentCounts['positive'] . " konten dengan sentimen positif dan " . $sentimentCounts['neutral'] . " konten netral, yang menunjukkan masih adanya keseimbangan narasi di media sosial terkait isu-isu tersebut.\n\n";
            
            // Customize recommendations based on engine type
            $formattedText .= "Prediksi :\n";
            
            if ($engine === 'twitter' || $engine === 'reddit') {
                $formattedText .= "A. Konten narasi negatif di platform media sosial {$engineTitle} akan terus muncul dan dapat menyebar dengan cepat karena sifat viralitas platform tersebut.\n";
                $formattedText .= "B. Diskusi di {$engineTitle} cenderung lebih terpolarisasi dan emosional, sehingga berpotensi memicu reaksi yang lebih intens dari masyarakat.\n\n";
            } else if ($engine === 'youtube') {
                $formattedText .= "A. Konten video di {$engineTitle} memiliki dampak yang lebih kuat karena kombinasi visual dan audio, sehingga narasi negatif dapat memiliki pengaruh yang lebih besar terhadap persepsi publik.\n";
                $formattedText .= "B. Video-video dengan narasi negatif berpotensi mendapatkan lebih banyak penonton dan engagement, terutama jika algoritma platform mempromosikannya ke audiens yang lebih luas.\n\n";
            } else {
                $formattedText .= "A. Konten narasi negatif akan terus muncul sampai tujuan yang diinginkan tercapai yaitu pembatalan UU TNI, Revisi RUU POLRI dan perlawanan terhadap kebijakan pemerintah dengan issu tema Indonesia Gelap serta pengakuan Ijazah Palsu Jokowi dengan cara provokasi di seluruh media sosial.\n";
                $formattedText .= "B. Konten narasi negatif di media sosial mulai mengarah pada ajakan dan provokasi untuk melakukan pergerakan / perlawanan secara masif dan sistimatis pada peringatan mayday di seluruh daerah wilayah Jawa Tengah.\n\n";
            }
            
            $formattedText .= "Rekomendasi :\n";
            $formattedText .= "A. Laksanakan pulbaket dan penyelidikan lebih mendalam terhadap pegiat media sosial yang telah membuat konten narasi negatif dan provokatif sehingga dapat diketahui maksud dan tujuannya.\n";
            
            if ($engine === 'twitter' || $engine === 'reddit' || $engine === 'youtube') {
                $formattedText .= "B. Laksanakan kontra narasi di platform {$engineTitle} dengan memperhatikan karakteristik khusus platform tersebut untuk efektivitas yang lebih baik.\n";
            } else {
                $formattedText .= "B. Laksanakan kontra terhadap konten narasi negatif beserta pemilik akun medsos agar konten tersebut tidak berkembang menjadi viral dan menggiring opini negatif serta memprovokasi masyarakat untuk melakukan perlawanan terhadap pemerintah dengan cara berunjuk rasa.\n";
            }
            
            $formattedText .= "C. Laksanakan kontra dengan pembuatan konten isu tandingan yg berbeda guna penggembosan konten narasi negatif terkait penolakan UU TNI, RUU Polri, Aksi Mayday dan kebijakan pemerintah dgn isu Indonesia gelap.\n\n";
            
            $formattedText .= "Prediksi kata kunci :\n{$keywordPrediction}\n\n";
            
            $formattedText .= "Analisa sentimen negatif :\n{$sentimentAnalysis}\n\n";
            
            // Add formatted creator information using ion auth user
            $user = $this->ionAuth->user()->row();
            $formattedText .= "Pembuat Laporan :\n";
            $formattedText .= "{$user->first_name} {$user->last_name}\n";
            $formattedText .= date('d-m-Y H:i:s');
            
            // Return the formatted text
            return $this->response->setJSON([
                'success' => true,
                'text' => $formattedText
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Error generating text report: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Kesalahan pembuatan laporan teks: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Debug Google Trends API with plaintext output (only in development environment)
     */
    public function debugTrendsText()
    {
        // Only allow in development environment
        if (ENVIRONMENT !== 'development') {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
        
        // Get API key and debug info
        $apiKey = config('Serp')->apiKey;
        $url = 'https://serpapi.com/search.json?engine=google_trends&api_key=' . $apiKey . '&geo=ID&hl=id';
        
        // Build debug output
        $output = "==== GOOGLE TRENDS DEBUG ====\n";
        $output .= "API Key: ***" . substr($apiKey, -4) . "\n";
        $output .= "URL: " . $url . "\n";
        $output .= "Environment: " . ENVIRONMENT . "\n";
        $output .= "Timestamp: " . date('Y-m-d H:i:s') . "\n\n";
        
        // Make direct API request using cURL
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $output .= "HTTP Code: " . $httpCode . "\n";
        
        if(curl_errno($ch)) {
            $output .= "cURL Error: " . curl_error($ch) . "\n";
        }
        
        curl_close($ch);
        
        // Process response
        $data = json_decode($response, true);
        $jsonError = json_last_error_msg();
        
        $output .= "JSON Error: " . $jsonError . "\n";
        
        if ($data) {
            $output .= "Data Keys: " . implode(', ', array_keys($data)) . "\n\n";
            
            // Check for trending searches
            if (isset($data['trending_searches'])) {
                $output .= "Found " . count($data['trending_searches']) . " trending searches\n";
                
                // Show the first few trends
                $output .= "Sample trends:\n";
                for ($i = 0; $i < min(5, count($data['trending_searches'])); $i++) {
                    $trend = $data['trending_searches'][$i];
                    $output .= ($i+1) . ". " . ($trend['title'] ?? 'No title') . "\n";
                    
                    // Check for related queries
                    if (isset($trend['related_queries']) && is_array($trend['related_queries'])) {
                        $output .= "   Related queries: " . implode(', ', array_slice($trend['related_queries'], 0, 3)) . "...\n";
                    } else {
                        $output .= "   No related queries found\n";
                    }
                }
            } else {
                $output .= "No trending_searches found in API response\n";
            }
        } else {
            $output .= "Failed to parse response as JSON\n";
        }
        
        // Testing implementation
        $output .= "\n==== IMPLEMENTATION TEST ====\n";
        $this->serpApi->setApiKey($apiKey);
        $trendingSearches = $this->serpApi->getTrendingSearches('ID', 10);
        $output .= "Implementation returned " . count($trendingSearches) . " trends\n";
        
        if (!empty($trendingSearches)) {
            $output .= "Sample implementation results:\n";
            for ($i = 0; $i < min(5, count($trendingSearches)); $i++) {
                $trend = $trendingSearches[$i];
                $title = is_array($trend) ? ($trend['title'] ?? 'No title') : $trend;
                $output .= ($i+1) . ". " . $title . "\n";
                
                // Check for related queries
                if (is_array($trend) && isset($trend['related_queries']) && is_array($trend['related_queries'])) {
                    $output .= "   Related queries: " . implode(', ', array_slice($trend['related_queries'], 0, 3)) . "...\n";
                }
            }
        }
        
        // Return as plain text
        return $this->response->setHeader('Content-Type', 'text/plain')->setBody($output);
    }
    
    /**
     * Get fallback trend data for display
     */
    public function fallbackTrends()
    {
        $fallbackTrends = [
            [
                'title' => 'hari buruh',
                'related_queries' => ['hari buruh internasional', 'hari buruh 2025', 'hari buruh nasional', 'apa itu hari buruh']
            ],
            [
                'title' => 'may day',
                'related_queries' => ['hari buruh internasional', 'hari buruh 2025', 'hari buruh nasional', 'tanggal 1 mei libur']
            ],
            [
                'title' => '1 mei',
                'related_queries' => ['memperingati hari apa', 'hari apa', 'tanggal 1 mei 2025 apakah libur']
            ],
            [
                'title' => 'tanggal merah',
                'related_queries' => ['besok tanggal merah', 'apakah besok tanggal merah', 'hari buruh 2025']
            ],
            [
                'title' => 'labour day',
                'related_queries' => ['hari buruh internasional', 'may day', '1 mei memperingati hari apa']
            ],
            [
                'title' => 'pemilu',
                'related_queries' => ['pemilu 2024', 'hasil pemilu', 'jadwal pemilu 2024']
            ],
            [
                'title' => 'pilpres',
                'related_queries' => ['hasil pilpres', 'pilpres 2024', 'calon pilpres 2024']
            ],
            [
                'title' => 'COVID-19',
                'related_queries' => ['vaksin covid', 'gejala covid', 'covid di indonesia']
            ],
            [
                'title' => 'harga sembako',
                'related_queries' => ['daftar harga sembako', 'harga beras', 'kenaikan harga sembako']
            ],
            [
                'title' => 'banjir',
                'related_queries' => ['banjir jakarta', 'penyebab banjir', 'cara mengatasi banjir']
            ]
        ];
        
        return $this->response->setJSON($fallbackTrends);
    }
} 