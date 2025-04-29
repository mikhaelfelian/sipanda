<?php

namespace App\Models;

use CodeIgniter\Model;

class KeywordModel extends Model
{
    protected $table = 'tbl_keywords';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = true;
    protected $allowedFields = ['user_id', 'keyword', 'search_count', 'last_searched'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    /**
     * Save or update keyword
     * 
     * @param int $userId
     * @param string $keyword
     * @return bool
     */
    public function saveKeyword(int $userId, string $keyword): bool
    {
        // Check if keyword exists for this user
        $existing = $this->where('user_id', $userId)
                        ->where('keyword', $keyword)
                        ->first();

        if ($existing) {
            // Update existing keyword
            return $this->update($existing['id'], [
                'search_count' => $existing['search_count'] + 1,
                'last_searched' => date('Y-m-d H:i:s')
            ]);
        }

        // Create new keyword
        return $this->insert([
            'user_id' => $userId,
            'keyword' => $keyword,
            'search_count' => 1,
            'last_searched' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Get user's search history
     * 
     * @param int $userId
     * @param int $limit
     * @return array
     */
    public function getUserHistory(int $userId, int $limit = 10): array
    {
        return $this->where('user_id', $userId)
                    ->orderBy('last_searched', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }

    /**
     * Get most searched keywords
     * 
     * @param int $limit
     * @return array
     */
    public function getPopularKeywords(int $limit = 10): array
    {
        return $this->select('keyword, COUNT(*) as total_searches')
                    ->groupBy('keyword')
                    ->orderBy('total_searches', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }
} 