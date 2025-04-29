<?php
/**
 * Created by:
 * Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * 2025-02-21
 * 
 * MedTransFile Model
 * Handles medical record file operations
 */

namespace App\Models;

use CodeIgniter\Model;

class MedTransFileModel extends Model
{
    protected $table = 'tbl_trans_medrecs_file';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'object';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'id_medrecs', 'id_berkas', 'id_pasien', 'id_rad', 'id_user',
        'created_at', 'updated_at', 'judul', 'keterangan',
        'file_name_ori', 'file_name', 'file_ext', 'file_type',
        'status', 'sp'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
} 