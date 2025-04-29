<?php
/**
 * Created by:
 * Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * 2025-01-13
 * 
 * Lab controller
 */

namespace App\Models;

use CodeIgniter\Model;

class ItemRefInputModel extends Model
{
    protected $table            = 'tbl_m_item_ref_input';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_item',
        'id_user', 
        'item_name',
        'item_value',
        'item_value_l1',
        'item_value_l2', 
        'item_value_p1',
        'item_value_p2',
        'item_satuan'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // // Validation
    // protected $validationRules = [
    //     'id_item'       => 'permit_empty|integer',
    //     'id_user'       => 'permit_empty|integer',
    //     'item_name'     => 'permit_empty|max_length[160]',
    //     'item_value'    => 'permit_empty',
    //     'item_value_l1' => 'permit_empty',
    //     'item_value_l2' => 'permit_empty',
    //     'item_value_p1' => 'permit_empty', 
    //     'item_value_p2' => 'permit_empty',
    //     'item_satuan'   => 'permit_empty|max_length[100]'
    // ];

    /**
     * Get item reference inputs by item ID
     * 
     * @param int $itemId The ID of the laboratory item
     * @return array Array of item reference inputs
     */
    public function getByItemId($itemId)
    {
        return $this->where('id_item', $itemId)
                   ->orderBy('id', 'ASC')
                   ->findAll();
    }

    /**
     * Get item reference input with item details
     * 
     * @param int|null $id The ID of the item reference input
     * @return object|array Item reference input with item details
     */
    public function getWithItem($id = null)
    {
        $builder = $this->db->table($this->table)
            ->select('tbl_m_item_ref_input.*, tbl_m_item.item')
            ->join('tbl_m_item', 'tbl_m_item.id = tbl_m_item_ref_input.id_item');

        if ($id !== null) {
            return $builder->where('tbl_m_item_ref_input.id', $id)
                         ->get()
                         ->getRow();
        }

        return $builder->get()->getResult();
    }
}