<?php
/**
 * Dibuat oleh:
 * Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * 2025-02-08
 * 
 * Model MedTrans
 * 
 * Model ini menangani operasi basis data untuk transaksi rekam medis
 */

namespace App\Models;

use CodeIgniter\Model;

class MedTransModel extends Model
{
    protected $table            = 'tbl_trans_medrecs'; // Nama tabel
    protected $primaryKey       = 'id'; // Kunci utama
    protected $useAutoIncrement = true; // Menggunakan auto increment
    protected $returnType       = 'object'; // Tipe data yang dikembalikan
    protected $useSoftDeletes   = false; // Tidak menggunakan soft delete
    protected $protectFields    = true; // Melindungi field
    protected $allowedFields    = [ // Field yang diizinkan
        'id_user', 'id_dokter', 'id_nurse', 'id_analis', 'id_farmasi', 'id_pasien', 'id_instansi', 'id_poli', 'id_dft', 'id_ant', 'id_kasir', 'id_icd', 'id_encounter', 'id_condition', 'id_post_location', 'tgl_masuk', 'tgl_keluar', 'no_rm', 'no_akun', 'no_nota', 'dokter', 'dokter_nik', 'poli', 'pasien', 'pasien_alamat', 'pasien_nik', 'keluhan', 'ttv', 'ttv_st', 'ttv_bb', 'ttv_tb', 'ttv_td', 'ttv_sistole', 'ttv_diastole', 'ttv_nadi', 'ttv_laju', 'ttv_saturasi', 'ttv_skala', 'ttd_obat', 'diagnosa', 'anamnesa', 'pemeriksaan', 'program', 'alergi', 'metode', 'platform', 'jml_total', 'jml_ongkir', 'jml_dp', 'jml_diskon', 'diskon', 'jml_potongan', 'jml_potongan_poin', 'jml_subtotal', 'jml_ppn', 'ppn', 'jml_gtotal', 'jml_bayar', 'jml_kembali', 'jml_kurang', 'jml_poin', 'jml_poin_nom', 'tipe', 'tipe_bayar', 'status', 'status_bayar', 'status_nota', 'status_hps', 'status_pos', 'status_periksa', 'status_resep', 'sp'
    ];

    // Tanggal
    protected $useTimestamps = true; // Menggunakan timestamp
    protected $dateFormat    = 'datetime'; // Format tanggal
    protected $createdField  = 'created_at'; // Field untuk tanggal pembuatan
    protected $updatedField  = 'updated_at'; // Field untuk tanggal pembaruan

    /**
     * Menghasilkan kode unik untuk transaksi rekam medis
     * 
     * @return string Kode yang dihasilkan
     */
    public function generateKode()
    {
        $date       = date('ymd'); // Mendapatkan tanggal saat ini dalam format ymd
        $lastRecord = $this->orderBy('id', 'DESC')->first(); // Mendapatkan record terakhir
        $lastId     = $lastRecord ? $lastRecord->id : 0; // Mendapatkan ID terakhir
        $newId      = $lastId + 1; // Menambah ID baru
        $nomer      = $date . str_pad($newId, 4, '0', STR_PAD_LEFT); // Menghasilkan kode baru
        return $nomer;
    }

    /**
     * Get medical record data by ID with joins
     * 
     * @param int $id Medical record ID
     * @return object|null Medical record data
     */
    public function getTransById($id)
    {
        $builder = $this->select('
                tbl_trans_medrecs.*,
                tbl_m_poli.poli,
                tbl_pendaftaran.no_urut,
                tbl_m_pasien.kode as no_pasien,
                tbl_m_pasien.nama_pgl as nama_pasien,
                tbl_m_pasien.jns_klm,
                tbl_m_pasien.nik,
                tbl_m_pasien.tgl_lahir,
                tbl_m_pasien.file_foto,
                tbl_m_pasien.id as id_pasien,
                tbl_trans_medrecs.created_at,
                tbl_trans_medrecs.tgl_masuk,
                tbl_trans_medrecs.tgl_keluar
            ')
            ->join('tbl_m_poli', 'tbl_m_poli.id = tbl_trans_medrecs.id_poli', 'left')
            ->join('tbl_pendaftaran', 'tbl_pendaftaran.id = tbl_trans_medrecs.id_dft', 'left')
            ->join('tbl_m_pasien', 'tbl_m_pasien.id = tbl_trans_medrecs.id_pasien', 'left')
            ->get()->getRow();

        return $builder;
    }
} 