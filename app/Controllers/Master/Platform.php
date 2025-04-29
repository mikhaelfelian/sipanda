<?php
/**
 * Created by:
 * Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * 2025-01-18
 * 
 * Platform Controller
 * 
 * Controller for managing Platform (Payment Platform) data
 */

namespace App\Controllers\Master;

use App\Controllers\BaseController;
use App\Models\PlatformModel;
use App\Models\PengaturanModel;

class Platform extends BaseController
{
    protected $platformModel;
    protected $validation;
    protected $pengaturan;

    public function __construct()
    {
        $this->platformModel = new PlatformModel();
        $this->pengaturan = new PengaturanModel();
        $this->validation = \Config\Services::validation();
    }

    public function index()
    {
        $currentPage = $this->request->getVar('page_platform') ?? 1;
        $perPage = $this->pengaturan->pagination_limit ?? 10;

        // Start with the model query
        $query = $this->platformModel;

        // Filter by code/name
        $search = $this->request->getVar('search');
        if ($search) {
            $query->groupStart()
                ->like('kode', $search)
                ->orLike('platform', $search)
                ->orLike('keterangan', $search)
                ->groupEnd();
        }

        // Filter by status
        $status = $this->request->getVar('status');
        if ($status !== null && $status !== '') {
            $query->where('status', $status);
        }

        $data = [
            'title'          => 'Data Platform',
            'platforms'      => $query->paginate($perPage, 'platform'),
            'pager'          => $this->platformModel->pager,
            'currentPage'    => $currentPage,
            'perPage'        => $perPage,
            'search'         => $search,
            'status'         => $status,
            'getStatusLabel' => function($status) {
                return $this->platformModel->getStatusLabel($status);
            },
            'breadcrumbs'    => '
                <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>
                <li class="breadcrumb-item">Master</li>
                <li class="breadcrumb-item active">Platform</li>
            '
        ];

        return $this->view($this->theme->getThemePath() . '/master/platform/index', $data);
    }

    /**
     * Display create form
     */
    public function create()
    {
        $data = [
            'title'       => 'Tambah Platform',
            'validation'  => $this->validation,
            'kode'        => $this->platformModel->generateKode(),
            'breadcrumbs' => '
                <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>
                <li class="breadcrumb-item"><a href="' . base_url('master/platform') . '">Platform</a></li>
                <li class="breadcrumb-item active">Tambah</li>
            '
        ];

        return $this->view($this->theme->getThemePath() . '/master/platform/create', $data);
    }

    /**
     * Store new platform data
     */
    public function store()
    {
        // Validation rules
        $rules = [
            'kode' => [
                'rules'  => 'required|is_unique[tbl_m_platform.kode]',
                'errors' => [
                    'required'  => 'Kode platform harus diisi',
                    'is_unique' => 'Kode platform sudah digunakan'
                ]
            ],
            'platform' => [
                'rules'  => 'required',
                'errors' => [
                    'required' => 'Nama platform harus diisi'
                ]
            ],
            'keterangan' => [
                'rules'  => 'permit_empty',
                'errors' => []
            ],
            'persen' => [
                'rules'  => 'permit_empty|numeric|greater_than_equal_to[0]|less_than_equal_to[100]',
                'errors' => [
                    'numeric' => 'Persentase harus berupa angka',
                    'greater_than_equal_to' => 'Persentase minimal 0',
                    'less_than_equal_to' => 'Persentase maksimal 100'
                ]
            ],
            'status' => [
                'rules'  => 'required|in_list[0,1]',
                'errors' => [
                    'required' => 'Status harus diisi',
                    'in_list' => 'Status tidak valid'
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
                'kode'       => $this->request->getPost('kode'),
                'platform'   => $this->request->getPost('platform'),
                'keterangan' => $this->request->getPost('keterangan'),
                'persen'     => $this->request->getPost('persen') ?: null,
                'status'     => $this->request->getPost('status')
            ];

            if (!$this->platformModel->insert($data)) {
                throw new \RuntimeException('Gagal menyimpan data platform');
            }

            return redirect()->to(base_url('master/platform'))
                           ->with('success', 'Data platform berhasil ditambahkan');

        } catch (\Exception $e) {
            log_message('error', '[Platform::store] ' . $e->getMessage());
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Gagal menyimpan data platform');
        }
    }

    /**
     * Display edit form
     */
    public function edit($id = null)
    {
        if (!$id) {
            return redirect()->to('master/platform')
                           ->with('error', 'ID Platform tidak ditemukan');
        }

        $platform = $this->platformModel->find($id);
        if (!$platform) {
            return redirect()->to('master/platform')
                           ->with('error', 'Data platform tidak ditemukan');
        }

        $data = [
            'title'       => 'Edit Platform',
            'validation'  => $this->validation,
            'platform'    => $platform,
            'breadcrumbs' => '
                <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>
                <li class="breadcrumb-item"><a href="' . base_url('master/platform') . '">Platform</a></li>
                <li class="breadcrumb-item active">Edit</li>
            '
        ];

        return $this->view($this->theme->getThemePath() . '/master/platform/edit', $data);
    }

    /**
     * Update platform data
     */
    public function update($id = null)
    {
        if (!$id) {
            return redirect()->to('master/platform')
                           ->with('error', 'ID Platform tidak ditemukan');
        }

        // Validation rules
        $rules = [
            'kode' => [
                'rules'  => "required|is_unique[tbl_m_platform.kode,id,$id]",
                'errors' => [
                    'required'  => 'Kode platform harus diisi',
                    'is_unique' => 'Kode platform sudah digunakan'
                ]
            ],
            'platform' => [
                'rules'  => 'required',
                'errors' => [
                    'required' => 'Nama platform harus diisi'
                ]
            ],
            'keterangan' => [
                'rules'  => 'permit_empty',
                'errors' => []
            ],
            'persen' => [
                'rules'  => 'permit_empty|numeric|greater_than_equal_to[0]|less_than_equal_to[100]',
                'errors' => [
                    'numeric' => 'Persentase harus berupa angka',
                    'greater_than_equal_to' => 'Persentase minimal 0',
                    'less_than_equal_to' => 'Persentase maksimal 100'
                ]
            ],
            'status' => [
                'rules'  => 'required|in_list[0,1]',
                'errors' => [
                    'required' => 'Status harus diisi',
                    'in_list' => 'Status tidak valid'
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
                'kode'       => $this->request->getPost('kode'),
                'platform'   => $this->request->getPost('platform'),
                'keterangan' => $this->request->getPost('keterangan'),
                'persen'     => $this->request->getPost('persen') ?: null,
                'status'     => $this->request->getPost('status')
            ];

            if (!$this->platformModel->update($id, $data)) {
                throw new \RuntimeException('Gagal mengupdate data platform');
            }

            return redirect()->to(base_url('master/platform'))
                           ->with('success', 'Data platform berhasil diupdate');

        } catch (\Exception $e) {
            log_message('error', '[Platform::update] ' . $e->getMessage());
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Gagal mengupdate data platform');
        }
    }

    /**
     * Display platform details
     */
    public function detail($id = null)
    {
        if (!$id) {
            return redirect()->to('master/platform')
                           ->with('error', 'ID Platform tidak ditemukan');
        }

        $platform = $this->platformModel->find($id);
        if (!$platform) {
            return redirect()->to('master/platform')
                           ->with('error', 'Data platform tidak ditemukan');
        }

        $data = [
            'title'       => 'Detail Platform',
            'platform'    => $platform,
            'breadcrumbs' => '
                <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>
                <li class="breadcrumb-item"><a href="' . base_url('master/platform') . '">Platform</a></li>
                <li class="breadcrumb-item active">Detail</li>
            '
        ];

        return $this->view($this->theme->getThemePath() . '/master/platform/detail', $data);
    }

    /**
     * Delete platform data permanently
     */
    public function delete($id = null)
    {
        if (!$id) {
            return redirect()->to('master/platform')
                           ->with('error', 'ID Platform tidak ditemukan');
        }

        try {
            $platform = $this->platformModel->find($id);
            if (!$platform) {
                throw new \RuntimeException('Data platform tidak ditemukan');
            }

            if (!$this->platformModel->delete($id)) {
                throw new \RuntimeException('Gagal menghapus data platform');
            }

            return redirect()->to(base_url('master/platform'))
                           ->with('success', 'Data platform berhasil dihapus');

        } catch (\Exception $e) {
            log_message('error', '[Platform::delete] ' . $e->getMessage());
            return redirect()->back()
                           ->with('error', 'Gagal menghapus data platform');
        }
    }
} 