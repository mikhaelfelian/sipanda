<?php
/**
 * Created by:
 * Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * 2025-01-18
 * 
 * Kamar Controller
 * 
 * Controller for managing Kamar (Room) data
 */

namespace App\Controllers\Master;

use App\Controllers\BaseController;
use App\Models\KamarModel;
use App\Models\PengaturanModel;

class Kamar extends BaseController
{
    protected $kamarModel;
    protected $validation;
    protected $pengaturan;

    public function __construct()
    {
        $this->kamarModel = new KamarModel();
        $this->pengaturan = new PengaturanModel();
        $this->validation = \Config\Services::validation();
    }

    public function index()
    {
        $currentPage = $this->request->getVar('page_kamar') ?? 1;
        $perPage = $this->pengaturan->pagination_limit ?? 10;

        // Start with the model query
        $query = $this->kamarModel;

        // Filter by code/name
        $search = $this->request->getVar('search');
        if ($search) {
            $query->groupStart()
                ->like('kode', $search)
                ->orLike('kamar', $search)
                ->groupEnd();
        }

        // Filter by status
        $status = $this->request->getVar('status');
        if ($status !== null && $status !== '') {
            $query->where('status', $status);
        }

        $data = [
            'title'          => 'Data Kamar',
            'kamars'         => $query->paginate($perPage, 'kamar'),
            'pager'          => $this->kamarModel->pager,
            'currentPage'    => $currentPage,
            'perPage'        => $perPage,
            'search'         => $search,
            'status'         => $status,
            'getStatusLabel' => function($status) {
                return $this->kamarModel->getStatusLabel($status);
            },
            'breadcrumbs'    => '
                <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>
                <li class="breadcrumb-item">Master</li>
                <li class="breadcrumb-item active">Kamar</li>
            '
        ];

        return $this->view($this->theme->getThemePath() . '/master/kamar/index', $data);
    }

    /**
     * Display create form
     */
    public function create()
    {
        $data = [
            'title'       => 'Tambah Kamar',
            'validation'  => $this->validation,
            'kode'        => $this->kamarModel->generateKode(),
            'breadcrumbs' => '
                <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>
                <li class="breadcrumb-item"><a href="' . base_url('master/kamar') . '">Kamar</a></li>
                <li class="breadcrumb-item active">Tambah</li>
            '
        ];

        return $this->view($this->theme->getThemePath() . '/master/kamar/create', $data);
    }

    /**
     * Store new kamar data
     */
    public function store()
    {
        // Validation rules
        $rules = [
            'kode' => [
                'rules'  => 'required|is_unique[tbl_m_kamar.kode]',
                'errors' => [
                    'required'  => 'Kode kamar harus diisi',
                    'is_unique' => 'Kode kamar sudah digunakan'
                ]
            ],
            'kamar' => [
                'rules'  => 'required',
                'errors' => [
                    'required' => 'Nama kamar harus diisi'
                ]
            ],
            'jml_max' => [
                'rules'  => 'required|numeric|greater_than[0]',
                'errors' => [
                    'required' => 'Kapasitas kamar harus diisi',
                    'numeric' => 'Kapasitas kamar harus berupa angka',
                    'greater_than' => 'Kapasitas kamar harus lebih dari 0'
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
                'kode'    => $this->request->getPost('kode'),
                'kamar'   => $this->request->getPost('kamar'),
                'jml'     => 0, // Default value for new room
                'jml_max' => $this->request->getPost('jml_max'),
                'status'  => $this->request->getPost('status')
            ];

            if (!$this->kamarModel->insert($data)) {
                throw new \RuntimeException('Gagal menyimpan data kamar');
            }

            return redirect()->to(base_url('master/kamar'))
                           ->with('success', 'Data kamar berhasil ditambahkan');

        } catch (\Exception $e) {
            log_message('error', '[Kamar::store] ' . $e->getMessage());
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Gagal menyimpan data kamar');
        }
    }

    /**
     * Display edit form
     */
    public function edit($id = null)
    {
        if (!$id) {
            return redirect()->to('master/kamar')
                           ->with('error', 'ID Kamar tidak ditemukan');
        }

        $kamar = $this->kamarModel->find($id);
        if (!$kamar) {
            return redirect()->to('master/kamar')
                           ->with('error', 'Data kamar tidak ditemukan');
        }

        $data = [
            'title'       => 'Edit Kamar',
            'validation'  => $this->validation,
            'kamar'       => $kamar,
            'breadcrumbs' => '
                <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>
                <li class="breadcrumb-item"><a href="' . base_url('master/kamar') . '">Kamar</a></li>
                <li class="breadcrumb-item active">Edit</li>
            '
        ];

        return $this->view($this->theme->getThemePath() . '/master/kamar/edit', $data);
    }

    /**
     * Update kamar data
     */
    public function update($id = null)
    {
        if (!$id) {
            return redirect()->to('master/kamar')
                           ->with('error', 'ID Kamar tidak ditemukan');
        }

        // Validation rules
        $rules = [
            'kode' => [
                'rules'  => "required|is_unique[tbl_m_kamar.kode,id,$id]",
                'errors' => [
                    'required'  => 'Kode kamar harus diisi',
                    'is_unique' => 'Kode kamar sudah digunakan'
                ]
            ],
            'kamar' => [
                'rules'  => 'required',
                'errors' => [
                    'required' => 'Nama kamar harus diisi'
                ]
            ],
            'jml_max' => [
                'rules'  => 'required|numeric|greater_than[0]',
                'errors' => [
                    'required' => 'Kapasitas kamar harus diisi',
                    'numeric' => 'Kapasitas kamar harus berupa angka',
                    'greater_than' => 'Kapasitas kamar harus lebih dari 0'
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
                'kode'    => $this->request->getPost('kode'),
                'kamar'   => $this->request->getPost('kamar'),
                'jml_max' => $this->request->getPost('jml_max'),
                'status'  => $this->request->getPost('status')
            ];

            if (!$this->kamarModel->update($id, $data)) {
                throw new \RuntimeException('Gagal mengupdate data kamar');
            }

            return redirect()->to(base_url('master/kamar'))
                           ->with('success', 'Data kamar berhasil diupdate');

        } catch (\Exception $e) {
            log_message('error', '[Kamar::update] ' . $e->getMessage());
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Gagal mengupdate data kamar');
        }
    }

    /**
     * Display kamar details
     */
    public function detail($id = null)
    {
        if (!$id) {
            return redirect()->to('master/kamar')
                           ->with('error', 'ID Kamar tidak ditemukan');
        }

        $kamar = $this->kamarModel->find($id);
        if (!$kamar) {
            return redirect()->to('master/kamar')
                           ->with('error', 'Data kamar tidak ditemukan');
        }

        $data = [
            'title'       => 'Detail Kamar',
            'kamar'       => $kamar,
            'breadcrumbs' => '
                <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>
                <li class="breadcrumb-item"><a href="' . base_url('master/kamar') . '">Kamar</a></li>
                <li class="breadcrumb-item active">Detail</li>
            '
        ];

        return $this->view($this->theme->getThemePath() . '/master/kamar/detail', $data);
    }

    /**
     * Delete kamar data permanently
     */
    public function delete($id = null)
    {
        if (!$id) {
            return redirect()->to('master/kamar')
                           ->with('error', 'ID Kamar tidak ditemukan');
        }

        try {
            $kamar = $this->kamarModel->find($id);
            if (!$kamar) {
                throw new \RuntimeException('Data kamar tidak ditemukan');
            }

            // Check if room is occupied
            if ($kamar->jml > 0) {
                throw new \RuntimeException('Kamar masih terisi, tidak dapat dihapus');
            }

            if (!$this->kamarModel->delete($id)) {
                throw new \RuntimeException('Gagal menghapus data kamar');
            }

            return redirect()->to(base_url('master/kamar'))
                           ->with('success', 'Data kamar berhasil dihapus');

        } catch (\Exception $e) {
            log_message('error', '[Kamar::delete] ' . $e->getMessage());
            return redirect()->back()
                           ->with('error', $e->getMessage());
        }
    }
} 