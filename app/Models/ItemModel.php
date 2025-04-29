<?php
/**
 * Created by:
 * Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * 2025-01-19
 * 
 * ItemModel
 */

namespace App\Models;

use CodeIgniter\Model;

class ItemModel extends Model
{
    protected $table = 'tbl_m_item';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'object';
    protected $useSoftDeletes = true;
    protected $protectFields = true;
    protected $allowedFields = [
        'kode',
        'item',
        'item_alias',
        'item_kand',
        'barcode',
        'id_satuan',
        'id_kategori',
        'id_kategori_obat',
        'id_merk',
        'jml',
        'jml_min',
        'jml_limit',
        'harga_beli',
        'harga_jual',
        'remun_tipe',
        'remun_perc',
        'remun_nom',
        'apres_tipe',
        'apres_perc',
        'apres_nom',
        'status',
        'status_stok',
        'status_racikan',
        'status_item',
        'id_user',
        'status_hps'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    /**
     * Generate unique kode for item
     * Format: OBT-001, OBT-002, etc
     */
    public function generateKode($status_item = null)
    {
        switch ($status_item) {
            case 1:
                $prefix = 'OBT' . date('ym') . rand(10, 99);
                break;

            case 2:
                $prefix = 'TND' . date('ym') . rand(10, 99);
                break;

            case 3:
                $prefix = 'LAB' . date('ym') . rand(10, 99);
                break;

            case 4:
                $prefix = 'RAD' . date('ym') . rand(10, 99);
                break;

            case 5:
                $prefix = 'BHP' . date('ym') . rand(10, 99);
                break;

            default:
                $prefix = '-';
                break;
        }

        $lastKode = $this->select('kode')
            ->where('status_item', $status_item)
            ->orderBy('kode', 'DESC')
            ->first();

        if (!$lastKode) {
            return $prefix . '00001';
        }

        $lastNumber = (int) substr($lastKode->kode, strlen($prefix));
        $newNumber  = $lastNumber + 1;

        return $prefix . str_pad($newNumber, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Get item with all its relations
     */
    public function getItemWithRelations($id = null)
    {
        $builder = $this->db->table($this->table . ' i')
            ->select('i.*, s.satuanBesar as satuan, k.kategori, m.merk, ko.jenis')
            ->join('tbl_m_satuan s', 's.id = i.id_satuan', 'left')
            ->join('tbl_m_kategori k', 'k.id = i.id_kategori', 'left')
            ->join('tbl_m_merk m', 'm.id = i.id_merk', 'left')
            ->join('tbl_m_kategori_obat ko', 'ko.id = i.id_kategori_obat', 'left');

        if ($id !== null) {
            return $builder->where('i.id', $id)->get()->getRow();
        }

        return $builder->get()->getResult();
    }

    /**
     * Soft delete an item
     */
    public function delete($id = null, bool $purge = false)
    {
        if ($purge) {
            return parent::delete($id, true);
        }

        $data = [
            'status_hps' => '1',
            'deleted_at' => date('Y-m-d H:i:s')
        ];

        return $this->update($id, $data);
    }

    /**
     * Permanently delete an item
     * 
     * @param int|string|null $id The ID to delete
     * @return bool True on success, false on failure
     */
    public function delete_permanent($id = null)
    {
        return parent::delete($id, true);
    }

    /**
     * Override find methods to exclude soft deleted records
     */
    public function find($id = null)
    {
        $this->where('status_hps', '0');
        return parent::find($id);
    }

    /**
     * Override findAll to exclude soft deleted records
     * 
     * @param int|null $limit  The limit of records to return
     * @param int      $offset The record offset
     */
    public function findAll(?int $limit = null, int $offset = 0)
    {
        $this->where('status_hps', '0');
        return parent::findAll($limit, $offset);
    }

    /**
     * Get obat items only (status_item = 1)
     */
    public function getObat()
    {
        return $this->select('tbl_m_item.*, tbl_m_merk.merk, tbl_m_satuan.satuanBesar, tbl_m_satuan.satuanKecil, tbl_m_satuan.jml, tbl_m_kategori.kategori, tbl_m_kategori_obat.jenis')
            ->join('tbl_m_merk', 'tbl_m_merk.id = tbl_m_item.id_merk', 'left')
            ->join('tbl_m_satuan', 'tbl_m_satuan.id = tbl_m_item.id_satuan', 'left')
            ->join('tbl_m_kategori', 'tbl_m_kategori.id = tbl_m_item.id_kategori', 'left')
            ->join('tbl_m_kategori_obat', 'tbl_m_kategori_obat.id = tbl_m_item.id_kategori_obat', 'left')
            ->where('tbl_m_item.status_item', 1)
            ->where('tbl_m_item.status_hps', '0');
    }

    /**
     * Count soft deleted records
     */
    public function countDeleted($status_item = 1)  // Default to OBAT (1)
    {
        return $this->db->table($this->table)
            ->where('status_hps', '1')
            ->where('status_item', $status_item)
            ->countAllResults();
    }

    /**
     * Get deleted obat items
     */
    public function getObatTrash()
    {
        $builder = $this->db->table($this->table);
        $builder->select('
                tbl_m_item.*,
                tbl_m_satuan.satuanBesar,
                tbl_m_kategori.kategori,
                tbl_m_merk.merk
            ')
            ->join('tbl_m_satuan', 'tbl_m_satuan.id = tbl_m_item.id_satuan', 'left')
            ->join('tbl_m_kategori', 'tbl_m_kategori.id = tbl_m_item.id_kategori', 'left')
            ->join('tbl_m_merk', 'tbl_m_merk.id = tbl_m_item.id_merk', 'left')
            ->where('tbl_m_item.status_item', 1)
            ->where('tbl_m_item.status_hps', '1');

        return $builder;
    }

    public function getTindakan()
    {
        return $this->select('
                tbl_m_item.id,
                tbl_m_item.kode,
                tbl_m_item.item,
                tbl_m_item.item_alias,
                tbl_m_item.item_kand,
                tbl_m_item.harga_jual,
                tbl_m_item.status,
                tbl_m_item.status_item
            ')
            ->where('tbl_m_item.status_item', 2)
            ->where('tbl_m_item.status_hps', '0')->get()->getResult();
    }

    public function getTindakanTrash()
    {
        $builder = $this->db->table($this->table);
        return $builder->select('
                tbl_m_item.id,
                tbl_m_item.kode,
                tbl_m_item.item,
                tbl_m_item.item_alias,
                tbl_m_item.item_kand,
                tbl_m_item.harga_jual,
                tbl_m_item.status,
                tbl_m_item.status_item,
                tbl_m_item.deleted_at
            ')
            ->where('tbl_m_item.status_item', 2)
            ->where('tbl_m_item.status_hps', '1');
    }

    public function getLab(){
        return $this->select('
                tbl_m_item.id,
                tbl_m_item.kode,
                tbl_m_item.item,
                tbl_m_item.item_alias,
                tbl_m_item.item_kand,
                tbl_m_item.harga_beli,
                tbl_m_item.harga_jual,
                tbl_m_item.status,
                tbl_m_item.status_item,
                tbl_m_item.status_stok,
                tbl_m_kategori.kategori,
                tbl_m_merk.merk
            ')
            ->join('tbl_m_kategori', 'tbl_m_kategori.id = tbl_m_item.id_kategori', 'left')
            ->join('tbl_m_merk', 'tbl_m_merk.id = tbl_m_item.id_merk', 'left')
            ->where('tbl_m_item.status_item', 3)
            ->where('tbl_m_item.status_hps', '0');
    }

    public function getLabTrash(){
        $builder = $this->db->table($this->table);
        return $builder->select('
                tbl_m_item.id,
                tbl_m_item.kode,
                tbl_m_item.item,
                tbl_m_item.item_alias,
                tbl_m_item.item_kand,
                tbl_m_item.harga_jual,
                tbl_m_item.status,
                tbl_m_item.status_item,
                tbl_m_item.deleted_at
            ')
            ->where('tbl_m_item.status_item', 3)
            ->where('tbl_m_item.status_hps', '1');
    }

    public function getRadiologi(){
        return $this->select('
                tbl_m_item.id,
                tbl_m_item.kode,
                tbl_m_item.item,
                tbl_m_item.item_alias,
                tbl_m_item.item_kand,
                tbl_m_item.harga_jual,
                tbl_m_item.status,
                tbl_m_item.status_item,
                tbl_m_item.status_stok,
                tbl_m_kategori.kategori,
                tbl_m_merk.merk
            ')
            ->join('tbl_m_kategori', 'tbl_m_kategori.id = tbl_m_item.id_kategori', 'left')
            ->join('tbl_m_merk', 'tbl_m_merk.id = tbl_m_item.id_merk', 'left')
            ->where('tbl_m_item.status_item', 4)
            ->where('tbl_m_item.status_hps', '0');
    }

    public function getRadiologiTrash(){
        $builder = $this->db->table($this->table);
        return $builder->select('
                tbl_m_item.id,
                tbl_m_item.kode,
                tbl_m_item.item,
                tbl_m_item.item_alias,
                tbl_m_item.item_kand,
                tbl_m_item.harga_jual,
                tbl_m_item.status,
                tbl_m_item.status_item,
                tbl_m_item.deleted_at
            ')
            ->where('tbl_m_item.status_item', 4)
            ->where('tbl_m_item.status_hps', '1');
    }

    public function getBHP()
    {
        return $this->select('
                tbl_m_item.id,
                tbl_m_item.kode,
                tbl_m_item.item,
                tbl_m_item.item_alias,
                tbl_m_item.item_kand,
                tbl_m_item.harga_jual,
                tbl_m_item.status,
                tbl_m_item.status_item,
                tbl_m_item.status_stok,
                tbl_m_kategori.kategori,
                tbl_m_merk.merk
            ')
            ->join('tbl_m_kategori', 'tbl_m_kategori.id = tbl_m_item.id_kategori', 'left')
            ->join('tbl_m_merk', 'tbl_m_merk.id = tbl_m_item.id_merk', 'left')
            ->where('tbl_m_item.status_item', 5)  // 5 for BHP
            ->where('tbl_m_item.status_hps', '0');
    }

    /**
     * Get kategori list for dropdown
     */
    public function getKategoriList()
    {
        $builder = $this->db->table('tbl_m_kategori');
        $result = $builder->select('id, kategori')
                         ->where('status', '1')
                         ->orderBy('kategori', 'ASC')
                         ->get()
                         ->getResult();

        $list = [];
        foreach ($result as $row) {
            $list[$row->id] = $row->kategori;
        }
        return $list;
    }

    /**
     * Get merk list for dropdown
     */
    public function getMerkList()
    {
        return $this->select('DISTINCT(merk) as merk')
                   ->where('status', '1')
                   ->orderBy('merk', 'ASC')
                   ->get()
                   ->getResult();
    }

    /**
     * Get stockable items with filters and pagination
     * 
     * @param array $filters Filter parameters
     * @param bool $countOnly Return count only
     * @param int|null $limit Limit per page
     * @param int|null $offset Offset for pagination
     * @return mixed Count or results depending on $countOnly
     */
    public function getStockable($filters = [], $countOnly = false, $limit = null, $offset = null)
    {
        $builder = $this->select('
                tbl_m_item.*, 
                tbl_m_merk.merk,
                tbl_m_kategori.kategori,
                tbl_m_satuan.satuanBesar as satuan,
                SUM(tbl_m_item_stok.jml) as stok
            ')
            ->join('tbl_m_merk', 'tbl_m_merk.id = tbl_m_item.id_merk', 'left')
            ->join('tbl_m_kategori', 'tbl_m_kategori.id = tbl_m_item.id_kategori', 'left')
            ->join('tbl_m_satuan', 'tbl_m_satuan.id = tbl_m_item.id_satuan', 'left')
            ->join('tbl_m_item_stok', 'tbl_m_item_stok.id_item = tbl_m_item.id', 'left')
            ->where('tbl_m_item.status_stok', '1')
            ->groupBy('tbl_m_item_stok.id_item');

        // Apply filters
        if (!empty($filters['kategori'])) {
            $builder->where('tbl_m_item.id_kategori', $filters['kategori']);
        }

        if (!empty($filters['merk'])) {
            $builder->where('tbl_m_item.id_merk', $filters['merk']);
        }

        if (!empty($filters['item'])) {
            $builder->groupStart()
                    ->like('tbl_m_item.kode', $filters['item'])
                    ->orLike('tbl_m_item.item', $filters['item'])
                    ->groupEnd();
        }

        if (!empty($filters['harga'])) {
            $builder->where('tbl_m_item.harga_beli', str_replace(['Rp', '.', ','], '', $filters['harga']));
        }

        if ($countOnly) {
            return $builder->countAllResults();
        }

        if ($limit !== null) {
            $builder->limit($limit, $offset);
        }

        return $builder->get()->getResult();
    }

    /**
     * Get status label
     */
    public function getStatusLabel($status)
    {
        return $status === '1' ? 'Aktif' : 'Tidak Aktif';
    }

    /**
     * Get item with all its details
     */
    public function getItemDetail($id)
    {
        return $this->select('
                tbl_m_item.*,
                tbl_m_kategori.kategori as nama_kategori,
                tbl_m_merk.merk as nama_merk,
                tbl_m_satuan.satuanBesar as satuan
            ')
            ->join('tbl_m_kategori', 'tbl_m_kategori.id = tbl_m_item.id_kategori', 'left')
            ->join('tbl_m_merk', 'tbl_m_merk.id = tbl_m_item.id_merk', 'left')
            ->join('tbl_m_satuan', 'tbl_m_satuan.id = tbl_m_item.id_satuan', 'left')
            ->where('tbl_m_item.id', $id)
            ->first();
    }
}