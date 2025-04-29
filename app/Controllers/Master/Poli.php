<?php
/**
 * Created by:
 * Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * 2025-01-13
 * 
 * Poli Controller
 * 
 * Controller for managing clinic/department (poli) data
 * Handles CRUD operations and other related functionalities
 */

namespace App\Controllers\Master;

use App\Controllers\BaseController;
use App\Models\PoliModel;
use App\Models\PengaturanModel;

class Poli extends BaseController
{
    protected $poliModel;
    protected $validation;
    protected $pengaturan;

    public function __construct()
    {
        $this->poliModel = new PoliModel();
        $this->pengaturan = new PengaturanModel();
        $this->validation = \Config\Services::validation();
    }

    public function index()
    {
        $currentPage = $this->request->getVar('page_poli') ?? 1;
        $perPage = 10;

        // Start with the model query
        $query = $this->poliModel;

        // Filter by name/code
        $search = $this->request->getVar('search');
        if ($search) {
            $query->groupStart()
                ->like('poli', $search)
                ->orLike('kode', $search)
                ->groupEnd();
        }

        // Filter by status
        $selectedStatus = $this->request->getVar('status');
        if ($selectedStatus !== null && $selectedStatus !== '') {
            $query->where('status', $selectedStatus);
        }

        $data = [
            'title'          => 'Data Poli',
            'Pengaturan'     => $this->pengaturan,
            'user'           => $this->ionAuth->user()->row(),
            'poli'           => $query->paginate($perPage, 'poli'),
            'pager'          => $this->poliModel->pager,
            'currentPage'    => $currentPage,
            'perPage'        => $perPage,
            'search'         => $search,
            'selectedStatus' => $selectedStatus,
            'breadcrumbs'    => '
                <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>
                <li class="breadcrumb-item">Master</li>
                <li class="breadcrumb-item active">Poli</li>
            '
        ];

        return view($this->theme->getThemePath() . '/master/poli/index', $data);
    }

    public function create()
    {
        $data = [
            'title'         => 'Tambah Poli',
            'Pengaturan'    => $this->pengaturan,
            'user'          => $this->ionAuth->user()->row(),
            'validation'    => $this->validation,
            'breadcrumbs'   => '
                <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>
                <li class="breadcrumb-item">Master</li>
                <li class="breadcrumb-item"><a href="' . base_url('master/poli') . '">Poli</a></li>
                <li class="breadcrumb-item active">Tambah</li>
            '
        ];

        return view($this->theme->getThemePath() . '/master/poli/create', $data);
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
            'poli' => [
                'rules' => 'required|max_length[64]',
                'errors' => [
                    'required' => 'Nama poli harus diisi',
                    'max_length' => 'Nama poli maksimal 64 karakter'
                ]
            ],
            'keterangan' => [
                'rules' => 'permit_empty',
            ],
            'post_location' => [
                'rules' => 'permit_empty|max_length[100]',
                'errors' => [
                    'max_length' => 'Post location maksimal 100 karakter'
                ]
            ],
            'status' => [
                'rules' => 'required|in_list[0,1]',
                'errors' => [
                    'required' => 'Status harus dipilih',
                    'in_list' => 'Status tidak valid'
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
                'kode'          => $this->poliModel->generateKode(),
                'poli'          => $this->request->getPost('poli'),
                'keterangan'    => $this->request->getPost('keterangan'),
                'post_location' => $this->request->getPost('post_location'),
                'status'        => $this->request->getPost('status')
            ];

            if (!$this->poliModel->insert($data)) {
                throw new \Exception('Gagal menyimpan data poli');
            }

            return redirect()->to(base_url('master/poli'))
                ->with('success', 'Data poli berhasil ditambahkan');

        } catch (\Exception $e) {
            log_message('error', '[Poli::store] ' . $e->getMessage());
            
            return redirect()->back()
                ->withInput()
                ->with('error', ENVIRONMENT === 'development' ? $e->getMessage() : 'Gagal menyimpan data poli');
        }
    }

    public function edit($id = null)
    {
        if (!$id) {
            return redirect()->back()
                ->with('error', 'ID Poli tidak ditemukan');
        }

        $poli = $this->poliModel->find($id);
        if (!$poli) {
            return redirect()->back()
                ->with('error', 'Data Poli tidak ditemukan');
        }

        $data = [
            'title'         => 'Edit Poli',
            'Pengaturan'    => $this->pengaturan,
            'user'          => $this->ionAuth->user()->row(),
            'validation'    => $this->validation,
            'poli'          => $poli,
            'breadcrumbs'   => '
                <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>
                <li class="breadcrumb-item">Master</li>
                <li class="breadcrumb-item"><a href="' . base_url('master/poli') . '">Poli</a></li>
                <li class="breadcrumb-item active">Edit</li>
            '
        ];

        return view($this->theme->getThemePath() . '/master/poli/edit', $data);
    }

    public function update($id = null)
    {
        if (!$id) {
            return redirect()->back()
                ->with('error', 'ID Poli tidak ditemukan');
        }

        // Validation rules
        $rules = [
            env('security.tokenName') => [
                'rules' => 'required',
                'errors' => [
                    'required' => env('csrf.name') . ' harus diisi'
                ]
            ],
            'poli' => [
                'rules' => 'required|max_length[64]',
                'errors' => [
                    'required' => 'Nama poli harus diisi',
                    'max_length' => 'Nama poli maksimal 64 karakter'
                ]
            ],
            'keterangan' => [
                'rules' => 'permit_empty',
            ],
            'post_location' => [
                'rules' => 'permit_empty|max_length[100]',
                'errors' => [
                    'max_length' => 'Post location maksimal 100 karakter'
                ]
            ],
            'status' => [
                'rules' => 'required|in_list[0,1]',
                'errors' => [
                    'required' => 'Status harus dipilih',
                    'in_list' => 'Status tidak valid'
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
                'poli'          => $this->request->getPost('poli'),
                'keterangan'    => $this->request->getPost('keterangan'),
                'post_location' => $this->request->getPost('post_location'),
                'status'        => $this->request->getPost('status')
            ];

            if (!$this->poliModel->update($id, $data)) {
                throw new \Exception('Gagal mengupdate data poli');
            }

            return redirect()->to(base_url('master/poli'))
                ->with('success', 'Data poli berhasil diupdate');

        } catch (\Exception $e) {
            log_message('error', '[Poli::update] ' . $e->getMessage());
            
            return redirect()->back()
                ->withInput()
                ->with('error', ENVIRONMENT === 'development' ? $e->getMessage() : 'Gagal mengupdate data poli');
        }
    }

    public function delete($id = null)
    {
        if (!$id) {
            return redirect()->back()
                ->with('error', 'ID Poli tidak ditemukan');
        }

        try {
            $poli = $this->poliModel->find($id);
            if (!$poli) {
                throw new \Exception('Data Poli tidak ditemukan');
            }

            if (!$this->poliModel->delete($id)) {
                throw new \Exception('Gagal menghapus data poli');
            }

            return redirect()->back()
                ->with('success', 'Data poli berhasil dihapus');

        } catch (\Exception $e) {
            log_message('error', '[Poli::delete] ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', ENVIRONMENT === 'development' ? $e->getMessage() : 'Gagal menghapus data poli');
        }
    }
} 