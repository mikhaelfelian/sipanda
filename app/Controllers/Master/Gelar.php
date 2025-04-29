<?php
/**
 * Created by:
 * Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * 2024-01-13
 * 
 * Gelar Controller
 * 
 * Controller for managing academic titles/degrees (gelar) data
 * Handles CRUD operations and other related functionalities
 */

namespace App\Controllers\Master;

use App\Controllers\BaseController;
use App\Models\GelarModel;
use App\Models\PengaturanModel;

class Gelar extends BaseController
{
    protected $gelarModel;
    protected $validation;
    protected $pengaturan;

    public function __construct()
    {
        $this->gelarModel = new GelarModel();
        $this->pengaturan = new PengaturanModel();
        $this->validation = \Config\Services::validation();
    }

    public function index()
    {
        $currentPage = $this->request->getVar('page_gelar') ?? 1;
        $perPage = 10;

        // Start with the model query
        $query = $this->gelarModel;

        // Filter by gelar/keterangan
        $search = $this->request->getVar('search');
        if ($search) {
            $query->groupStart()
                ->like('gelar', $search)
                ->orLike('keterangan', $search)
                ->groupEnd();
        }

        $data = [
            'title'          => 'Data Gelar',
            'Pengaturan'     => $this->pengaturan,
            'user'           => $this->ionAuth->user()->row(),
            'gelar'          => $query->paginate($perPage, 'gelar'),
            'pager'          => $this->gelarModel->pager,
            'currentPage'    => $currentPage,
            'perPage'        => $perPage,
            'search'         => $search,
            'breadcrumbs'    => '
                <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>
                <li class="breadcrumb-item">Master</li>
                <li class="breadcrumb-item active">Gelar</li>
            '
        ];

        return view($this->theme->getThemePath() . '/master/gelar/index', $data);
    }

    public function create()
    {
        $data = [
            'title'         => 'Tambah Gelar',
            'Pengaturan'    => $this->pengaturan,
            'user'          => $this->ionAuth->user()->row(),
            'validation'    => $this->validation,
            'breadcrumbs'   => '
                <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>
                <li class="breadcrumb-item">Master</li>
                <li class="breadcrumb-item"><a href="' . base_url('master/gelar') . '">Gelar</a></li>
                <li class="breadcrumb-item active">Tambah</li>
            '
        ];

        return view($this->theme->getThemePath() . '/master/gelar/create', $data);
    }

    public function store()
    {
        // Validation rules
        $rules = [
            env('security.tokenName') => [
                'rules' => 'required',
                'errors' => [
                    'required' => env('csrf.name') . ' harus diisi'
                ]
            ],
            'gelar' => [
                'rules' => 'required|max_length[50]',
                'errors' => [
                    'required' => 'Gelar harus diisi',
                    'max_length' => 'Gelar maksimal 50 karakter'
                ]
            ],
            'keterangan' => [
                'rules' => 'required|max_length[50]',
                'errors' => [
                    'required' => 'Keterangan harus diisi',
                    'max_length' => 'Keterangan maksimal 50 karakter'
                ]
            ]
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('validation_errors', $this->validator->getErrors())
                ->with('error', 'Validasi gagal. Silakan periksa kembali input Anda.');
        }

        try {
            $data = [
                'gelar'      => $this->request->getPost('gelar'),
                'keterangan' => $this->request->getPost('keterangan')
            ];

            if (!$this->gelarModel->insert($data)) {
                throw new \Exception('Gagal menyimpan data gelar');
            }

            return redirect()->to(base_url('master/gelar'))
                ->with('success', 'Data gelar berhasil ditambahkan');

        } catch (\Exception $e) {
            log_message('error', '[Gelar::store] ' . $e->getMessage());
            
            return redirect()->back()
                ->withInput()
                ->with('error', ENVIRONMENT === 'development' ? $e->getMessage() : 'Gagal menyimpan data gelar');
        }
    }

    public function edit($id = null)
    {
        if (!$id) {
            return redirect()->back()
                ->with('error', 'ID Gelar tidak ditemukan');
        }

        $gelar = $this->gelarModel->find($id);
        if (!$gelar) {
            return redirect()->back()
                ->with('error', 'Data Gelar tidak ditemukan');
        }

        $data = [
            'title'         => 'Edit Gelar',
            'Pengaturan'    => $this->pengaturan,
            'user'          => $this->ionAuth->user()->row(),
            'validation'    => $this->validation,
            'gelar'         => $gelar,
            'breadcrumbs'   => '
                <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>
                <li class="breadcrumb-item">Master</li>
                <li class="breadcrumb-item"><a href="' . base_url('master/gelar') . '">Gelar</a></li>
                <li class="breadcrumb-item active">Edit</li>
            '
        ];

        return view($this->theme->getThemePath() . '/master/gelar/edit', $data);
    }

    public function update($id = null)
    {
        if (!$id) {
            return redirect()->back()
                ->with('error', 'ID Gelar tidak ditemukan');
        }

        // Validation rules
        $rules = [
            env('security.tokenName') => [
                'rules' => 'required',
                'errors' => [
                    'required' => env('csrf.name') . ' harus diisi'
                ]
            ],
            'gelar' => [
                'rules' => 'required|max_length[50]',
                'errors' => [
                    'required' => 'Gelar harus diisi',
                    'max_length' => 'Gelar maksimal 50 karakter'
                ]
            ],
            'keterangan' => [
                'rules' => 'required|max_length[50]',
                'errors' => [
                    'required' => 'Keterangan harus diisi',
                    'max_length' => 'Keterangan maksimal 50 karakter'
                ]
            ]
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('validation_errors', $this->validator->getErrors())
                ->with('error', 'Validasi gagal. Silakan periksa kembali input Anda.');
        }

        try {
            $data = [
                'gelar'      => $this->request->getPost('gelar'),
                'keterangan' => $this->request->getPost('keterangan')
            ];

            if (!$this->gelarModel->update($id, $data)) {
                throw new \Exception('Gagal mengupdate data gelar');
            }

            return redirect()->to(base_url('master/gelar'))
                ->with('success', 'Data gelar berhasil diupdate');

        } catch (\Exception $e) {
            log_message('error', '[Gelar::update] ' . $e->getMessage());
            
            return redirect()->back()
                ->withInput()
                ->with('error', ENVIRONMENT === 'development' ? $e->getMessage() : 'Gagal mengupdate data gelar');
        }
    }

    public function delete($id = null)
    {
        if (!$id) {
            return redirect()->back()
                ->with('error', 'ID Gelar tidak ditemukan');
        }

        try {
            $gelar = $this->gelarModel->find($id);
            if (!$gelar) {
                throw new \Exception('Data Gelar tidak ditemukan');
            }

            if (!$this->gelarModel->delete($id)) {
                throw new \Exception('Gagal menghapus data gelar');
            }

            return redirect()->back()
                ->with('success', 'Data gelar berhasil dihapus');

        } catch (\Exception $e) {
            log_message('error', '[Gelar::delete] ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', ENVIRONMENT === 'development' ? $e->getMessage() : 'Gagal menghapus data gelar');
        }
    }
} 