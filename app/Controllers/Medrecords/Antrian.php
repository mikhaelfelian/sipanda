<?php
/**
 * Created by:
 * Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * 2025-02-07
 * 
 * Antrian Controller
 * Handles patient queue management operations
 */

namespace App\Controllers\Medrecords;

use App\Controllers\BaseController;
use App\Models\MedDaftarModel;
use App\Models\PoliModel;
use App\Models\DokterModel;
use App\Models\PlatformModel;
use App\Models\GeneralConsentModel;

class Antrian extends BaseController
{
    protected $medDaftarModel;
    protected $poliModel;
    protected $dokterModel;
    protected $platformModel;
    protected $gcModel;

    public function __construct()
    {
        $this->medDaftarModel   = new MedDaftarModel();
        $this->poliModel        = new PoliModel();
        $this->platformModel    = new PlatformModel();
        
        helper(['tanggalan', 'general', 'akses']);
    }

    public function index()
    {
        $currentPage = $this->request->getVar('page') ?? 1;
        $perPage = $this->pengaturan->pagination_limit ?? 10;

        // Get queue data with all related info
        $query = $this->medDaftarModel->findAll();


        $data = [
            'title'         => 'Antrian Pasien',
            'Pengaturan'    => $this->pengaturan,
            'user'          => $this->ionAuth->user()->row(),
            'antrians'      => $query,
            'pager'         => $this->medDaftarModel->pager,
            'currentPage'   => $currentPage,
            'perPage'       => $perPage,
            'breadcrumbs'   => '
                <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>
                <li class="breadcrumb-item">Medical Records</li>
                <li class="breadcrumb-item active">Antrian</li>
            '
        ];

        return view($this->theme->getThemePath() . '/medrecords/med_antrian', $data);
    }

    public function ubah($id)
    {
        return redirect()->to('medrecords/daftar?tipe_pas=3&dft=' . general::enkrip($id));
    }
} 