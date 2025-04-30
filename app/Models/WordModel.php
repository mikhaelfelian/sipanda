<?php

namespace App\Models;

use CodeIgniter\Model;

class WordModel extends Model
{
    protected $table            = 'tbl_m_words';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'word', 'status_word', 'language', 'weight', 'category'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [
        'word'        => 'required|min_length[1]|max_length[255]',
        'status_word' => 'required|in_list[1,2]',
    ];
    protected $validationMessages   = [
        'word' => [
            'required'    => 'Word is required',
            'min_length'  => 'Word must have at least 1 character',
            'max_length'  => 'Word cannot exceed 255 characters',
        ],
        'status_word' => [
            'required' => 'Word status is required',
            'in_list'  => 'Word status must be either positive (1) or negative (2)',
        ],
    ];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    /**
     * Get all positive words
     * 
     * @param string $language Language code (default: 'en')
     * @return array
     */
    public function getPositiveWords($language = 'en')
    {
        return $this->where('status_word', 1)
                    ->where('language', $language)
                    ->findAll();
    }

    /**
     * Get all negative words
     * 
     * @param string $language Language code (default: 'en')
     * @return array
     */
    public function getNegativeWords($language = 'en')
    {
        return $this->where('status_word', 2)
                    ->where('language', $language)
                    ->findAll();
    }

    /**
     * Get words by category
     * 
     * @param string $category Category name
     * @param string $language Language code (default: 'en')
     * @return array
     */
    public function getWordsByCategory($category, $language = 'en')
    {
        return $this->where('category', $category)
                    ->where('language', $language)
                    ->findAll();
    }
} 