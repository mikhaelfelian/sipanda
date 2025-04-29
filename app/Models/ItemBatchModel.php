<?php
/**
 * Created by:
 * Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * 2025-01-19
 * 
 * ItemBatchModel
 * 
 * This model handles database operations for item batch data
 */

namespace App\Models;

use CodeIgniter\Model;

class ItemBatchModel extends Model
{
    protected $table            = 'tbl_m_item_batch';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_item', 'id_gudang', 'id_pembelian', 'id_pembelian_det', 
        'id_user', 'tgl_terima', 'tgl_ed', 'kode', 'kode_batch', 
        'item', 'jml', 'jml_keluar', 'jml_sisa', 'status'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Generate unique batch code
     */
    public function generateKode()
    {
        $prefix = 'BTH';
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

    /**
     * Get status label
     */
    public function getStatusLabel($status)
    {
        return $status === '1' ? 'Aktif' : 'Tidak Aktif';
    }

    /**
     * Get item batch with relations
     */
    public function getWithRelations($id = null)
    {
        $builder = $this->db->table($this->table)
            ->select('tbl_m_item_batch.*, tbl_m_item.item as item_name, tbl_m_gudang.gudang')
            ->join('tbl_m_item', 'tbl_m_item.id = tbl_m_item_batch.id_item')
            ->join('tbl_m_gudang', 'tbl_m_gudang.id = tbl_m_item_batch.id_gudang');

        if ($id !== null) {
            return $builder->where('tbl_m_item_batch.id', $id)
                         ->get()
                         ->getRow();
        }

        return $builder->get()->getResult();
    }

    /**
     * Get batches by item ID
     * 
     * @param int $itemId Item ID
     * @return array Batch data
     */
    public function getBatchesByItem($itemId)
    {
        return $this->select('
                tbl_m_item_batch.*,
                tbl_m_gudang.gudang
            ')
            ->join('tbl_m_gudang', 'tbl_m_gudang.id = tbl_m_item_batch.id_gudang', 'left')
            ->where('tbl_m_item_batch.id_item', $itemId)
            ->where('tbl_m_item_batch.status', '1')
            ->orderBy('tbl_m_item_batch.tgl_ed', 'ASC')
            ->findAll();
    }
} 