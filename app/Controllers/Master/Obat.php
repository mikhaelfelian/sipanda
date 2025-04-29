<?php
/**
 * Obat Controller
 * 
 * Controller for managing medicines (obat)
 * Handles CRUD operations and other related functionalities
 * 
 * @author    Mikhael Felian Waskito <mikhaelfelian@gmail.com>
 * @date      2025-01-12
 */

namespace App\Controllers\Master;

use App\Controllers\BaseController;
use App\Models\GudangModel;
use App\Models\ItemModel;
use App\Models\ItemRefModel;
use App\Models\ItemStokModel;
use App\Models\SatuanModel;
use App\Models\KategoriModel;
use App\Models\KategoriObatModel;
use App\Models\MerkModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Obat extends BaseController
{
    protected $itemModel;
    protected $satuanModel;
    protected $kategoriModel;
    protected $merkModel;
    protected $validation;

    public function __construct()
    {
        $this->gudangModel       = new GudangModel();
        $this->itemModel         = new ItemModel();
        $this->itemRefModel      = new ItemRefModel();
        $this->itemStokModel     = new ItemStokModel();
        $this->satuanModel       = new SatuanModel(); 
        $this->kategoriModel     = new KategoriModel();
        $this->kategoriObatModel = new KategoriObatModel();
        $this->merkModel         = new MerkModel();
        $this->validation        = \Config\Services::validation();
    }

    public function index()
    {
        $currentPage    = $this->request->getVar('page_obat') ?? 1;
        $perPage        = 10;
        $query          = $this->itemModel->getObat();

        // Get active kategori list for dropdown
        $kategoriList = [];
        $activeKategori = $this->kategoriModel->where('status', '1')->findAll();
        foreach ($activeKategori as $kategori) {
            $kategoriList[$kategori->id] = $kategori->kategori;
        }

        // Get active merk list for dropdown
        $merkList = [];
        $activeMerks = $this->merkModel->where('status', '1')->findAll();
        foreach ($activeMerks as $merk) {
            $merkList[$merk->id] = $merk->merk;
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

        // Filter by item name/code/alias
        $item = $this->request->getVar('item');
        if ($item) {
            $query->groupStart()
                ->like('tbl_m_item.item', $item)
                ->orLike('tbl_m_item.kode', $item)
                ->orLike('tbl_m_item.item_alias', $item)
                ->groupEnd();
        }

        // Filter by harga_beli
        $hargaBeli = $this->request->getVar('harga_beli');
        if ($hargaBeli) {
            $hargaBeli = format_angka_db($hargaBeli);
            $query->where('tbl_m_item.harga_beli', $hargaBeli);
        }

        // Filter by status
        $selectedStatus = $this->request->getVar('status');
        if ($selectedStatus !== null && $selectedStatus !== '') {
            $query->where('tbl_m_item.status', $selectedStatus);
        }

        $data = [
            'title'           => 'Data Obat',
            'Pengaturan'      => $this->pengaturan,
            'user'            => $this->ionAuth->user()->row(),
            'obat'            => $query->paginate($perPage, 'obat'),
            'pager'           => $this->itemModel->pager,
            'currentPage'     => $currentPage,
            'perPage'         => $perPage,
            'kategoriList'    => $kategoriList,
            'selectedKategori'=> $selectedKategori,
            'merkList'        => $merkList,
            'selectedMerk'    => $selectedMerk,
            'selectedStatus'  => $selectedStatus,
            'trashCount'      => $this->itemModel->countDeleted(),
            'breadcrumbs'     => '
                <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>
                <li class="breadcrumb-item">Master</li>
                <li class="breadcrumb-item active">Obat</li>
            '
        ];

        return view($this->theme->getThemePath() . '/master/obat/index', $data);
    }

    public function create()
    {
        $data = [
            'title'         => 'Form Obat',
            'Pengaturan'    => $this->pengaturan,
            'user'          => $this->ionAuth->user()->row(),
            'validation'    => $this->validation,
            'satuan'        => $this->satuanModel->where('status', '1')->findAll(),
            'kategori'      => $this->kategoriModel->where('status', '1')->findAll(),
            'merk'          => $this->merkModel->where('status', '1')->findAll(),
            'jenis'         => $this->kategoriObatModel->where('status', '1')->findAll(),
            'breadcrumbs'   => '
                <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>


                <li class="breadcrumb-item">Master</li>
                <li class="breadcrumb-item"><a href="' . base_url('master/obat') . '">Obat</a></li>
                <li class="breadcrumb-item active">Tambah</li>
            '
        ];

        return view($this->theme->getThemePath() . '/master/obat/create', $data);
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
            'item' => [
                'rules' => 'required|max_length[160]',
                'errors' => [
                    'required' => 'Nama obat harus diisi',
                    'max_length' => 'Nama obat maksimal 160 karakter'
                ]
            ],
            'id_satuan' => [
                'rules' => 'required|integer',
                'errors' => [
                    'required' => 'Satuan harus dipilih',
                    'integer' => 'Satuan tidak valid'
                ]
            ],
            'id_kategori' => [
                'rules' => 'required|integer',
                'errors' => [
                    'required' => 'Kategori harus dipilih',
                    'integer' => 'Kategori tidak valid'
                ]
            ],
            'jenis' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Jenis Obat harus dipilih'
                ]
            ],            
            'id_merk' => [
                'rules' => 'required|integer',
                'errors' => [
                    'required' => 'Merk harus dipilih',
                    'integer' => 'Merk tidak valid'
                ]
            ],
            'harga_beli' => [
                'rules' => 'required|numeric|greater_than_equal_to[0]',
                'errors' => [
                    'required' => 'Harga beli harus diisi',
                    'numeric' => 'Harga beli harus berupa angka',
                    'greater_than_equal_to' => 'Harga beli tidak boleh negatif'
                ]
            ],
            'harga_jual' => [
                'rules' => 'required|numeric|greater_than_equal_to[0]',
                'errors' => [
                    'required' => 'Harga jual harus diisi',
                    'numeric' => 'Harga jual harus berupa angka',
                    'greater_than_equal_to' => 'Harga jual tidak boleh negatif'
                ]
            ]
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Validasi gagal');
        }

        $data = [
            'id_user'            => $this->ionAuth->user()->row()->id,
            'kode'               => $this->itemModel->generateKode(1),
            'item'               => $this->request->getPost('item'),
            'item_alias'         => $this->request->getPost('item_alias'),
            'item_kand'          => $this->request->getPost('item_kand'),
            'barcode'            => $this->request->getPost('barcode'),
            'id_satuan'          => $this->request->getPost('id_satuan'),
            'id_kategori'        => $this->request->getPost('id_kategori'),
            'id_kategori_obat'   => $this->request->getPost('jenis'),   
            'id_merk'            => $this->request->getPost('id_merk'),
            'jml'                => $this->request->getPost('jml') ?? 0,
            'harga_beli'         => format_angka_db($this->request->getPost('harga_beli')) ?? 0,
            'harga_jual'         => format_angka_db($this->request->getPost('harga_jual')) ?? 0,
            'status'             => $this->request->getPost('status'),
            'status_stok'        => $this->request->getPost('status_stok') ?? '0',
            'status_racikan'     => $this->request->getPost('status_racikan') ?? '0',
            'status_item'        => 1, // 1 = Obat
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
            
            return redirect()->to(base_url('master/obat'))
                ->with('success', 'Data obat berhasil ditambahkan');
        }

        return redirect()->back()
            ->with('error', 'Gagal menambahkan data obat')
            ->withInput();
    }

    public function edit($id)
    {
        $data = [
            'title'         => 'Form Obat',
            'Pengaturan'    => $this->pengaturan,
            'user'          => $this->ionAuth->user()->row(),
            'validation'    => $this->validation,
            'satuan'        => $this->satuanModel->findAll(),
            'kategori'      => $this->kategoriModel->findAll(),
            'merk'          => $this->merkModel->findAll(),
            'jenis'         => $this->kategoriObatModel->where('status', '1')->findAll(),
            'breadcrumbs'   => '
                <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>
                <li class="breadcrumb-item">Master</li>
                <li class="breadcrumb-item"><a href="' . base_url('master/obat') . '">Obat</a></li>
                <li class="breadcrumb-item active">Edit</li>
            '
        ];

        $data['obat']       = $this->itemModel->getItemWithRelations($id);
        $data['item_refs']  = $this->itemRefModel->getRefsByItem($id);

        if (empty($data['obat'])) {
            return redirect()->to(base_url('master/obat'))
                ->with('error', 'Data obat tidak ditemukan');
        }

        return view($this->theme->getThemePath() . '/master/obat/edit', $data);
    }

    public function update($id)
    {
        // Validation rules
        $rules = [
            'item' => [
                'rules' => 'required|max_length[160]',
                'errors' => [
                    'required' => 'Nama obat harus diisi',
                    'max_length' => 'Nama obat maksimal 160 karakter'
                ]
            ],
            'id_satuan' => [
                'rules' => 'required|integer',
                'errors' => [
                    'required' => 'Satuan harus dipilih',
                    'integer' => 'Satuan tidak valid'
                ]
            ],
            'jenis' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Jenis Obat harus dipilih'
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
            'item'              => $this->request->getPost('item'),
            'item_alias'        => $this->request->getPost('item_alias'),
            'item_kand'         => $this->request->getPost('item_kand'),
            'barcode'           => $this->request->getPost('barcode'),
            'id_satuan'         => $this->request->getPost('id_satuan'),
            'id_kategori'       => $this->request->getPost('id_kategori'),
            'id_kategori_obat'  => $this->request->getPost('jenis'),
            'id_merk'           => $this->request->getPost('id_merk'),
            'jml_min'           => $this->request->getPost('jml_min') ?? 0,
            'jml_limit'         => $this->request->getPost('jml_limit') ?? 0,
            'harga_beli'        => format_angka_db($this->request->getPost('harga_beli')) ?? 0,
            'harga_jual'        => format_angka_db($this->request->getPost('harga_jual')) ?? 0,
            'status'            => $this->request->getPost('status'),
            'status_stok'       => $this->request->getPost('status_stok') ?? '0',
            'status_racikan'    => $this->request->getPost('status_racikan') ?? '0'
        ];

        $lastInsertId = $id;

        if ($this->itemModel->update($id, $data)) {
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

            return redirect()->to(base_url('master/obat'))
                ->with('success', 'Data obat berhasil diubah');
        }

        return redirect()->back()
            ->with('error', 'Gagal mengupdate data obat')
            ->withInput();
    }

    public function delete($id)
    {
        // Start transaction
        $this->db->transStart();

        try {
            // Soft delete the item
            $this->itemModel->delete($id);

            $this->db->transCommit();
            
            return redirect()->to(base_url('master/obat'))
                ->with('success', 'Data obat berhasil dihapus');
        } catch (\Exception $e) {
            $this->db->transRollback();
            
            return redirect()->back()
                ->with('error', 'Gagal menghapus data obat');
        }
    }

    public function delete_permanent($id)
    {
        // Start transaction
        $this->db->transStart();

        try {
            // Permanently delete the item
            $this->itemModel->delete($id, true);
            $this->db->transCommit();
            
            return redirect()->to(base_url('master/obat/trash'))
                ->with('success', 'Data obat berhasil dihapus permanen');
        } catch (\Exception $e) {
            $this->db->transRollback();
            
            return redirect()->back()
                ->with('error', 'Gagal menghapus permanen data obat');
        }
    }

    public function trash()
    {
        $currentPage = $this->request->getVar('page_obat') ?? 1;
        $perPage = 10;
        $offset = ($currentPage - 1) * $perPage;
        $keyword = $this->request->getVar('keyword');
        $builder = $this->itemModel->getObatTrash();

        if ($keyword) {
            $builder->groupStart()
                ->like('tbl_m_item.item', $keyword)
                ->orLike('tbl_m_item.kode', $keyword)
                ->orLike('tbl_m_item.barcode', $keyword)
                ->orLike('tbl_m_item.item_alias', $keyword)
                ->groupEnd();
        }

        // Get total rows for pagination
        $total = $builder->countAllResults(false);  // false to not reset the query builder

        // Get paginated results
        $obat = $builder->limit($perPage, $offset)->get()->getResult();

        // Create pager
        $pager = service('pager');
        $pager->setPath('master/obat/trash');
        $pager->makeLinks($currentPage, $perPage, $total, 'adminlte_pagination');

        $data = [
            'title'         => 'Data Obat Terhapus',
            'Pengaturan'    => $this->pengaturan,
            'user'          => $this->ionAuth->user()->row(),
            'obat'          => $obat,
            'pager'         => $pager,
            'currentPage'   => $currentPage,
            'perPage'       => $perPage,
            'total'         => $total,
            'keyword'       => $keyword,
            'breadcrumbs'   => '
                <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>
                <li class="breadcrumb-item">Master</li>
                <li class="breadcrumb-item"><a href="' . base_url('master/obat') . '">Obat</a></li>
                <li class="breadcrumb-item active">Sampah</li>
            '
        ];

        return view($this->theme->getThemePath() . '/master/obat/trash', $data);
    }

    public function restore($id)
    {
        // Start transaction
        $this->db->transStart();

        try {            
            // Restore the item
            $this->itemModel->update($id, [
                'status_hps' => '0',
                'deleted_at' => null,
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            $this->db->transCommit();
            
            return redirect()->to(base_url('master/obat/trash'))
                ->with('success', 'Data obat berhasil dipulihkan');
        } catch (\Exception $e) {
            $this->db->transRollback();
            
            return redirect()->back()
                ->with('error', 'Gagal memulihkan data obat');
        }
    }

    public function item_ref_save($id = null)
    {
        if (!$id) {
            return redirect()->back()
                ->with('error', 'ID obat tidak ditemukan');
        }

        $id_item        = $this->request->getPost('id_item');
        $id_item_ref    = $this->request->getPost('id_item_ref');
        $jml            = $this->request->getPost('jml');

        // Validation rules
        $rules = [
            'id_item' => [
                'rules' => 'required|integer',
                'errors' => [
                    'required' => 'ID item harus diisi',
                    'integer' => 'ID item tidak valid'
                ]
            ],
            'id_item_ref' => [
                'rules' => 'required|integer|differs[id_item]',
                'errors' => [
                    'required' => 'ID item referensi harus diisi',
                    'integer' => 'ID item referensi tidak valid',
                    'differs' => 'Item referensi tidak boleh sama dengan item utama'
                ]
            ],
            'item_ref' => [
                'rules' => 'required|max_length[160]',
                'errors' => [
                    'required' => 'Item harus diisi',
                    'max_length' => 'Item maksimal 160 karakter'
                ]
            ],
            'jml' => [
                'rules' => 'required|numeric|greater_than[0]',
                'errors' => [
                    'required' => 'Jumlah harus diisi',
                    'numeric' => 'Jumlah harus berupa angka',
                    'greater_than' => 'Jumlah harus lebih besar dari 0'
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
            // Get required data
            $sql_item = $this->itemModel->find($id_item);
            if (!$sql_item) {
                throw new \Exception('Item utama tidak ditemukan');
            }

            $sql_item_ref = $this->itemModel->find($id_item_ref);
            if (!$sql_item_ref) {
                throw new \Exception('Item referensi tidak ditemukan');
            }

            $sql_satuan = $this->satuanModel->find($sql_item_ref->id_satuan);
            if (!$sql_satuan) {
                throw new \Exception('Satuan tidak ditemukan');
            }

            // Calculate values
            $subtotal = format_angka_db($sql_item_ref->harga_jual * $jml);

            $data = [
                'id_item'      => $sql_item->id,
                'id_item_ref'  => $sql_item_ref->id,
                'id_satuan'    => $sql_item_ref->id_satuan,
                'id_user'      => $this->ionAuth->user()->row()->id,
                'item'         => $sql_item_ref->item,
                'harga'        => format_angka_db($sql_item_ref->harga_jual),
                'jml'          => (int)$jml,
                'jml_satuan'   => (int)$sql_satuan->jml,
                'subtotal'     => $subtotal,
                'status'       => $sql_item_ref->status
            ];

            // Insert data
            if (!$this->itemRefModel->insert($data)) {
                throw new \Exception('Gagal menyimpan data referensi obat');
            }

            // Commit transaction
            $this->db->transCommit();

            return redirect()->to(base_url('master/obat/edit/' . $id_item))
                ->with('success', 'Berhasil menyimpan data referensi obat');

        } catch (\Exception $e) {
            // Rollback transaction
            $this->db->transRollback();

            log_message('error', '[Obat::item_ref_save] ' . $e->getMessage());
            
            return redirect()->to(base_url('master/obat/edit/' . $id_item))
                ->withInput()
                ->with('error', ENVIRONMENT === 'development' ? $e->getMessage() : 'Gagal menyimpan data referensi obat');
        }
    }

    public function item_ref_delete($id)
    {
        try {
            $itemRef = $this->itemRefModel->find($id);
            if (!$itemRef) {
                throw new \Exception('Data referensi obat tidak ditemukan');
            }

            if (!$this->itemRefModel->delete($id)) {
                throw new \Exception('Gagal menghapus data referensi obat');
            }

            return redirect()->back()
                ->with('success', 'Berhasil menghapus data referensi obat');

        } catch (\Exception $e) {
            log_message('error', '[Obat::item_ref_delete] ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', ENVIRONMENT === 'development' ? $e->getMessage() : 'Gagal menghapus data referensi obat');
        }
    }

    public function xls_items()
    {
        try {
            // Create new Spreadsheet object
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // Set document properties
            $spreadsheet->getProperties()
                ->setCreator($this->pengaturan->judul_app)
                ->setLastModifiedBy($this->pengaturan->judul_app)
                ->setTitle('Data Obat')
                ->setSubject('Data Obat ' . $this->pengaturan->judul_app)
                ->setDescription('Data Obat ' . date('Y-m-d H:i:s'));

            // Add header row
            $sheet->setCellValue('A1', 'No');
            $sheet->setCellValue('B1', 'Kode');
            $sheet->setCellValue('C1', 'Nama Obat');
            $sheet->setCellValue('D1', 'Alias');
            $sheet->setCellValue('E1', 'Kandungan');
            $sheet->setCellValue('F1', 'Kategori');
            $sheet->setCellValue('G1', 'Merk');
            $sheet->setCellValue('H1', 'Satuan');
            $sheet->setCellValue('I1', 'Harga Beli');
            $sheet->setCellValue('J1', 'Harga Jual');
            $sheet->setCellValue('K1', 'Stok');
            $sheet->setCellValue('L1', 'Status Item');
            $sheet->setCellValue('M1', 'isStockAble');
            $sheet->setCellValue('N1', 'isRacikan');

            // Style the header row
            $headerStyle = [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4B5563'],
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    ],
                ],
            ];
            $sheet->getStyle('A1:N1')->applyFromArray($headerStyle);

            // Get data with filters
            $query = $this->itemModel->getObat()->where('tbl_m_item.status_hps', '0');

            // Apply filters
            $selectedKategori = $this->request->getVar('kategori');
            if ($selectedKategori) {
                $query->where('tbl_m_item.id_kategori', $selectedKategori);
            }

            $selectedMerk = $this->request->getVar('merk');
            if ($selectedMerk) {
                $query->where('tbl_m_item.id_merk', $selectedMerk);
            }

            $item = $this->request->getVar('item');
            if ($item) {
                $query->groupStart()
                    ->like('tbl_m_item.item', $item)
                    ->orLike('tbl_m_item.kode', $item)
                    ->orLike('tbl_m_item.item_alias', $item)
                    ->groupEnd();
            }

            $hargaBeli = $this->request->getVar('harga_beli');
            if ($hargaBeli) {
                $hargaBeli = format_angka_db($hargaBeli);
                $query->where('tbl_m_item.harga_beli', $hargaBeli);
            }

            $selectedStatus = $this->request->getVar('status');
            if ($selectedStatus !== null && $selectedStatus !== '') {
                $query->where('tbl_m_item.status', $selectedStatus);
            }

            $items = $query->orderBy('tbl_m_item.item', 'ASC')->findAll();

            // Add data rows
            $row = 2;
            foreach ($items as $i => $item) {
                $sheet->setCellValue('A' . $row, $i + 1);
                $sheet->setCellValue('B' . $row, $item->kode);
                $sheet->setCellValue('C' . $row, $item->item);
                $sheet->setCellValue('D' . $row, $item->item_alias);
                $sheet->setCellValue('E' . $row, $item->item_kand);
                $sheet->setCellValue('F' . $row, $item->kategori);
                $sheet->setCellValue('G' . $row, $item->merk);
                $sheet->setCellValue('H' . $row, $item->satuanBesar);
                $sheet->setCellValue('I' . $row, $item->harga_beli);
                $sheet->setCellValue('J' . $row, $item->harga_jual);
                $sheet->setCellValue('K' . $row, $item->jml);
                $sheet->setCellValue('L' . $row, $item->status_stok);
                $sheet->setCellValue('M' . $row, $item->status_racikan);
                $sheet->setCellValue('N' . $row, $item->status);

                // Format currency cells
                $sheet->getStyle('I' . $row)->getNumberFormat()->setFormatCode('#,##0');
                $sheet->getStyle('J' . $row)->getNumberFormat()->setFormatCode('#,##0');

                $row++;
            }

            // Style the data rows
            $dataStyle = [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    ],
                ],
            ];
            $sheet->getStyle('A2:N' . ($row - 1))->applyFromArray($dataStyle);

            // Auto-size columns
            foreach (range('A', 'N') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            // Create your filename
            $filename = 'Data_Obat_' . date('Y-m-d_H-i-s') . '.xlsx';

            // Redirect output to client browser
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $filename . '"');
            header('Cache-Control: max-age=0');

            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
            exit();

        } catch (\Exception $e) {
            log_message('error', '[Obat::xls_items] ' . $e->getMessage());
            return redirect()->back()
                ->with('error', ENVIRONMENT === 'development' ? $e->getMessage() : 'Gagal mengekspor data obat');
        }
    }
} 