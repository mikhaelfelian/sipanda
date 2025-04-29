<?php
/**
 * Created by:
 * Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * 2025-02-08
 * 
 * KategoriObat Controller
 * 
 * Handles medicine category management operations
 */

namespace App\Controllers\Master;

use App\Controllers\BaseController;
use App\Models\KategoriObatModel;

class KategoriObat extends BaseController
{
    protected $kategoriObatModel;
    
    public function __construct()
    {
        $this->kategoriObatModel = new KategoriObatModel();
    }

    public function index()
    {
        $currentPage = $this->request->getVar('page_kategori_obat') ?? 1;
        $perPage = 10;
        $search = $this->request->getVar('search');

        $query = $this->kategoriObatModel;
        
        if ($search) {
            $query = $query->like('jenis', $search)
                          ->orLike('keterangan', $search);
        }

        $data = [
            'title'         => 'Data Jenis Obat',
            'Pengaturan'    => $this->pengaturan,
            'user'          => $this->ionAuth->user()->row(),
            'jenis'         => $query->paginate($perPage, 'jenis'),
            'pager'         => $query->pager,
            'currentPage'   => $currentPage,
            'perPage'       => $perPage,
            'search'        => $search,
            'breadcrumbs'   => '

                <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>
                <li class="breadcrumb-item">Master</li>
                <li class="breadcrumb-item active">Jenis Obat</li>
            '
        ];

        return view($this->theme->getThemePath() . '/master/kategori_obat/index', $data);
    }

    public function create()
    {
        $data = [
            'title' => 'Tambah Jenis Obat',
            'Pengaturan'    => $this->pengaturan,
            'user'          => $this->ionAuth->user()->row(),
            'breadcrumbs' => '
                <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>
                <li class="breadcrumb-item">Master</li>
                <li class="breadcrumb-item"><a href="' . base_url('master/jenis') . '">Jenis Obat</a></li>
                <li class="breadcrumb-item active">Tambah</li>
            '
        ];

        return view($this->theme->getThemePath() . '/master/kategori_obat/create', $data);
    }

    public function store()
    {
        $rules = [
            'jenis' => [
                'rules' => 'required|max_length[160]',
                'errors' => [
                    'required' => 'Jenis obat harus diisi',
                    'max_length' => 'Jenis obat maksimal 160 karakter'
                ]
            ]
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                            ->withInput()
                            ->with('validation_errors', $this->validator->getErrors());
        }

        try {
            $data = [
                'jenis' => $this->request->getPost('jenis'),
                'keterangan' => $this->request->getPost('keterangan'),
                'status' => $this->request->getPost('status') ?? '1'
            ];

            $this->kategoriObatModel->insert($data);

            return redirect()->to('master/jenis')
                            ->with('success', 'Data berhasil ditambahkan');
        } catch (\Exception $e) {
            return redirect()->back()
                            ->withInput()
                            ->with('error', 'Gagal menambahkan data: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $kategoriObat = $this->kategoriObatModel->find($id);
        
        if (!$kategoriObat) {
            return redirect()->to('master/jenis')
                            ->with('error', 'Data tidak ditemukan');
        }

        $data = [
            'title' => 'Edit Jenis Obat',
            'Pengaturan'    => $this->pengaturan,
            'user'          => $this->ionAuth->user()->row(),
            'kategori_obat' => $kategoriObat,
            'breadcrumbs' => '
                <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>

                <li class="breadcrumb-item">Master</li>
                <li class="breadcrumb-item"><a href="' . base_url('master/jenis') . '">Jenis Obat</a></li>
                <li class="breadcrumb-item active">Edit</li>
            '
        ];

        return view($this->theme->getThemePath() . '/master/kategori_obat/edit', $data);
    }

    public function update($id)
    {
        $kategoriObat = $this->kategoriObatModel->find($id);
        
        if (!$kategoriObat) {
            return redirect()->to('master/jenis')
                            ->with('error', 'Data tidak ditemukan');
        }

        $rules = [
            'jenis' => [
                'rules' => 'required|max_length[160]',
                'errors' => [
                    'required' => 'Jenis obat harus diisi',
                    'max_length' => 'Jenis obat maksimal 160 karakter'
                ]
            ]
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                            ->withInput()
                            ->with('validation_errors', $this->validator->getErrors());
        }

        try {
            $data = [
                'jenis' => $this->request->getPost('jenis'),
                'keterangan' => $this->request->getPost('keterangan'),
                'status' => $this->request->getPost('status') ?? '1'
            ];

            $this->kategoriObatModel->update($id, $data);

            return redirect()->to('master/jenis')
                            ->with('success', 'Data berhasil diupdate');
        } catch (\Exception $e) {
            return redirect()->back()
                            ->withInput()
                            ->with('error', 'Gagal mengupdate data: ' . $e->getMessage());
        }
    }

    public function delete($id)
    {
        try {
            $kategoriObat = $this->kategoriObatModel->find($id);
            
            if (!$kategoriObat) {
                throw new \Exception('Data tidak ditemukan');
            }

            $this->kategoriObatModel->delete($id);

            return redirect()->to('master/jenis')
                            ->with('success', 'Data berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->to('master/jenis')
                            ->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }
} 