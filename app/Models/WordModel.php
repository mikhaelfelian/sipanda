<?php
/**
 * WordModel
 * 
 * This model handles the dictionary of words used for sentiment analysis,
 * with methods to retrieve positive, negative, and categorized words.
 *
 * @author    Mikhael Felian Waskito <mikhaelfelian@gmail.com>
 * @created   2025-04-30
 */

namespace App\Models;

use CodeIgniter\Model;

class WordModel extends Model
{
    /**
     * Table name
     *
     * @var string
     */
    protected $table            = 'tbl_m_words';
    
    /**
     * Primary key field
     *
     * @var string
     */
    protected $primaryKey       = 'id';
    
    /**
     * Use auto increment for primary key
     *
     * @var bool
     */
    protected $useAutoIncrement = true;
    
    /**
     * Return type of model methods
     *
     * @var string
     */
    protected $returnType       = 'array';
    
    /**
     * Enable soft deletes
     *
     * @var bool
     */
    protected $useSoftDeletes   = true;
    
    /**
     * Enable field protection
     *
     * @var bool
     */
    protected $protectFields    = true;
    
    /**
     * Fields that can be mass assigned
     *
     * @var array
     */
    protected $allowedFields    = [
        'word', 'status_word', 'language', 'weight', 'category'
    ];

    /**
     * Enable timestamps
     *
     * @var bool
     */
    protected $useTimestamps = true;
    
    /**
     * Date format
     *
     * @var string
     */
    protected $dateFormat    = 'datetime';
    
    /**
     * Created at field name
     *
     * @var string
     */
    protected $createdField  = 'created_at';
    
    /**
     * Updated at field name
     *
     * @var string
     */
    protected $updatedField  = 'updated_at';
    
    /**
     * Deleted at field name
     *
     * @var string
     */
    protected $deletedField  = 'deleted_at';

    /**
     * Validation rules
     *
     * @var array
     */
    protected $validationRules      = [
        'word'        => 'required|min_length[1]|max_length[255]',
        'status_word' => 'required|in_list[1,2]',
    ];
    
    /**
     * Validation messages
     *
     * @var array
     */
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
    
    /**
     * Skip validation
     *
     * @var bool
     */
    protected $skipValidation       = false;
    
    /**
     * Clean validation rules
     *
     * @var bool
     */
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