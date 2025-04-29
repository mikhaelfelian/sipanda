<?php
/**
 * Created by:
 * Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * 2025-01-13
 * 
 * Penjamin Model
 * 
 * Model for managing insurance/guarantor (penjamin) data
 * Handles CRUD operations and data validation
 */

namespace App\Models;

use CodeIgniter\Model;

/**
 * PenjaminModel
 * 
 * Handles database operations for penjamin (insurance/guarantor) data
 * 
 * @author    Mikhael Felian Waskito <mikhaelfelian@gmail.com>
 * @date      2025-02-06
 */
class PenjaminModel extends Model
{
    protected $table            = 'tbl_m_penjamin';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'kode',
        'penjamin',
        'persen',
        'status',
        'nama',
        'keterangan',
        'created_at',
        'updated_at'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules = [
        'penjamin' => 'required|max_length[160]',
        'kode' => 'permit_empty|max_length[160]',
        'persen' => 'permit_empty|numeric|greater_than_equal_to[0]|less_than_equal_to[100]',
        'status' => 'required|in_list[0,1]'
    ];
    
    protected $validationMessages = [
        'penjamin' => [
            'required' => 'Nama penjamin harus diisi',
            'max_length' => 'Nama penjamin maksimal 160 karakter'
        ],
        'kode' => [
            'max_length' => 'Kode maksimal 160 karakter'
        ],
        'persen' => [
            'numeric' => 'Persentase harus berupa angka',
            'greater_than_equal_to' => 'Persentase tidak boleh kurang dari 0',
            'less_than_equal_to' => 'Persentase tidak boleh lebih dari 100'
        ],
        'status' => [
            'required' => 'Status harus dipilih',
            'in_list' => 'Status tidak valid'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    /**
     * Generate unique code for penjamin
     * Format: PJMyymmXXX where XXX is sequential number
     * 
     * @return string
     */
    public function generateKode()
    {
        $prefix = 'PJM' . date('ym');
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