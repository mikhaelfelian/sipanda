<?php

namespace App\Controllers;

use App\Models\WordModel;

class Words extends BaseController
{
    protected $wordModel;

    public function __construct()
    {
        $this->wordModel = new WordModel();
    }

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
} 