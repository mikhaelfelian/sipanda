<?php
/**
 * Created by:
 * Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * 2025-01-13
 * 
 * Poli Model
 * 
 * Model for managing clinic/department (poli) data
 * Handles CRUD operations and data validation
 */

namespace App\Models;

use CodeIgniter\Model;

class PoliModel extends Model
{
    protected $table            = 'tbl_m_poli';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'kode',
        'poli',
        'keterangan',
        'post_location',
        'status'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules = [
        'poli' => 'required|max_length[64]',
        'kode' => 'permit_empty|max_length[64]',
        'status' => 'required|in_list[0,1]'
    ];
    
    protected $validationMessages = [
        'poli' => [
            'required' => 'Nama poli harus diisi',
            'max_length' => 'Nama poli maksimal 64 karakter'
        ],
        'kode' => [
            'max_length' => 'Kode maksimal 64 karakter'
        ],
        'status' => [
            'required' => 'Status harus dipilih',
            'in_list' => 'Status tidak valid'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    /**
     * Generate unique code for poli
     * Format: POLyymm-XX where XX is random number
     * 
     * @return string
     */
    public function generateKode()
    {
        $prefix = 'PL' . date('ym');
        $lastCode = $this->select('kode')
            ->like('kode', $prefix, 'after')
            ->orderBy('kode', 'DESC')
            ->first();

        if ($lastCode) {
            $lastNumber = (int) substr($lastCode->kode, -3);
            $newNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '001';
        }

        return $prefix . $newNumber;
    }
} 