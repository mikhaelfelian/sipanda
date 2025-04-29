<?php

namespace App\Models;

use CodeIgniter\Model;

class ItemStokModel extends Model
{
    protected $table            = 'tbl_m_item_stok';
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
        'jml',
        'status'
    ];

    // Tanggal
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validasi
    protected $validationRules = [
        'id_item'    => 'required|integer',
        'id_gudang'  => 'permit_empty|integer',
        'id_satuan'  => 'permit_empty|integer',
        'jml'        => 'permit_empty|numeric',
        'status'     => 'permit_empty|in_list[0,1,2]',
    ];

    /**
     * Mendapatkan stok dengan relasi (item, gudang, satuan)
     */
    public function getStockWithRelations($id = null)
    {
        $builder = $this->db->table($this->table . ' s')
            ->select('
                s.*, 
                i.kode as item_kode, 
                i.item, 
                g.gudang,
                g.status_gd,
                st.satuanBesar as satuan
            ')
            ->join('tbl_m_item i', 'i.id = s.id_item', 'left')
            ->join('tbl_m_gudang g', 'g.id = s.id_gudang', 'left')
            ->join('tbl_m_satuan st', 'st.id = s.id_satuan', 'left')
            ->where('i.status_hps', '0')
            ->where('s.status', '1');

        if ($id !== null) {
            $builder->where('s.id_item', $id);
        }

        return $builder->get()->getResult();
    }

    /**
     * Mendapatkan stok berdasarkan ID item
     */
    public function getStockByItem($itemId)
    {
        return $this->db->table($this->table . ' s')
            ->select('
                s.*, 
                i.kode as item_kode, 
                i.item, 
                g.gudang,
                g.status_gd,
                st.satuanBesar as satuan
            ')
            ->join('tbl_m_item i', 'i.id = s.id_item', 'left')
            ->join('tbl_m_gudang g', 'g.id = s.id_gudang', 'left')
            ->join('tbl_m_satuan st', 'st.id = s.id_satuan', 'left')
            ->where('s.id_item', $itemId)
            ->where('i.status_hps', '0')
            ->where('s.status', '1')
            ->get()
            ->getResult();
    }

    /**
     * Get total stock for an item across all warehouses
     * 
     * @param int $itemId The ID of the item
     * @return int Total stock quantity
     */
    public function getTotalStockByItem($itemId)
    {
        return $this->where('id_item', $itemId)
                   ->selectSum('jml')
                   ->get()
                   ->getRow()
                   ->jml ?? 0;
    }

    /**
     * Mendapatkan stok berdasarkan ID item dan ID gudang
     */
    public function getStockByItemAndGudang($itemId, $gudangId)
    {
        return $this->where([
            'id_item'   => $itemId,
            'id_gudang' => $gudangId,
            'status'    => '1'
        ])->first();
    }

    /**
     * Memperbarui atau membuat stok
     */
    public function updateStock($itemId, $gudangId, $qty, $status = '1')
    {
        $existingStock = $this->getStockByItemAndGudang($itemId, $gudangId);

        if ($existingStock) {
            $data = [
                'jml'    => $existingStock->jml + $qty,
                'status' => $status
            ];
            return $this->update($existingStock->id, $data);
        } else {
            $data = [
                'id_item'   => $itemId,
                'id_gudang' => $gudangId,
                'jml'       => $qty,
                'status'    => $status,
                'id_user'   => user_id()
            ];
            return $this->insert($data);
        }
    }

    /**
     * Mendapatkan item stok rendah
     */
    public function getLowStockItems()
    {
        return $this->db->table($this->table . ' s')
            ->select('s.*, i.kode as item_kode, i.item, i.jml_limit, g.gudang')
            ->join('tbl_m_item i', 'i.id = s.id_item')
            ->join('tbl_m_gudang g', 'g.id = s.id_gudang')
            ->where('s.jml <=', 'i.jml_limit', false)
            ->where('s.status', '1')
            ->where('i.status_hps', '0')
            ->get()
            ->getResult();
    }
} 