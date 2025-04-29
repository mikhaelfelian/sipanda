<?php
/**
 * Created by:
 * Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * 2025-01-30
 * 
 * Item History Model
 */

namespace App\Models;

use CodeIgniter\Model;

class ItemHistModel extends Model
{
    protected $table            = 'tbl_m_item_hist';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_item',
        'id_satuan',
        'id_gudang',
        'id_user',
        'id_pelanggan',
        'id_supplier',
        'id_penjualan',
        'id_pembelian',
        'id_pembelian_det',
        'id_so',
        'tgl_masuk',
        'tgl_ed',
        'no_nota',
        'kode',
        'kode_batch',
        'item',
        'keterangan',
        'nominal',
        'jml',
        'jml_satuan',
        'satuan',
        'status',
        'sp'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Get status label
     */
    public function getStatusLabel($status)
    {
        $labels = [
            '1' => 'Stok Masuk Pembelian',
            '2' => 'Stok Masuk',
            '3' => 'Stok Masuk Retur Jual',
            '4' => 'Stok Keluar Penjualan',
            '5' => 'Stok Keluar Retur Beli',
            '6' => 'SO',
            '7' => 'Stok Keluar',
            '8' => 'Mutasi Antar Gudang'
        ];

        return $labels[$status] ?? '-';
    }

    /**
     * Get item history with relations
     */
    public function getWithRelations($id = null)
    {
        $builder = $this->db->table($this->table)
            ->select('tbl_m_item_hist.*, tbl_m_item.item as item_name, tbl_m_gudang.gudang')
            ->join('tbl_m_item', 'tbl_m_item.id = tbl_m_item_hist.id_item')
            ->join('tbl_m_gudang', 'tbl_m_gudang.id = tbl_m_item_hist.id_gudang')
            ->orderBy('tbl_m_item_hist.id', 'DESC');

        if ($id !== null) {
            $builder->where('tbl_m_item_hist.id_item', $id);
        }

        return $builder->get()->getResult();
    }
} 