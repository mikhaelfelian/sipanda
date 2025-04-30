<?php
/**
 * Words Controller
 * 
 * This controller manages the words dictionary for sentiment analysis
 * 
 * @author    Mikhael Felian Waskito <mikhaelfelian@gmail.com>
 * @created   2025-04-30
 */

namespace App\Controllers;

use App\Models\WordModel;
use App\Libraries\PHPInsights;

class Words extends BaseController
{
    /**
     * Word model instance
     * 
     * @var WordModel
     */
    protected $wordModel;
    
    /**
     * PHPInsights library instance
     * 
     * @var PHPInsights
     */
    protected $insights;

    /**
     * Constructor initializes models and libraries
     */
    public function __construct()
    {
        parent::__construct();
        $this->wordModel = new WordModel();
        $this->insights = new PHPInsights();
    }

    /**
     * Display the word management interface
     * 
     * @return \CodeIgniter\HTTP\RedirectResponse|string
     */
    public function index()
    {
        // Check if user is logged in
        if (!$this->ionAuth->loggedIn()) {
            return redirect()->to('/auth/login');
        }

        $data = [
            'title'         => 'Word Management',
            'Pengaturan'    => $this->pengaturan,
            'user'          => $this->ionAuth->user()->row(),
            'isMenuActive'  => isMenuActive('words') ? 'active' : '',
            'positiveWords' => $this->wordModel->getPositiveWords(),
            'negativeWords' => $this->wordModel->getNegativeWords(),
        ];

        return view($this->theme->getThemePath() . '/words/index', $data);
    }

    /**
     * Add a new word to the dictionary
     * 
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function add()
    {
        // Using GET parameters instead of POST
        $word = $this->request->getGet('word');
        $status = $this->request->getGet('status_word'); // 1=positive, 2=negative
        $language = $this->request->getGet('language') ?? 'en';
        $weight = $this->request->getGet('weight') ?? 1.00;
        $category = $this->request->getGet('category');

        // Check if word exists
        $existing = $this->wordModel->where('word', $word)->first();
        
        if ($existing) {
            return redirect()->to('words')
                ->with('error', 'Word already exists in the database');
        }

        // Validate and save the word
        $data = [
            'word' => $word,
            'status_word' => $status,
            'language' => $language,
            'weight' => $weight,
            'category' => $category
        ];

        if ($this->wordModel->save($data)) {
            return redirect()->to('words')
                ->with('message', 'Word added successfully');
        } else {
            return redirect()->to('words')
                ->with('errors', $this->wordModel->errors())
                ->withInput();
        }
    }

    /**
     * Edit an existing word in the dictionary
     * 
     * @param int|null $id Word ID
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function edit($id = null)
    {
        if ($id === null) {
            return redirect()->to('words')->with('error', 'Word ID required');
        }

        // Using GET parameters instead of POST
        $word = $this->request->getGet('word');
        $status = $this->request->getGet('status_word');
        $language = $this->request->getGet('language');
        $weight = $this->request->getGet('weight');
        $category = $this->request->getGet('category');

        // Update the word
        $data = [
            'id' => $id,
            'word' => $word,
            'status_word' => $status
        ];

        // Only add optional fields if they are provided
        if ($language !== null) $data['language'] = $language;
        if ($weight !== null) $data['weight'] = $weight;
        if ($category !== null) $data['category'] = $category;

        if ($this->wordModel->save($data)) {
            return redirect()->to('words')
                ->with('message', 'Word updated successfully');
        } else {
            return redirect()->to('words')
                ->with('errors', $this->wordModel->errors())
                ->withInput();
        }
    }

    /**
     * Delete a word from the dictionary
     * 
     * @param int|null $id Word ID
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function delete($id = null)
    {
        if ($id === null) {
            return redirect()->to('words')->with('error', 'Word ID required');
        }

        if ($this->wordModel->delete($id)) {
            return redirect()->to('words')
                ->with('message', 'Word deleted successfully');
        } else {
            return redirect()->to('words')
                ->with('error', 'Failed to delete word');
        }
    }

    /**
     * View positive words
     * 
     * @return \CodeIgniter\HTTP\RedirectResponse|string
     */
    public function viewPositive()
    {
        $language = $this->request->getGet('language') ?? 'en';
        
        $data = [
            'title'        => 'Positive Words',
            'Pengaturan'   => $this->pengaturan,
            'user'         => $this->ionAuth->user()->row(),
            'isMenuActive' => isMenuActive('words/positive') ? 'active' : '',
            'words'        => $this->wordModel->getPositiveWords($language),
            'language'     => $language
        ];

        return view($this->theme->getThemePath() . '/words/positive', $data);
    }

    /**
     * View negative words
     * 
     * @return \CodeIgniter\HTTP\RedirectResponse|string
     */
    public function viewNegative()
    {
        $language = $this->request->getGet('language') ?? 'en';
        
        $data = [
            'title'        => 'Negative Words',
            'Pengaturan'   => $this->pengaturan,
            'user'         => $this->ionAuth->user()->row(),
            'isMenuActive' => isMenuActive('words/negative') ? 'active' : '',
            'words'        => $this->wordModel->getNegativeWords($language),
            'language'     => $language
        ];

        return view($this->theme->getThemePath() . '/words/negative', $data);
    }

    /**
     * View words by category
     * 
     * @return \CodeIgniter\HTTP\RedirectResponse|string
     */
    public function viewByCategory()
    {
        $category = $this->request->getGet('category');
        $language = $this->request->getGet('language') ?? 'en';
        
        if (empty($category)) {
            return redirect()->to('words')
                ->with('error', 'Category parameter is required');
        }
        
        $data = [
            'title'        => 'Words by Category: ' . $category,
            'Pengaturan'   => $this->pengaturan,
            'user'         => $this->ionAuth->user()->row(),
            'isMenuActive' => isMenuActive('words/category') ? 'active' : '',
            'words'        => $this->wordModel->getWordsByCategory($category, $language),
            'category'     => $category,
            'language'     => $language
        ];

        return view($this->theme->getThemePath() . '/words/category', $data);
    }
    
    /**
     * Analyze text using PHPInsights library
     * 
     * @return \CodeIgniter\HTTP\RedirectResponse|string
     */
    public function analyze()
    {
        $text = $this->request->getGet('text');
        $language = $this->request->getGet('language') ?? 'en';
        
        if (empty($text)) {
            return redirect()->to('words')
                ->with('error', 'Text parameter is required');
        }
        
        $analysis = $this->insights->analyzeSentiment($text, $language);
        
        $data = [
            'title'        => 'Sentiment Analysis',
            'Pengaturan'   => $this->pengaturan,
            'user'         => $this->ionAuth->user()->row(),
            'isMenuActive' => isMenuActive('words/analyze') ? 'active' : '',
            'text'         => $text,
            'analysis'     => $analysis,
            'language'     => $language
        ];

        return view($this->theme->getThemePath() . '/words/analyze', $data);
    }
} 