<?php
/**
 * Created by:
 * Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * 2025-01-13
 * 
 * Lab Controller
 * 
 * Controller for managing laboratory test data
 * Handles CRUD operations and other related functionalities
 */

namespace App\Controllers\Master;

use App\Controllers\BaseController;
use App\Models\ItemModel;
use App\Models\ItemStokModel;
use App\Models\ItemRefModel;
use App\Models\ItemRefInputModel;
use App\Models\KategoriModel;
use App\Models\SatuanModel;
use App\Models\MerkModel;
use App\Models\GudangModel;
use App\Models\PengaturanModel;

class Lab extends BaseController
{
    protected $itemModel;
    protected $validation;
    protected $pengaturan;

    public function __construct()
    {
        $this->itemModel         = new ItemModel();
        $this->itemStokModel     = new ItemStokModel();
        $this->itemRefModel      = new ItemRefModel();
        $this->itemRefInputModel = new ItemRefInputModel();
        $this->kategoriModel     = new KategoriModel();
        $this->merkModel         = new MerkModel();
        $this->satuanModel       = new SatuanModel();
        $this->gudangModel       = new GudangModel();
        $this->pengaturan        = new PengaturanModel();
        $this->validation        = \Config\Services::validation();
    }

    public function index()
    {
        $currentPage = $this->request->getVar('page_lab') ?? 1;
        $perPage = 10;

        // Start with the model query
        $query = $this->itemModel->getLab();

        // Filter by name/code
        $search = $this->request->getVar('search');
        if ($search) {
            $query->groupStart()
                ->like('item', $search)
                ->orLike('kode', $search)
                ->groupEnd();
        }

        // Filter by kategori
        $selectedKategori = $this->request->getVar('kategori');
        if ($selectedKategori) {
            $query->where('tbl_m_item.id_kategori', $selectedKategori);
        }

        // Filter by merk
        $selectedMerk = $this->request->getVar('merk');
        if ($selectedMerk) {
            $query->where('tbl_m_item.id_merk', $selectedMerk);
        }

        // Filter by status
        $selectedStatus = $this->request->getVar('status');
        if ($selectedStatus !== null && $selectedStatus !== '') {
            $query->where('tbl_m_item.status', $selectedStatus);
        }

        // Get kategori and merk lists for filters
        $db = \Config\Database::connect();
        $kategoriList = $db->table('tbl_m_kategori')
            ->select('id, kategori')
            ->where('status', '1')
            ->orderBy('kategori', 'ASC')
            ->get()
            ->getResult();

        $merkList = $db->table('tbl_m_merk')
            ->select('id, merk')
            ->where('status', '1')
            ->orderBy('merk', 'ASC')
            ->get()
            ->getResult();

        // Format lists for dropdown
        $kategoriOptions = [];
        foreach ($kategoriList as $kat) {
            $kategoriOptions[$kat->id] = $kat->kategori;
        }

        $merkOptions = [];
        foreach ($merkList as $mrk) {
            $merkOptions[$mrk->id] = $mrk->merk;
        }

        $data = [
            'title'          => 'Data Laboratorium',
            'Pengaturan'     => $this->pengaturan,
            'user'           => $this->ionAuth->user()->row(),
            'lab'            => $query->paginate($perPage, 'lab'),
            'pager'          => $this->itemModel->pager,
            'currentPage'    => $currentPage,
            'perPage'        => $perPage,
            'search'         => $search,
            'selectedStatus' => $selectedStatus,
            'trashCount'     => $this->itemModel->countDeleted(3), // 3 for LAB
            'kategoriList'   => $kategoriOptions,
            'merkList'       => $merkOptions,
            'selectedKategori' => $selectedKategori,
            'selectedMerk'   => $selectedMerk,
            'breadcrumbs'    => '
                <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>
                <li class="breadcrumb-item">Master</li>
                <li class="breadcrumb-item active">Laboratorium</li>
            '
        ];

        return view($this->theme->getThemePath() . '/master/lab/index', $data);
    }

    /**
     * Show create form
     */
    public function create()
    {
        // Get active data for dropdowns
        $kategoris  = $this->kategoriModel->where('status', '1')->findAll();
        $merks      = $this->merkModel->where('status', '1')->findAll();
        $satuans    = $this->satuanModel->where('status', '1')->findAll();

        $data = [
            'title'         => 'Tambah Laboratorium',
            'Pengaturan'    => $this->pengaturan,
            'kategoris'     => $kategoris,
            'merks'         => $merks,
            'satuans'       => $satuans,
            'user'          => $this->ionAuth->user()->row(),
            'validation'    => $this->validation,
            'breadcrumbs'   => '
                <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>
                <li class="breadcrumb-item">Master</li>
                <li class="breadcrumb-item"><a href="' . base_url('master/lab') . '">Laboratorium</a></li>
                <li class="breadcrumb-item active">Tambah</li>
            '
        ];

        return view($this->theme->getThemePath() . '/master/lab/create', $data);
    }

    /**
     * Store new lab data
     */
    public function store(){
        // Validation rules
        $rules = [
            env('security.tokenName') => [
                'rules' => 'required',
                'errors' => [
                    'required' => env('csrf.name') . ' harus diisi'
                ]
            ],
            'kategori' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Kategori harus dipilih'
                ]
            ],
            'item' => [
                'rules' => 'required|max_length[160]',
                'errors' => [
                    'required'      => 'Nama lab harus diisi',
                    'max_length'    => 'Nama lab maksimal 160 karakter'
                ]
            ],
            'harga_jual' => [
                'rules' => 'required',
                'errors' => [
                    'required'              => 'Harga jual harus diisi',
                    'numeric'               => 'Harga jual harus berupa angka',
                    'greater_than_equal_to' => 'Harga jual tidak boleh negatif'
                ]
            ],
            'satuan' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Satuan harus dipilih',
                ]
            ],
            'status' => [
                'rules' => 'required|in_list[0,1]',
                'errors' => [
                    'required'  => 'Status harus dipilih',
                    'in_list'   => 'Status tidak valid'
                ]
            ]
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('validation_errors', $this->validator->getErrors())
                ->with('error', 'Validasi gagal. Silakan periksa kembali input Anda.');
        }

        // Start transaction
        $this->db->transStart();
        try {
            $data = [
                'id_kategori' => $this->request->getVar('kategori'),
                'id_merk'     => $this->request->getVar('merk'),
                'id_satuan'   => $this->request->getVar('satuan'),
                'kode'        => $this->itemModel->generateKode(3), // LABORATORIEM
                'item'        => $this->request->getPost('item'),
                'item_kand'   => $this->request->getPost('item_kand'),
                'harga_beli'  => format_angka_db($this->request->getVar('harga_beli')),
                'harga_jual'  => format_angka_db($this->request->getVar('harga_jual')),
                'status'      => $this->request->getPost('status'),
                'status_stok' => $this->request->getPost('status_stok') ?? 1,
                'status_item' => 3, // LABORATORIEM
                'id_user'     => $this->ionAuth->user()->row()->id
            ];

            if ($this->itemModel->insert($data)) {
                $lastInsertId = $this->itemModel->getInsertID();
    
                // If stockable, create initial stock entries for active warehouses
                if ($data['status_stok'] == '1') {
                    $gudangAktif = $this->gudangModel->where('status', '1')->findAll();
    
                    foreach ($gudangAktif as $gudang) {
                        // Check if stock entry already exists for this warehouse and item
                        $existingStok = $this->itemStokModel->where([
                            'id_gudang' => $gudang->id,
                            'id_item'   => $lastInsertId
                        ])->first();
    
                        // Only create new stock entry if it doesn't exist
                        if (!$existingStok) {
                            $stokData = [
                                'id_gudang'  => $gudang->id,
                                'id_item'    => $lastInsertId,
                                'id_satuan'  => $data['id_satuan'],
                                'jml'        => 0,
                                'status'     => $gudang->status
                            ];
    
                            $this->itemStokModel->insert($stokData);
                        }
                    }
                }

                $this->db->transCommit();
                
                return redirect()->to(base_url('master/lab'))
                    ->with('success', 'Data lab berhasil ditambahkan');
            }
    
            return redirect()->back()
                ->with('error', 'Gagal menambahkan data lab')
                ->withInput();

        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', '[Lab::store] ' . $e->getMessage());
            
            return redirect()->back()
                ->withInput()
                ->with('error', ENVIRONMENT === 'development' ? $e->getMessage() : 'Gagal menyimpan data lab');
        }
    }

    /**
     * Show edit form
     */
    public function edit($id = null)
    {
        if (!$id) {
            return redirect()->back()
                ->with('error', 'ID Pemeriksaan Lab tidak ditemukan');
        }

        $lab = $this->itemModel->find($id);
        if (!$lab) {
            return redirect()->back()
                ->with('error', 'Data Pemeriksaan Lab tidak ditemukan');
        }

        // Get active data for dropdowns
        $kategoris  = $this->kategoriModel->where('status', '1')->findAll();
        $merks      = $this->merkModel->where('status', '1')->findAll();
        $satuans    = $this->satuanModel->where('status', '1')->findAll();
        
        // Get item references
        $item_refs = $this->itemRefModel->getRefsByItem($id);
        
        // Get lab reference inputs
        $lab_refs = $this->itemRefInputModel->getByItemId($id);

        $data = [
            'title'         => 'Edit Laboratorium',
            'Pengaturan'    => $this->pengaturan,
            'kategoris'     => $kategoris,
            'merks'         => $merks,
            'satuans'       => $satuans,
            'item_refs'     => $item_refs,
            'lab_refs'      => $lab_refs,
            'lab'           => $lab,
            'user'          => $this->ionAuth->user()->row(),
            'validation'    => $this->validation,
            'breadcrumbs'   => '
                <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>
                <li class="breadcrumb-item">Master</li>
                <li class="breadcrumb-item"><a href="' . base_url('master/lab') . '">Laboratorium</a></li>
                <li class="breadcrumb-item active">Edit</li>
            '
        ];

        return view($this->theme->getThemePath() . '/master/lab/edit', $data);
    }

    /**
     * Update lab data
     */
    public function update($id = null)
    {
        if (!$id) {
            return redirect()->back()
                ->with('error', 'ID Pemeriksaan Lab tidak ditemukan');
        }

        // Validation rules
        $rules = [
            env('security.tokenName') => [
                'rules' => 'required',
                'errors' => [
                    'required' => env('csrf.name') . ' harus diisi'
                ]
            ],
            'kategori' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Kategori harus dipilih'
                ]
            ],
            'item' => [
                'rules' => 'required|max_length[160]',
                'errors' => [
                    'required'      => 'Nama lab harus diisi',
                    'max_length'    => 'Nama lab maksimal 160 karakter'
                ]
            ],
            'harga_jual' => [
                'rules' => 'required',
                'errors' => [
                    'required'              => 'Harga jual harus diisi',
                    'numeric'               => 'Harga jual harus berupa angka',
                    'greater_than_equal_to' => 'Harga jual tidak boleh negatif'
                ]
            ],
            'satuan' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Satuan harus dipilih',
                ]
            ],
            'status' => [
                'rules' => 'required|in_list[0,1]',
                'errors' => [
                    'required'  => 'Status harus dipilih',
                    'in_list'   => 'Status tidak valid'
                ]
            ]
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('validation_errors', $this->validator->getErrors())
                ->with('error', 'Validasi gagal. Silakan periksa kembali input Anda.');
        }

        // Start transaction
        $this->db->transStart();
        try {
            $data = [
                'id_kategori' => $this->request->getVar('kategori'),
                'id_merk'     => $this->request->getVar('merk'),
                'id_satuan'   => $this->request->getVar('satuan'),
                'item'        => $this->request->getPost('item'),
                'item_kand'   => $this->request->getPost('item_kand'),
                'harga_beli'  => format_angka_db($this->request->getVar('harga_beli')),
                'harga_jual'  => format_angka_db($this->request->getVar('harga_jual')),
                'status'      => $this->request->getPost('status'),
                'status_stok' => $this->request->getPost('status_stok'),
                'remun_tipe'  => $this->request->getPost('remun_tipe'),
                'remun_perc'  => $this->request->getPost('remun_perc'),
                'remun_nom'   => format_angka_db($this->request->getPost('remun_nom')),
                'apres_tipe'  => $this->request->getPost('apres_tipe'),
                'apres_perc'  => $this->request->getPost('apres_perc'),
                'apres_nom'   => format_angka_db($this->request->getPost('apres_nom')),
                'id_user'     => $this->ionAuth->user()->row()->id
            ];

            if ($this->itemModel->update($id, $data)) {
                $this->db->transCommit();
                
                return redirect()->to(base_url('master/lab'))
                    ->with('success', 'Data pemeriksaan lab berhasil diperbarui');
            }

            throw new \Exception('Gagal memperbarui data pemeriksaan lab');

        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', '[Lab::update] ' . $e->getMessage());
            
            return redirect()->back()
                ->withInput()
                ->with('error', ENVIRONMENT === 'development' ? $e->getMessage() : 'Gagal memperbarui data pemeriksaan lab');
        }
    }

    /**
     * Soft delete lab data
     */
    public function delete($id = null)
    {
        if (!$id) {
            return redirect()->back()
                ->with('error', 'ID Pemeriksaan Lab tidak ditemukan');
        }

        try {
            $lab = $this->itemModel->find($id);
            if (!$lab) {
                throw new \Exception('Data Pemeriksaan Lab tidak ditemukan');
            }

            if (!$this->itemModel->delete($id)) {
                throw new \Exception('Gagal menghapus data pemeriksaan lab');
            }

            return redirect()->back()
                ->with('success', 'Data pemeriksaan lab berhasil dihapus');

        } catch (\Exception $e) {
            log_message('error', '[Lab::delete] ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', ENVIRONMENT === 'development' ? $e->getMessage() : 'Gagal menghapus data pemeriksaan lab');
        }
    }

    /**
     * Show trash list
     */
    public function trash()
    {
        $currentPage = $this->request->getVar('page_lab') ?? 1;
        $perPage = $this->pengaturan->pagination_limit;

        // Get builder instance and convert to array for pagination
        $builder = $this->itemModel->getLabTrash();

        // Filter by name/code/alias
        $item = $this->request->getVar('item');
        if ($item) {
            $builder->groupStart()
                ->like('tbl_m_item.item', $item)
                ->orLike('tbl_m_item.kode', $item)
                ->orLike('tbl_m_item.item_alias', $item)
                ->groupEnd();
        }

        // Get total rows for pagination
        $total = $builder->countAllResults(false);

        // Get the data with limit and offset
        $lab = $builder->limit($perPage, ($currentPage - 1) * $perPage)->get()->getResult();

        // Create manual pagination
        $pager = service('pager');
        $pager->setPath('master/lab/trash');
        $pager->makeLinks($currentPage, $perPage, $total, 'adminlte_pagination');

        $data = [
            'title'         => 'Data Sampah Laboratorium',
            'Pengaturan'    => $this->pengaturan,
            'user'          => $this->ionAuth->user()->row(),
            'lab'           => $lab,
            'pager'         => $pager,
            'currentPage'   => $currentPage,
            'perPage'       => $perPage,
            'search'        => $item,
            'breadcrumbs'   => '
                <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>
                <li class="breadcrumb-item">Master</li>
                <li class="breadcrumb-item"><a href="' . base_url('master/lab') . '">Laboratorium</a></li>
                <li class="breadcrumb-item active">Sampah</li>
            '
        ];

        return view($this->theme->getThemePath() . '/master/lab/trash', $data);
    }

    /**
     * Restore soft deleted item
     */
    public function restore($id = null)
    {
        if (!$id) {
            return redirect()->back()
                ->with('error', 'ID Pemeriksaan Lab tidak ditemukan');
        }

        try {
            $data = [
                'status_hps'  => '0',
                'deleted_at'  => null,
                'updated_at'  => date('Y-m-d H:i:s')
            ];

            if (!$this->itemModel->update($id, $data)) {
                throw new \Exception('Gagal memulihkan data pemeriksaan lab');
            }

            return redirect()->back()
                ->with('success', 'Data pemeriksaan lab berhasil dipulihkan');

        } catch (\Exception $e) {
            log_message('error', '[Lab::restore] ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', ENVIRONMENT === 'development' ? $e->getMessage() : 'Gagal memulihkan data pemeriksaan lab');
        }
    }

    /**
     * Permanently delete lab data
     */
    public function delete_permanent($id = null)
    {
        if (!$id) {
            return redirect()->back()
                ->with('error', 'ID Pemeriksaan Lab tidak ditemukan');
        }

        try {
            if (!$this->itemModel->delete_permanent($id)) {
                throw new \Exception('Gagal menghapus permanen data pemeriksaan lab');
            }

            return redirect()->back()
                ->with('success', 'Data pemeriksaan lab berhasil dihapus permanen');

        } catch (\Exception $e) {
            log_message('error', '[Lab::delete_permanent] ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', ENVIRONMENT === 'development' ? $e->getMessage() : 'Gagal menghapus permanen data pemeriksaan lab');
        }
    }
    




    public function item_ref_save($id_item = null){
        if (!$id_item) {
            return redirect()->back()
                ->with('error', 'ID tindakan tidak ditemukan');
        }

        // Start transaction
        $this->db->transStart();

        try {
            $data = [
                'id_item'        => $id_item,
                'id_item_ref'    => $this->request->getPost('id_item_ref'),
                'item'           => $this->request->getPost('item_ref'),
                'jml'            => format_angka_db($this->request->getPost('jml')),
                'harga'          => format_angka_db($this->request->getPost('harga_item_ref')),
                'subtotal'       => format_angka_db($this->request->getPost('jml')) * format_angka_db($this->request->getPost('harga_item_ref')),
                'status'         => '1',
                'id_user'        => $this->ionAuth->user()->row()->id
            ];

            if (!$this->itemRefModel->insert($data)) {
                throw new \Exception('Gagal menyimpan data referensi tindakan');
            }

            $this->db->transCommit();

            return redirect()->back()
                ->with('success', 'Berhasil menyimpan data referensi tindakan');
        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', '[Tindakan::item_ref_save] ' . $e->getMessage());
            
            return redirect()->back()
                ->withInput()
                ->with('error', ENVIRONMENT === 'development' ? $e->getMessage() : 'Gagal menyimpan data referensi tindakan');
        }
    }

    public function item_ref_delete($id = null){
        if (!$id) {
            return redirect()->back()
                ->with('error', 'ID referensi tindakan tidak ditemukan');
        }

        try {
            if (!$this->itemRefModel->delete($id)) {
                throw new \Exception('Gagal menghapus data referensi tindakan');
            }

            return redirect()->back()
                ->with('success', 'Berhasil menghapus data referensi tindakan');

        } catch (\Exception $e) {
            log_message('error', '[Tindakan::item_ref_delete] ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', ENVIRONMENT === 'development' ? $e->getMessage() : 'Gagal menghapus data referensi tindakan');
        }
    }

    /**
     * Save laboratory item reference input data
     * 
     * @param int|null $id The ID of the laboratory item
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function item_lab_save($id = null){
        if (!$id) {
            return redirect()->back()
                ->with('error', 'ID Lab tidak ditemukan');
        }

        // Validate form data
        $rules = [
            'item_pemeriksaan' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Nama item pemeriksaan harus diisi'
                ]
            ],
            'nilai' => [
                'rules' => 'permit_empty',
                'errors' => []
            ],
            'nilai_l1' => [
                'rules' => 'permit_empty',
                'errors' => []
            ],
            'nilai_l2' => [
                'rules' => 'permit_empty', 
                'errors' => []
            ],
            'nilai_p1' => [
                'rules' => 'permit_empty',
                'errors' => []
            ],
            'nilai_p2' => [
                'rules' => 'permit_empty',
                'errors' => []
            ],
            'satuan' => [
                'rules' => 'permit_empty',
                'errors' => []
            ]
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('error', $this->validator->listErrors());
        }

        try {            
            // Get the posted data
            $data = [
                'id_item'       => $id,
                'id_user'       => $this->ionAuth->user()->row()->id,
                'item_name'     => $this->request->getPost('item_pemeriksaan'),
                'item_value'    => $this->request->getPost('nilai'),
                'item_value_l1' => $this->request->getPost('nilai_l1'),
                'item_value_l2' => $this->request->getPost('nilai_l2'),
                'item_value_p1' => $this->request->getPost('nilai_p1'),
                'item_value_p2' => $this->request->getPost('nilai_p2'),
                'item_satuan'   => $this->request->getPost('satuan')
            ];

            // Validate required fields
            if (empty($data['item_name'])) {
                throw new \Exception('Nama item harus diisi');
            }

            // Save the data
            if (!$this->itemRefInputModel->insert($data)) {
                throw new \Exception('Gagal menyimpan data referensi lab');
            }

            return redirect()->back()
                ->with('success', 'Data referensi lab berhasil disimpan');

        } catch (\Exception $e) {
            log_message('error', '[Lab::item_lab_save] ' . $e->getMessage());
            
            return redirect()->back()
                ->withInput()
                ->with('error', ENVIRONMENT === 'development' ? $e->getMessage() : 'Gagal menyimpan data referensi lab');
        }
    }

    /**
     * Delete lab reference item
     */
    public function item_lab_delete($id = null)
    {
        try {
            if (!$id) {
                throw new \Exception('ID tidak valid');
            }

            // Delete the reference item
            if (!$this->itemRefInputModel->delete($id)) {
                throw new \Exception('Gagal menghapus data referensi lab');
            }

            return redirect()->back()
                ->with('success', 'Data referensi lab berhasil dihapus');

        } catch (\Exception $e) {
            log_message('error', '[Lab::item_lab_delete] ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', ENVIRONMENT === 'development' ? $e->getMessage() : 'Gagal menghapus data referensi lab');
        }
    }
    
    

    /**
     * Export lab items to Excel
     */
    public function xls_items()
    {
        try {
            // Get query builder instance
            $query = $this->itemModel->getLab();

            // Apply filters
            $item = $this->request->getVar('search');
            if ($item) {
                $query->groupStart()
                    ->like('tbl_m_item.item', $item)
                    ->orLike('tbl_m_item.kode', $item)
                    ->orLike('tbl_m_item.item_alias', $item)
                    ->groupEnd();
            }

            // Filter by kategori
            $selectedKategori = $this->request->getVar('kategori');
            if ($selectedKategori) {
                $query->where('tbl_m_item.id_kategori', $selectedKategori);
            }

            // Filter by merk
            $selectedMerk = $this->request->getVar('merk');
            if ($selectedMerk) {
                $query->where('tbl_m_item.id_merk', $selectedMerk);
            }

            // Filter by status
            $selectedStatus = $this->request->getVar('status');
            if ($selectedStatus !== null && $selectedStatus !== '') {
                $query->where('tbl_m_item.status', $selectedStatus);
            }

            $items = $query->get()->getResult();

            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // Set column headers
            $sheet->setCellValue('A1', 'No');
            $sheet->setCellValue('B1', 'Kode');
            $sheet->setCellValue('C1', 'Kategori');
            $sheet->setCellValue('D1', 'Merk');
            $sheet->setCellValue('E1', 'Nama Lab');
            $sheet->setCellValue('F1', 'Alias');
            $sheet->setCellValue('G1', 'Keterangan');
            $sheet->setCellValue('H1', 'Harga Beli');
            $sheet->setCellValue('I1', 'Harga Jual');
            $sheet->setCellValue('J1', 'Status');

            // Style the header row
            $headerStyle = [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4B5366'],
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    ],
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
            ];
            $sheet->getStyle('A1:J1')->applyFromArray($headerStyle);

            // Set row height for header
            $sheet->getRowDimension(1)->setRowHeight(30);

            // Auto size columns
            foreach (range('A', 'J') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            // Add data rows
            $row = 2;
            foreach ($items as $i => $item) {
                $sheet->setCellValue('A' . $row, $i + 1);
                $sheet->setCellValue('B' . $row, $item->kode);
                $sheet->setCellValue('C' . $row, $item->kategori);
                $sheet->setCellValue('D' . $row, $item->merk);
                $sheet->setCellValue('E' . $row, $item->item);
                $sheet->setCellValue('F' . $row, $item->item_alias);
                $sheet->setCellValue('G' . $row, $item->item_kand);
                $sheet->setCellValue('H' . $row, format_angka_rp($item->harga_beli));
                $sheet->setCellValue('I' . $row, format_angka_rp($item->harga_jual));
                $sheet->setCellValue('J' . $row, $item->status == '1' ? 'Aktif' : 'Non-Aktif');

                // Style for data rows
                $rowStyle = [
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        ],
                    ],
                    'alignment' => [
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    ],
                ];
                $sheet->getStyle('A' . $row . ':J' . $row)->applyFromArray($rowStyle);

                // Set row height
                $sheet->getRowDimension($row)->setRowHeight(25);

                // Center align specific columns
                $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('B' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('J' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                // Right align price columns
                $sheet->getStyle('H' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                $sheet->getStyle('I' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

                $row++;
            }

            // Create Excel file
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            $filename = 'data_laboratorium_' . date('Y-m-d_His') . '.xlsx';

            // Set headers for download
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $filename . '"');
            header('Cache-Control: max-age=0');

            // Save file to PHP output
            $writer->save('php://output');
            exit();

        } catch (\Exception $e) {
            log_message('error', '[Lab::xls_items] ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', ENVIRONMENT === 'development' ? $e->getMessage() : 'Gagal mengekspor data laboratorium');
        }
    }
} 