<?php
/**
 * Created by:
 * Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * 2025-01-13
 * 
 * Penjamin Controller
 * 
 * Controller for managing insurance/guarantor (penjamin) data
 * Handles CRUD operations and other related functionalities
 */

namespace App\Controllers\Master;

use App\Controllers\BaseController;
use App\Models\PenjaminModel;
use App\Models\PengaturanModel;

class Penjamin extends BaseController
{
    protected $penjaminModel;
    protected $validation;
    protected $pengaturan;

    public function __construct()
    {
        $this->penjaminModel = new PenjaminModel();
        $this->pengaturan = new PengaturanModel();
        $this->validation = \Config\Services::validation();
    }

    public function index()
    {
        $currentPage = $this->request->getVar('page_penjamin') ?? 1;
        $perPage = 10;

        // Start with the model query
        $query = $this->penjaminModel;

        // Filter by name/code
        $search = $this->request->getVar('search');
        if ($search) {
            $query->groupStart()
                ->like('penjamin', $search)
                ->orLike('kode', $search)
                ->groupEnd();
        }

        // Filter by status
        $selectedStatus = $this->request->getVar('status');
        if ($selectedStatus !== null && $selectedStatus !== '') {
            $query->where('status', $selectedStatus);
        }

        $data = [
            'title'          => 'Data Penjamin',
            'Pengaturan'     => $this->pengaturan,
            'user'           => $this->ionAuth->user()->row(),
            'penjamin'       => $query->paginate($perPage, 'penjamin'),
            'pager'          => $this->penjaminModel->pager,
            'currentPage'    => $currentPage,
            'perPage'        => $perPage,
            'search'         => $search,
            'selectedStatus' => $selectedStatus,
            'breadcrumbs'    => '
                <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>
                <li class="breadcrumb-item">Master</li>
                <li class="breadcrumb-item active">Penjamin</li>
            '
        ];

        return view($this->theme->getThemePath() . '/master/penjamin/index', $data);
    }

    public function create()
    {
        $data = [
            'title'         => 'Tambah Penjamin',
            'Pengaturan'    => $this->pengaturan,
            'user'          => $this->ionAuth->user()->row(),
            'validation'    => $this->validation,
            'breadcrumbs'   => '
                <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>
                <li class="breadcrumb-item">Master</li>
                <li class="breadcrumb-item"><a href="' . base_url('master/penjamin') . '">Penjamin</a></li>
                <li class="breadcrumb-item active">Tambah</li>
            '
        ];

        return view($this->theme->getThemePath() . '/master/penjamin/create', $data);
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
            'penjamin' => [
                'rules' => 'required|max_length[160]',
                'errors' => [
                    'required' => 'Nama penjamin harus diisi',
                    'max_length' => 'Nama penjamin maksimal 160 karakter'
                ]
            ],
            'persen' => [
                'rules' => 'permit_empty|numeric|greater_than_equal_to[0]|less_than_equal_to[100]',
                'errors' => [
                    'numeric' => 'Persentase harus berupa angka',
                    'greater_than_equal_to' => 'Persentase tidak boleh kurang dari 0',
                    'less_than_equal_to' => 'Persentase tidak boleh lebih dari 100'
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
                'kode'      => $this->penjaminModel->generateKode(),
                'penjamin'  => $this->request->getPost('penjamin'),
                'persen'    => floatval($this->request->getPost('persen')) ?? 0,
                'status'    => $this->request->getPost('status')
            ];

            if (!$this->penjaminModel->insert($data)) {
                throw new \Exception('Gagal menyimpan data penjamin');
            }

            return redirect()->to(base_url('master/penjamin'))
                ->with('success', 'Data penjamin berhasil ditambahkan');

        } catch (\Exception $e) {
            log_message('error', '[Penjamin::store] ' . $e->getMessage());
            
            return redirect()->back()
                ->withInput()
                ->with('error', ENVIRONMENT === 'development' ? $e->getMessage() : 'Gagal menyimpan data penjamin');
        }
    }

    public function edit($id = null)
    {
        if (!$id) {
            return redirect()->back()
                ->with('error', 'ID Penjamin tidak ditemukan');
        }

        $penjamin = $this->penjaminModel->find($id);
        if (!$penjamin) {
            return redirect()->back()
                ->with('error', 'Data Penjamin tidak ditemukan');
        }

        $data = [
            'title'         => 'Edit Penjamin',
            'Pengaturan'    => $this->pengaturan,
            'user'          => $this->ionAuth->user()->row(),
            'validation'    => $this->validation,
            'penjamin'      => $penjamin,
            'breadcrumbs'   => '
                <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>
                <li class="breadcrumb-item">Master</li>
                <li class="breadcrumb-item"><a href="' . base_url('master/penjamin') . '">Penjamin</a></li>
                <li class="breadcrumb-item active">Edit</li>
            '
        ];

        return view($this->theme->getThemePath() . '/master/penjamin/edit', $data);
    }

    public function update($id = null)
    {
        if (!$id) {
            return redirect()->back()
                ->with('error', 'ID Penjamin tidak ditemukan');
        }

        // Validation rules
        $rules = [
            env('security.tokenName') => [
                'rules' => 'required',
                'errors' => [
                    'required' => env('csrf.name') . ' harus diisi'
                ]
            ],
            'penjamin' => [
                'rules' => 'required|max_length[160]',
                'errors' => [
                    'required' => 'Nama penjamin harus diisi',
                    'max_length' => 'Nama penjamin maksimal 160 karakter'
                ]
            ],
            'persen' => [
                'rules' => 'permit_empty|numeric|greater_than_equal_to[0]|less_than_equal_to[100]',
                'errors' => [
                    'numeric' => 'Persentase harus berupa angka',
                    'greater_than_equal_to' => 'Persentase tidak boleh kurang dari 0',
                    'less_than_equal_to' => 'Persentase tidak boleh lebih dari 100'
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
                'penjamin'  => $this->request->getPost('penjamin'),
                'persen'    => floatval($this->request->getPost('persen')) ?? 0,
                'status'    => $this->request->getPost('status')
            ];

            if (!$this->penjaminModel->update($id, $data)) {
                throw new \Exception('Gagal mengupdate data penjamin');
            }

            return redirect()->to(base_url('master/penjamin'))
                ->with('success', 'Data penjamin berhasil diupdate');

        } catch (\Exception $e) {
            log_message('error', '[Penjamin::update] ' . $e->getMessage());
            
            return redirect()->back()
                ->withInput()
                ->with('error', ENVIRONMENT === 'development' ? $e->getMessage() : 'Gagal mengupdate data penjamin');
        }
    }

    public function delete($id = null)
    {
        if (!$id) {
            return redirect()->back()
                ->with('error', 'ID Penjamin tidak ditemukan');
        }

        try {
            $penjamin = $this->penjaminModel->find($id);
            if (!$penjamin) {
                throw new \Exception('Data Penjamin tidak ditemukan');
            }

            if (!$this->penjaminModel->delete($id)) {
                throw new \Exception('Gagal menghapus data penjamin');
            }

            return redirect()->back()
                ->with('success', 'Data penjamin berhasil dihapus');

        } catch (\Exception $e) {
            log_message('error', '[Penjamin::delete] ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', ENVIRONMENT === 'development' ? $e->getMessage() : 'Gagal menghapus data penjamin');
        }
    }
} 