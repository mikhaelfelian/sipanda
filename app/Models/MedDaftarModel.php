<?php
/**
 * Created by:
 * Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * 2025-02-07
 * 
 * MedDaftar Model
 * 
 * This model handles database operations for patient registration
 */

namespace App\Models;

use CodeIgniter\Model;

class MedDaftarModel extends Model
{
    protected $table            = 'tbl_pendaftaran';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_gelar',
        'id_pasien',
        'id_poli',
        'id_platform',
        'id_dokter',
        'id_pekerjaan',
        'id_ant',
        'id_instansi',
        'tgl_masuk',
        'tgl_keluar',
        'kode',
        'no_urut',
        'no_antrian',
        'nik',
        'nama',
        'nama_pgl',
        'tmp_lahir',
        'tgl_lahir',
        'jns_klm',
        'kontak',
        'kontak_rmh',
        'alamat',
        'alamat_dom',
        'rt',
        'rw',
        'kelurahan',
        'kecamatan',
        'kota',
        'instansi',
        'instansi_alamat',
        'alergi',
        'file_base64',
        'file_base64_id',
        'tipe_bayar',
        'tipe_rawat',
        'tipe',
        'status',
        'status_akt',
        'status_hdr',
        'status_gc',
        'status_dft',
        'status_hps'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    /**
     * Get queue data with related info
     */
    public function getQueueData()
    {
        return $this->select('
                tbl_pendaftaran.*,
                tbl_m_poli.poli,
                tbl_m_poli.kode as kode_poli,
                tbl_m_gelar.gelar
            ')
            ->join('tbl_m_poli', 'tbl_m_poli.id = tbl_pendaftaran.id_poli', 'left')
            ->join('tbl_m_gelar', 'tbl_m_gelar.id = tbl_pendaftaran.id_gelar', 'left')
            ->where('tbl_pendaftaran.status_hps', '0');
    }

    /**
     * Generate registration code
     * Format: DFT{YYYYMMDD}{0000}
     * 
     * @return string
     */
    public function generateKode()
    {
        $prefix = 'DFT' . date('ymd');
        
        $lastKode = $this->select('kode')
            ->where('DATE(created_at)', date('Y-m-d'))
            ->orderBy('id', 'DESC')
            ->first();

        if ($lastKode) {
            $lastNumber = (int) substr($lastKode->kode, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }
} 