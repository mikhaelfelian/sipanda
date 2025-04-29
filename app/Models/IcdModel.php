<?php
/**
 * Created by:
 * Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * 2025-01-18
 * 
 * IcdModel
 * 
 * This model handles database operations for ICD (International Classification of Diseases) data
 */

namespace App\Models;

use CodeIgniter\Model;

class IcdModel extends Model
{
    protected $table            = 'tbl_m_icd';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'kode', 'icd', 'diagnosa_en', 'diagnosa_id'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Generate unique ICD code
     */
    public function generateKode()
    {
        $prefix = 'ICD';
        $lastKode = $this->select('kode')
                        ->like('kode', $prefix, 'after')
                        ->orderBy('kode', 'DESC')
                        ->first();

        if (!$lastKode) {
            return $prefix . '0001';
        }

        $lastNumber = (int)substr($lastKode->kode, strlen($prefix));
        $newNumber = $lastNumber + 1;
        return $prefix . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    public function getIcdList()
    {
        return $this->select('id, kode, icd')
                    ->orderBy('kode', 'ASC')
                    ->findAll();
    }
} 