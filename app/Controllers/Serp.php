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

        $data = [
            'title'           => 'Google Search Analysis',
            'Pengaturan'      => $this->pengaturan,
            'user'            => $this->ionAuth->user()->row(),
            'isMenuActive'    => isMenuActive('serp') ? 'active' : '',
            'searchResults'   => [],
            'recentSearches'  => $recentSearches,
            'popularKeywords' => $popularKeywords
        ];

        return view($this->theme->getThemePath() . '/serp/index', $data);
    }

    public function search()
    {
        // Validate input
        $rules = [
            'query' => 'required|min_length[3]|max_length[255]',
            'deep_search' => 'permit_empty|in_list[0,1]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $query = $this->request->getPost('query');
        $deepSearch = (bool)$this->request->getPost('deep_search');

        // Save keyword to database
        $this->keywordModel->save([
            'keyword' => $query,
            'search_count' => 1,
            'last_searched' => date('Y-m-d H:i:s')
        ]);

        // Perform OSINT analysis
        $osintAnalysis = $this->osintAnalyzer->analyzeTrend($query);

        // Prepare search parameters
        $searchParams = [
            'engine' => 'google',
            'google_domain' => 'google.co.id',
            'gl' => 'id',
            'hl' => 'id',
            'num' => 20
        ];

        // Add deep search parameters if enabled
        if ($deepSearch) {
            $searchParams = array_merge($searchParams, [
                'tbs' => 'qdr:y' // Past year
            ]);
        }

        try {
            // Perform search
            $results = $this->serpApi->search($query, $searchParams);

            // Prepare data for view
            $data = [
                'title' => 'Search Results',
                'query' => $query,
                'results' => $results['organic_results'] ?? [],
                'osintAnalysis' => $osintAnalysis,
                'deepSearch' => $deepSearch,
                'Pengaturan' => $this->pengaturan,
                'user' => $this->ionAuth->user()->row(),
                'isMenuActive' => isMenuActive('serp') ? 'active' : ''
            ];

            return redirect()->to(base_url('serp/result?q=' . urlencode($query).'&deep_search='.$deepSearch));
        } catch (\RuntimeException $e) {
            if (strpos($e->getMessage(), 'timed out') !== false) {
                // Show a toast error for timeout
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Timeout koneksi ke Google API. Silakan coba lagi nanti.');
            } else if (strpos($e->getMessage(), 'SERP API Error') !== false) {
                // Show a toast error for other API errors
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Koneksi ke Google API gagal: ' . $e->getMessage());
            }
            
            // For other unexpected errors, rethrow
            throw $e;
        }
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
            $pdf->SetTitle('Analisis Hasil Pencarian');
            $pdf->SetSubject('Laporan Analisis Hasil Pencarian');
            $pdf->SetKeywords('Pencarian, Hasil, Analisis, Laporan');
            
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
                    
                    if (strpos($result['viral'], 'Likely') !== false) {
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
            
            // Results table header
            $pdf->SetFont('dejavusans', 'B', 10);
            $pdf->SetFillColor(230, 230, 230);
            $pdf->Cell(10, 10, '#', 1, 0, 'C', 1);
            $pdf->Cell(60, 10, 'Judul', 1, 0, 'C', 1);
            $pdf->Cell(40, 10, 'Tanggal', 1, 0, 'C', 1);
            $pdf->Cell(40, 10, 'Sentimen', 1, 0, 'C', 1);
            $pdf->Cell(40, 10, 'Prediksi Viral', 1, 1, 'C', 1);
            
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
                $title = $result['title'];
                if (strlen($title) > 30) {
                    $title = substr($title, 0, 27) . '...';
                }
                $pdf->Cell(60, 10, $title, 1, 0);
                
                // Published date
                $publishedDate = '';
                if (!empty($result['date'])) {
                    $publishedDate = $result['date'];
                } elseif (!empty($result['published_date'])) {
                    $publishedDate = $result['published_date'];
                }
                $pdf->Cell(40, 10, $publishedDate, 1, 0, 'C');
                
                // Sentiment with color
                $sentiment = !empty($result['sentiment']) ? $result['sentiment'] : 'Belum dianalisis';
                
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
                
                $pdf->Cell(40, 10, $sentimentDisplay, 1, 0, 'C');
                $pdf->SetTextColor(0, 0, 0); // Reset to black
                
                // Viral prediction
                $viral = !empty($result['viral']) ? $result['viral'] : 'Belum dianalisis';
                if ($viral == 'Likely Viral') {
                    $viral = 'Berpotensi Viral';
                } else if ($viral == 'Not Likely Viral') {
                    $viral = 'Tidak Berpotensi Viral';
                }
                $pdf->Cell(40, 10, $viral, 1, 1, 'C');
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
                $pdf->Cell(0, 10, 'Hasil #' . ($i + 1) . ': ' . $result['title'], 0, 1);
                
                // Link
                $pdf->SetFont('dejavusans', '', 10);
                $pdf->SetTextColor(0, 0, 255); // Blue
                $pdf->Cell(20, 8, 'URL:', 0, 0);
                $pdf->Cell(0, 8, $result['link'], 0, 1);
                $pdf->SetTextColor(0, 0, 0); // Reset to black
                
                // Snippet
                $pdf->SetFont('dejavusans', 'B', 10);
                $pdf->Cell(0, 8, 'Cuplikan Konten:', 0, 1);
                $pdf->SetFont('dejavusans', '', 10);
                $pdf->MultiCell(0, 8, $result['snippet'], 0, 'L');
                
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
                    $pdf->Cell(40, 8, 'Prediksi Viral:', 0, 0);
                    $viral = $result['viral'];
                    if ($viral == 'Likely Viral') {
                        $viral = 'Berpotensi Viral';
                    } else if ($viral == 'Not Likely Viral') {
                        $viral = 'Tidak Berpotensi Viral';
                    }
                    $pdf->Cell(0, 8, $viral, 0, 1);
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
        $deepSearch = (bool)$this->request->getGet('deep_search', FILTER_VALIDATE_BOOLEAN);
        
        // Validate query
        if (empty($query) || strlen($query) < 3) {
            return redirect()->to('/serp')->with('error', 'Kata kunci pencarian diperlukan (minimal 3 karakter)');
        }
        
        // Perform OSINT analysis
        $osintAnalysis = $this->osintAnalyzer->analyzeTrend($query);

        // Save keyword to database
        $this->keywordModel->save([
            'keyword' => $query,
            'search_count' => 1,
            'last_searched' => date('Y-m-d H:i:s')
        ]);
        
        // Prepare search parameters
        $searchParams = [
            'engine' => 'google',
            'google_domain' => 'google.co.id',
            'gl' => 'id',
            'hl' => 'id',
            'num' => 20,
            'tbm' => 'nws',       // Search for news results
            'tbs' => 'qdr:d'      // Sort by date (newest first)
        ];
        
        // Add deep search parameters if enabled
        if ($deepSearch) {
            $searchParams['tbs'] = 'qdr:y,sbd:1'; // Past year, sorted by date
        }
        
        try {
            // Perform search
            $results = $this->serpApi->search($query, $searchParams);
            
            // Process the results to ensure dates are properly formatted
            $processedResults = [];
            $newsResults = $results['news_results'] ?? ($results['organic_results'] ?? []);
            
            foreach ($newsResults as $result) {
                // Extract and process date if available
                if (!empty($result['date'])) {
                    // Date format can vary, so try to standardize it
                    try {
                        $dateObj = new \DateTime($result['date']);
                        $result['formatted_date'] = $dateObj->format('Y-m-d H:i:s');
                    } catch (\Exception $e) {
                        // If date parsing fails, keep original
                        $result['formatted_date'] = $result['date'];
                    }
                } else if (!empty($result['published_date'])) {
                    try {
                        $dateObj = new \DateTime($result['published_date']);
                        $result['formatted_date'] = $dateObj->format('Y-m-d H:i:s');
                    } catch (\Exception $e) {
                        $result['formatted_date'] = $result['published_date'];
                    }
                } else if (!empty($result['snippet']) && preg_match('/(\d{1,2}\s+\w+\s+\d{4})|(\d{4}-\d{1,2}-\d{1,2})/', $result['snippet'], $matches)) {
                    // Try to extract date from snippet if in common format
                    try {
                        $dateObj = new \DateTime($matches[0]);
                        $result['formatted_date'] = $dateObj->format('Y-m-d H:i:s');
                    } catch (\Exception $e) {
                        // Ignore if parsing fails
                    }
                }
                
                $processedResults[] = $result;
            }
            
            // Get user's recent searches
            $userId = $this->ionAuth->user()->row()->id;
            $recentSearches = $this->keywordModel->getUserHistory($userId, 5);
            
            // Prepare data for view
            $data = [
                'title' => 'Search Results: ' . $query,
                'query' => $query,
                'results' => $processedResults,
                'osintAnalysis' => $osintAnalysis,
                'deepSearch' => $deepSearch,
                'Pengaturan' => $this->pengaturan,
                'user' => $this->ionAuth->user()->row(),
                'isMenuActive' => isMenuActive('serp') ? 'active' : '',
                'recentSearches' => $recentSearches
            ];
            
            return view($this->theme->getThemePath() . '/serp/results', $data);
        } catch (\RuntimeException $e) {
            if (strpos($e->getMessage(), 'timed out') !== false) {
                return redirect()->to('/serp')
                    ->with('error', 'Timeout koneksi ke Google API. Silakan coba lagi nanti.');
            } else if (strpos($e->getMessage(), 'SERP API Error') !== false) {
                return redirect()->to('/serp')
                    ->with('error', 'Koneksi ke Google API gagal: ' . $e->getMessage());
            }
            
            // For other unexpected errors, rethrow
            throw $e;
        }
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
            
            foreach ($results as $i => $result) {
                $contentList .= ($i + 1) . '. ' . ($i < 1 ? 'Akun media sosial: ' : 'Website: ');
                
                // Use published date if available
                $publishedDate = date('Y-m-d');
                if (!empty($result['date'])) {
                    $publishedDate = date('Y-m-d', strtotime($result['date']));
                } elseif (!empty($result['published_date'])) {
                    $publishedDate = date('Y-m-d', strtotime($result['published_date']));
                }
                
                $contentList .= $publishedDate . ' ' . $result['link'] . ' - ' . $result['snippet'] . "\n";
                
                if (!empty($result['sentiment'])) {
                    $sentimentAnalysis .= $result['sentiment'] . "\n";
                }
                
                // Extract keywords from title
                $words = explode(' ', $result['title']);
                $keywords = array_slice($words, 0, min(3, count($words)));
                $keywordPrediction .= implode(', ', $keywords) . ', ';
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
            
            $formattedText .= "Selamat {$greeting} Komandan, Mohon ijin melaporkan pada hari {$hari_laporan} <division> melaksanakan Patroli Cyber di Media Sosial ";
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
                    $snippet = strtolower($result['snippet']);
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
            $formattedText .= "Prediksi :\n";
            $formattedText .= "A. Konten narasi negatif akan terus muncul sampai tujuan yang diinginkan tercapai yaitu pembatalan UU TNI, Revisi RUU POLRI dan perlawanan terhadap kebijakan pemerintah dengan issu tema Indonesia Gelap serta pengakuan Ijazah Palsu Jokowi dengan cara provokasi di seluruh media sosial.\n";
            $formattedText .= "B. Konten narasi negatif di media sosial mulai mengarah pada ajakan dan provokasi untuk melakukan pergerakan / perlawanan secara masif dan sistimatis pada peringatan mayday di seluruh daerah wilayah Jawa Tengah.\n\n";
            
            $formattedText .= "Rekomendasi :\n";
            $formattedText .= "A. Laksanakan pulbaket dan penyelidikan lebih mendalam terhadap pegiat media sosial yang telah membuat konten narasi negatif dan provokatif sehingga dapat diketahui maksud dan tujuannya.\n";
            $formattedText .= "B. Laksanakan kontra terhadap konten narasi negatif beserta pemilik akun medsos agar konten tersebut tidak  berkembang menjadi viral dan menggiring opini negatif serta memprovokasi masyarakat untuk melakukan perlawanan terhadap pemerintah dengan cara berunjuk rasa.\n";
            $formattedText .= "C. Laksanakan kontra dengan pembuatan konten isu tandingan yg berbeda guna penggembosan konten narasi negatif terkait penolakan UU TNI,  RUU Polri, Aksi Mayday dan kebijakan pemerintah dgn isu Indonesia gelap.\n\n";
            
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
} 