<?php
/**
 * Kategori Controller
 * 
 * Controller for managing categories (kategori)
 * Handles CRUD operations and other related functionalities
 * 
 * @author    Mikhael Felian Waskito <mikhaelfelian@gmail.com>
 * @date      2025-01-12
 */

namespace App\Controllers\Master;

use App\Controllers\BaseController;
use App\Models\KategoriModel;

class Kategori extends BaseController
{
    protected $kategoriModel;
    protected $validation;

    public function __construct()
    {
        $this->kategoriModel = new KategoriModel();
        $this->validation = \Config\Services::validation();
    }

    public function index()
    {
        $currentPage = $this->request->getVar('page_kategori') ?? 1;
        $perPage = 10;
        $keyword = $this->request->getVar('keyword');

        if ($keyword) {
            $this->kategoriModel->groupStart()
                ->like('kategori', $keyword)
                ->orLike('kode', $keyword)
                ->orLike('keterangan', $keyword)
                ->groupEnd();
        }

        $data = [
            'title'         => 'Data Kategori',
            'Pengaturan'    => $this->pengaturan,
            'user'          => $this->ionAuth->user()->row(),
            'kategori'      => $this->kategoriModel->paginate($perPage, 'kategori'),
            'pager'         => $this->kategoriModel->pager,
            'currentPage'   => $currentPage,
            'perPage'       => $perPage,
            'keyword'       => $keyword,
            'breadcrumbs'   => '
                <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>
                <li class="breadcrumb-item">Master</li>
                <li class="breadcrumb-item active">Kategori</li>
            '
        ];

        return view($this->theme->getThemePath() . '/master/kategori/index', $data);
    }

    public function create()
    {
        $data = [
            'title'         => 'Form Kategori',
            'Pengaturan'    => $this->pengaturan,
            'user'          => $this->ionAuth->user()->row(),
            'validation'    => $this->validation,
            'breadcrumbs'   => '
                <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>
                <li class="breadcrumb-item">Master</li>
                <li class="breadcrumb-item"><a href="' . base_url('master/kategori') . '">Kategori</a></li>
                <li class="breadcrumb-item active">Tambah</li>
            '
        ];

        return view($this->theme->getThemePath() . '/master/kategori/create', $data);
    }

    public function store()
    {
        // Validation rules
        $rules = [
            'kategori' => [
                'rules' => 'required|max_length[255]',
                'errors' => [
                    'required' => 'Kategori harus diisi',
                    'max_length' => 'Kategori maksimal 255 karakter'
                ]
            ],
            env('security.tokenName', 'csrf_test_name') => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'CSRF token tidak valid'
                ]
            ]
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Validasi gagal');
        }

        $data = [
            'kode'       => $this->kategoriModel->generateKode(),
            'kategori'   => $this->request->getPost('kategori'),
            'keterangan' => $this->request->getPost('keterangan'),
            'status'     => $this->request->getPost('status')
        ];

        if ($this->kategoriModel->insert($data)) {
            return redirect()->to(base_url('master/kategori'))
                ->with('success', 'Data kategori berhasil ditambahkan');
        }

        return redirect()->back()
            ->with('error', 'Gagal menambahkan data kategori')
            ->withInput();
    }

    public function edit($id)
    {
        $data = [
            'title'         => 'Form Kategori',
            'Pengaturan'    => $this->pengaturan,
            'user'          => $this->ionAuth->user()->row(),
            'validation'    => $this->validation,
            'breadcrumbs'   => '
                <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>
                <li class="breadcrumb-item">Master</li>
                <li class="breadcrumb-item"><a href="' . base_url('master/kategori') . '">Kategori</a></li>
                <li class="breadcrumb-item active">Tambah</li>
            '
        ];
        $data['kategori'] = $this->kategoriModel->find($id);

        if (empty($data['kategori'])) {
            return redirect()->to(base_url('master/kategori'))
                ->with('error', 'Data kategori tidak ditemukan');
        }

        return view($this->theme->getThemePath() . '/master/kategori/edit', $data);
    }

    public function update($id)
    {
        // Validation rules
        $rules = [
            'kategori' => [
                'rules' => 'required|max_length[255]',
                'errors' => [
                    'required' => 'Kategori harus diisi',
                    'max_length' => 'Kategori maksimal 255 karakter'
                ]
            ],
            env('security.tokenName', 'csrf_test_name') => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'CSRF token tidak valid'
                ]
            ]
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Validasi gagal');
        }

        $data = [
            'kategori'   => $this->request->getPost('kategori'),
            'keterangan' => $this->request->getPost('keterangan'),
            'status'     => $this->request->getPost('status')
        ];

        if ($this->kategoriModel->update($id, $data)) {
            return redirect()->to(base_url('master/kategori'))
                ->with('success', 'Data kategori berhasil diubah!');
        }

        return redirect()->back()
            ->with('error', 'Gagal mengupdate data kategori')
            ->withInput();
    }

    public function delete($id)
    {
        if ($this->kategoriModel->delete($id)) {
            return redirect()->to(base_url('master/kategori'))
                ->with('success', 'Data kategori berhasil dihapus');
        }

        return redirect()->back()
            ->with('error', 'Gagal menghapus data kategori');
    }
}