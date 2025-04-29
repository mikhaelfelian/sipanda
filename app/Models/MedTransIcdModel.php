<?php
/**
 * Created by:
 * Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * 2025-02-21
 * 
 * MedTransIcdModel
 * 
 * This model handles database operations for the tbl_trans_medrecs_icd table
 */

namespace App\Models;

use CodeIgniter\Model;

class MedTransIcdModel extends Model
{
    protected $table            = 'tbl_trans_medrecs_icd';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_medrecs', 'id_user', 'id_dokter', 'id_icd', 'kode', 'icd', 'status_icd', 'created_at', 'updated_at'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
} 