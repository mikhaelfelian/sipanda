<?php
/**
 * Created by:
 * Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * 2025-02-08
 * 
 * MedTransDet Model
 * 
 * This model handles database operations for medical record transaction details
 */

namespace App\Models;

use CodeIgniter\Model;

class MedTransDetModel extends Model
{
    protected $table            = 'tbl_trans_medrecs_det';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_medrecs', 'id_resep', 'id_resep_det', 'id_resep_det_rc',
        'id_lab', 'id_lab_kat', 'id_rad', 'id_mcu', 'id_item',
        'id_item_kat', 'id_item_sat', 'id_user', 'id_dokter',
        'id_perawat', 'id_analis', 'id_radiografer',
        'tgl_simpan', 'tgl_modif', 'tgl_masuk', 'tgl_baca',
        'kode', 'item', 'keterangan', 'jml', 'jml_resep',
        'jml_satuan', 'satuan', 'file_rad', 'resep', 'kesan_rad',
        'hasil_rad', 'hasil_lab', 'dosis', 'dosis_ket',
        'harga', 'disk1', 'disk2', 'disk3', 'diskon',
        'potongan', 'potongan_poin', 'subtotal',
        'status', 'status_ctk', 'status_hsl', 'status_hsl_lab',
        'status_hsl_rad', 'status_baca', 'status_post', 'status_remun',
        'status_pj', 'status_rc', 'status_rf', 'status_pkt', 'sp'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'tgl_simpan';
    protected $updatedField  = 'tgl_modif';
} 