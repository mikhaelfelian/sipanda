<?php
/**
 * InsightsDemo Controller
 * 
 * This controller demonstrates how to use the PHPInsights library
 * from any controller in the application.
 * 
 * @author    Mikhael Felian Waskito <mikhaelfelian@gmail.com>
 * @created   2025-04-30
 */

namespace App\Controllers;

use App\Libraries\PHPInsights;

class InsightsDemo extends BaseController
{
    /**
     * PHPInsights library instance
     * 
     * @var PHPInsights
     */
    protected $insights;
    
    /**
     * Constructor initializes the PHPInsights library
     */
    public function __construct()
    {
        parent::__construct();
        // Initialize the PHPInsights library
        $this->insights = new PHPInsights();
    }
    
    /**
     * Index method to demonstrate using PHPInsights
     * 
     * @return string
     */
    public function index()
    {
        // Check if user is logged in
        if (!$this->ionAuth->loggedIn()) {
            return redirect()->to('/auth/login');
        }
        
        // Sample texts for demo
        $positiveText = "I love this product. It's amazing and works great!";
        $negativeText = "This product is terrible and doesn't work at all.";
        
        // Using PHPInsights to analyze sentiments
        $positiveAnalysis = $this->insights->analyzeSentiment($positiveText);
        $negativeAnalysis = $this->insights->analyzeSentiment($negativeText);
        
        // Compare the two texts
        $comparison = $this->insights->compareSentiment($positiveText, $negativeText);
        
        // Find top categories in a text
        $topCategories = $this->insights->findTopCategories("I love technology and software development, but I hate bugs and errors.");
        
        // Prepare view data
        $data = [
            'title'            => 'PHPInsights Demo',
            'Pengaturan'       => $this->pengaturan,
            'user'             => $this->ionAuth->user()->row(),
            'isMenuActive'     => isMenuActive('insights') ? 'active' : '',
            'positiveText'     => $positiveText,
            'negativeText'     => $negativeText,
            'positiveAnalysis' => $positiveAnalysis,
            'negativeAnalysis' => $negativeAnalysis,
            'comparison'       => $comparison,
            'topCategories'    => $topCategories
        ];
        
        // Render the view
        return view($this->theme->getThemePath() . '/insights/demo', $data);
    }
    
    /**
     * API endpoint to analyze text sentiment
     * 
     * @return \CodeIgniter\HTTP\Response
     */
    public function apiAnalyze()
    {
        // Get text from query parameter
        $text = $this->request->getGet('text');
        
        if (empty($text)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Text parameter is required'
            ])->setStatusCode(400);
        }
        
        // Analyze sentiment using PHPInsights
        $analysis = $this->insights->analyzeSentiment($text);
        
        // Return JSON response
        return $this->response->setJSON([
            'success' => true,
            'data' => $analysis
        ]);
    }
    
    /**
     * Compare two texts
     * 
     * @return \CodeIgniter\HTTP\Response
     */
    public function apiCompare()
    {
        // Get texts from query parameters
        $text1 = $this->request->getGet('text1');
        $text2 = $this->request->getGet('text2');
        
        if (empty($text1) || empty($text2)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Both text1 and text2 parameters are required'
            ])->setStatusCode(400);
        }
        
        // Compare sentiments using PHPInsights
        $comparison = $this->insights->compareSentiment($text1, $text2);
        
        // Return JSON response
        return $this->response->setJSON([
            'success' => true,
            'data' => $comparison
        ]);
    }
} 