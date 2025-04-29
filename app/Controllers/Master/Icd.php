<?php
/**
 * Created by:
 * Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * 2025-01-18
 * 
 * ICD Controller
 * 
 * Controller for managing ICD (International Classification of Diseases) data
 */

namespace App\Controllers\Master;

use App\Controllers\BaseController;
use App\Models\IcdModel;
use App\Models\PengaturanModel;

class Icd extends BaseController
{
    protected $icdModel;
    protected $validation;
    protected $pengaturan;

    public function __construct()
    {
        $this->icdModel = new IcdModel();
        $this->pengaturan = new PengaturanModel();
        $this->validation = \Config\Services::validation();
    }

    public function index()
    {
        $currentPage = $this->request->getVar('page_icd') ?? 1;
        $perPage = $this->pengaturan->pagination_limit ?? 10;

        // Start with the model query
        $query = $this->icdModel;

        // Filter by code/diagnosa
        $search = $this->request->getVar('search');
        if ($search) {
            $query->groupStart()
                ->like('kode', $search)
                ->orLike('icd', $search)
                ->orLike('diagnosa_id', $search)
                ->orLike('diagnosa_en', $search)
                ->groupEnd();
        }

        $data = [
            'title'       => 'Data ICD',
            'icds'        => $query->paginate($perPage, 'icd'),
            'pager'       => $this->icdModel->pager,
            'currentPage' => $currentPage,
            'perPage'     => $perPage,
            'search'      => $search,
            'breadcrumbs' => '
                <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>
                <li class="breadcrumb-item">Master</li>
                <li class="breadcrumb-item active">ICD</li>
            '
        ];

        return $this->view($this->theme->getThemePath() . '/master/icd/index', $data);
    }

    /**
     * Display create form
     */
    public function create()
    {
        $data = [
            'title'       => 'Tambah ICD',
            'validation'  => $this->validation,
            'kode'        => $this->icdModel->generateKode(),
            'breadcrumbs' => '
                <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>
                <li class="breadcrumb-item"><a href="' . base_url('master/icd') . '">ICD</a></li>
                <li class="breadcrumb-item active">Tambah</li>
            '
        ];

        return $this->view($this->theme->getThemePath() . '/master/icd/create', $data);
    }

    /**
     * Store new ICD data
     */
    public function store()
    {
        // Validation rules
        $rules = [
            'kode' => [
                'rules'  => 'required|is_unique[tbl_m_icd.kode]',
                'errors' => [
                    'required'  => 'Kode ICD harus diisi',
                    'is_unique' => 'Kode ICD sudah digunakan'
                ]
            ],
            'icd' => [
                'rules'  => 'required',
                'errors' => [
                    'required' => 'ICD harus diisi'
                ]
            ],
            'diagnosa_en' => [
                'rules'  => 'required',
                'errors' => [
                    'required' => 'Diagnosa (EN) harus diisi'
                ]
            ],
            'diagnosa_id' => [
                'rules'  => 'required',
                'errors' => [
                    'required' => 'Diagnosa (ID) harus diisi'
                ]
            ]
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                           ->withInput()
                           ->with('validation', $this->validation);
        }

        try {
            $data = [
                'kode'        => $this->request->getPost('kode'),
                'icd'         => $this->request->getPost('icd'),
                'diagnosa_en' => $this->request->getPost('diagnosa_en'),
                'diagnosa_id' => $this->request->getPost('diagnosa_id')
            ];

            if (!$this->icdModel->insert($data)) {
                throw new \RuntimeException('Gagal menyimpan data ICD');
            }

            return redirect()->to(base_url('master/icd'))
                           ->with('success', 'Data ICD berhasil ditambahkan');

        } catch (\Exception $e) {
            log_message('error', '[Icd::store] ' . $e->getMessage());
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Gagal menyimpan data ICD');
        }
    }

    /**
     * Display edit form
     */
    public function edit($id = null)
    {
        if (!$id) {
            return redirect()->to('master/icd')
                           ->with('error', 'ID ICD tidak ditemukan');
        }

        $icd = $this->icdModel->find($id);
        if (!$icd) {
            return redirect()->to('master/icd')
                           ->with('error', 'Data ICD tidak ditemukan');
        }

        $data = [
            'title'       => 'Edit ICD',
            'validation'  => $this->validation,
            'icd'         => $icd,
            'breadcrumbs' => '
                <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>
                <li class="breadcrumb-item"><a href="' . base_url('master/icd') . '">ICD</a></li>
                <li class="breadcrumb-item active">Edit</li>
            '
        ];

        return $this->view($this->theme->getThemePath() . '/master/icd/edit', $data);
    }

    /**
     * Update ICD data
     */
    public function update($id = null)
    {
        if (!$id) {
            return redirect()->to('master/icd')
                           ->with('error', 'ID ICD tidak ditemukan');
        }

        // Validation rules
        $rules = [
            'kode' => [
                'rules'  => "required|is_unique[tbl_m_icd.kode,id,$id]",
                'errors' => [
                    'required'  => 'Kode ICD harus diisi',
                    'is_unique' => 'Kode ICD sudah digunakan'
                ]
            ],
            'icd' => [
                'rules'  => 'required',
                'errors' => [
                    'required' => 'ICD harus diisi'
                ]
            ],
            'diagnosa_en' => [
                'rules'  => 'required',
                'errors' => [
                    'required' => 'Diagnosa (EN) harus diisi'
                ]
            ],
            'diagnosa_id' => [
                'rules'  => 'required',
                'errors' => [
                    'required' => 'Diagnosa (ID) harus diisi'
                ]
            ]
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                           ->withInput()
                           ->with('validation', $this->validation);
        }

        try {
            $data = [
                'kode'        => $this->request->getPost('kode'),
                'icd'         => $this->request->getPost('icd'),
                'diagnosa_en' => $this->request->getPost('diagnosa_en'),
                'diagnosa_id' => $this->request->getPost('diagnosa_id')
            ];

            if (!$this->icdModel->update($id, $data)) {
                throw new \RuntimeException('Gagal mengupdate data ICD');
            }

            return redirect()->to(base_url('master/icd'))
                           ->with('success', 'Data ICD berhasil diupdate');

        } catch (\Exception $e) {
            log_message('error', '[Icd::update] ' . $e->getMessage());
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Gagal mengupdate data ICD');
        }
    }

    /**
     * Display ICD details
     */
    public function detail($id = null)
    {
        if (!$id) {
            return redirect()->to('master/icd')
                           ->with('error', 'ID ICD tidak ditemukan');
        }

        $icd = $this->icdModel->find($id);
        if (!$icd) {
            return redirect()->to('master/icd')
                           ->with('error', 'Data ICD tidak ditemukan');
        }

        $data = [
            'title'       => 'Detail ICD',
            'icd'         => $icd,
            'breadcrumbs' => '
                <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>
                <li class="breadcrumb-item"><a href="' . base_url('master/icd') . '">ICD</a></li>
                <li class="breadcrumb-item active">Detail</li>
            '
        ];

        return $this->view($this->theme->getThemePath() . '/master/icd/detail', $data);
    }

    /**
     * Delete ICD data permanently
     */
    public function delete($id = null)
    {
        if (!$id) {
            return redirect()->to('master/icd')
                           ->with('error', 'ID ICD tidak ditemukan');
        }

        try {
            $icd = $this->icdModel->find($id);
            if (!$icd) {
                throw new \RuntimeException('Data ICD tidak ditemukan');
            }

            if (!$this->icdModel->delete($id)) {
                throw new \RuntimeException('Gagal menghapus data ICD');
            }

            return redirect()->to(base_url('master/icd'))
                           ->with('success', 'Data ICD berhasil dihapus');

        } catch (\Exception $e) {
            log_message('error', '[Icd::delete] ' . $e->getMessage());
            return redirect()->back()
                           ->with('error', 'Gagal menghapus data ICD');
        }
    }
} 