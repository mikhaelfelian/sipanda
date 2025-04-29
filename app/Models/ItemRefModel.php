<?php

namespace App\Models;

use CodeIgniter\Model;

class ItemRefModel extends Model
{
    protected $table            = 'tbl_m_item_ref';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_item',
        'id_item_ref',
        'id_satuan',
        'id_user',
        'item',
        'harga',
        'jml',
        'jml_satuan',
        'subtotal',
        'status'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules = [
        'id_item'     => 'permit_empty|integer',
        'id_item_ref' => 'permit_empty|integer',
        'id_satuan'   => 'permit_empty|integer',
        'id_user'     => 'permit_empty|integer',
        'item'        => 'permit_empty|max_length[160]',
        'harga'       => 'permit_empty|numeric',
        'jml'         => 'permit_empty|numeric',
        'jml_satuan'  => 'permit_empty|integer',
        'subtotal'    => 'permit_empty|numeric',
        'status'      => 'permit_empty|integer'
    ];

    /**
     * Get item reference with relations
     */
    public function getItemRefWithRelations($id = null)
    {
        $builder = $this->db->table($this->table . ' ir')
            ->select('ir.*, i.kode as item_kode, i.item as item_name, s.satuanBesar')
            ->join('tbl_m_item i', 'i.id = ir.id_item', 'left')
            ->join('tbl_m_satuan s', 's.id = ir.id_satuan', 'left');

        if ($id !== null) {
            return $builder->where('ir.id', $id)->get()->getRow();
        }

        return $builder->get()->getResult();
    }

    /**
     * Get references by item ID
     */
    public function getRefsByItem($itemId)
    {
        return $this->where('id_item', $itemId)->findAll();
    }

    /**
     * Calculate subtotal
     */
    public function calculateSubtotal($jml, $harga)
    {
        return $jml * $harga;
    }
} 