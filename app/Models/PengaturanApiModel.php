<?php

namespace App\Models;

use CodeIgniter\Model;

class PengaturanApiModel extends Model
{
    protected $table      = 'tbl_pengaturan_api';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType     = 'object';
    protected $useSoftDeletes = true;
    protected $protectFields  = true;
    protected $allowedFields = ['id_pengaturan', 'name', 'tokens', 'created_date'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [
        'id_pengaturan' => 'required|numeric',
        'name'          => 'required|min_length[3]|max_length[100]',
        'tokens'        => 'permit_empty',
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    /**
     * Get API tokens by name
     *
     * @param string $name API name
     * @return object|null
     */
    public function getTokenByName(string $name)
    {
        return $this->where('name', $name)
                    ->where('deleted_at', null)
                    ->first();
    }

    /**
     * Get all API tokens
     *
     * @return array
     */
    public function getAllTokens()
    {
        return $this->where('deleted_at', null)
                    ->findAll();
    }

    /**
     * Save API token
     *
     * @param array $data
     * @return bool
     */
    public function saveToken(array $data)
    {
        // Check if token with same name exists
        $existing = $this->getTokenByName($data['name']);
        
        if ($existing) {
            // Update existing token
            return $this->update($existing->id, $data);
        } else {
            // Add new token
            if (!isset($data['created_date'])) {
                $data['created_date'] = date('Y-m-d H:i:s');
            }
            return $this->insert($data);
        }
    }

    /**
     * Delete API token
     *
     * @param int|string $id
     * @return bool
     */
    public function deleteToken($id)
    {
        return $this->delete($id);
    }

    /**
     * Get API tokens by pengaturan ID
     *
     * @param int $idPengaturan
     * @return array
     */
    public function getTokensByPengaturanId(int $idPengaturan)
    {
        return $this->where('id_pengaturan', $idPengaturan)
                    ->where('deleted_at', null)
                    ->findAll();
    }
} 