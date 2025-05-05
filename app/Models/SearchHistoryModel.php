<?php

namespace App\Models;

use CodeIgniter\Model;

class SearchHistoryModel extends Model
{
    protected $table      = 'tbl_search_history';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType     = 'object';
    protected $useSoftDeletes = true;
    protected $protectFields  = true;
    protected $allowedFields = [
        'user_id', 
        'search_type', 
        'search_query', 
        'search_engine', 
        'result_count',
        'search_date'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [
        'search_type'   => 'required',
        'search_query'  => 'required',
        'search_engine' => 'required'
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    /**
     * Log a search to history
     *
     * @param int $userId
     * @param string $searchType
     * @param string $searchQuery
     * @param string $searchEngine
     * @param int $resultCount
     * @return bool
     */
    public function logSearch($userId, $searchType, $searchQuery, $searchEngine, $resultCount = 0)
    {
        $data = [
            'user_id'       => $userId,
            'search_type'   => $searchType,
            'search_query'  => $searchQuery,
            'search_engine' => $searchEngine,
            'result_count'  => $resultCount,
            'search_date'   => date('Y-m-d H:i:s')
        ];

        return $this->insert($data) !== false;
    }

    /**
     * Get recent searches for a user
     *
     * @param int $userId
     * @param int $limit
     * @return array
     */
    public function getRecentSearches($userId, $limit = 10)
    {
        return $this->where('user_id', $userId)
                   ->orderBy('search_date', 'DESC')
                   ->limit((int)$limit)
                   ->findAll();
    }

    /**
     * Get recent searches by type
     *
     * @param int $userId
     * @param string $searchType
     * @param int $limit
     * @return array
     */
    public function getRecentSearchesByType($userId, $searchType, $limit = 10)
    {
        return $this->where('user_id', $userId)
                   ->where('search_type', $searchType)
                   ->orderBy('search_date', 'DESC')
                   ->limit((int)$limit)
                   ->findAll();
    }

    /**
     * Get recent searches by engine
     *
     * @param int $userId
     * @param string $searchEngine
     * @param int $limit
     * @return array
     */
    public function getRecentSearchesByEngine($userId, $searchEngine, $limit = 10)
    {
        return $this->where('user_id', $userId)
                   ->where('search_engine', $searchEngine)
                   ->orderBy('search_date', 'DESC')
                   ->limit((int)$limit)
                   ->findAll();
    }

    /**
     * Get popular search queries
     *
     * @param int $limit
     * @return array
     */
    public function getPopularSearches($limit = 10)
    {
        $builder = $this->db->table('tbl_search_history');
        $builder->select('search_query, COUNT(*) as search_count');
        $builder->groupBy('search_query');
        $builder->orderBy('search_count', 'DESC');
        $builder->limit((int)$limit);
        
        return $builder->get()->getResult();
    }
} 