<?php
/**
 * Created by:
 * Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * 2025-01-12
 * 
 * BHP Controller
 * 
 * Controller for managing Bahan Habis Pakai (BHP)
 * Handles CRUD operations and other related functionalities
 */

namespace App\Controllers\Master;

use App\Controllers\BaseController;
use App\Models\ItemModel;
use App\Models\ItemStokModel;
use App\Models\ItemRefModel;
use App\Models\KategoriModel;
use App\Models\SatuanModel;
use App\Models\MerkModel;
use App\Models\GudangModel;
use App\Models\PengaturanModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class BHP extends BaseController
{
    protected $itemModel;
    protected $kategoriModel;
    protected $itemRefModel;
    protected $validation;
    protected $merkModel;
    protected $pengaturan;

    public function __construct()
    {
        $this->itemModel        = new ItemModel();
        $this->itemStokModel    = new ItemStokModel();
        $this->itemRefModel     = new ItemRefModel();
        $this->kategoriModel    = new KategoriModel();
        $this->merkModel        = new MerkModel();
        $this->satuanModel      = new SatuanModel();
        $this->gudangModel      = new GudangModel();
        $this->pengaturan       = new PengaturanModel();
        $this->validation       = \Config\Services::validation();
    }

    public function index()
    {
        $currentPage = $this->request->getVar('page_bhp') ?? 1;
        $perPage = 10;
        $query = $this->itemModel->getBHP();

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

        // Filter by name/code/alias
        $item = $this->request->getVar('item');
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

        // Filter by harga_jual
        $hargaJual = $this->request->getVar('harga_jual');
        if ($hargaJual) {
            $hargaJual = format_angka_db($hargaJual);
            $query->where('tbl_m_item.harga_jual', $hargaJual);
        }

        // Filter by status
        $selectedStatus = $this->request->getVar('status');
        if ($selectedStatus !== null && $selectedStatus !== '') {
            $query->where('tbl_m_item.status', $selectedStatus);
        }

        $data = [
            'title'            => 'Data BHP',
            'Pengaturan'       => $this->pengaturan,
            'user'             => $this->ionAuth->user()->row(),
            'bhp'              => $query->paginate($perPage, 'bhp'),
            'pager'            => $this->itemModel->pager,
            'currentPage'      => $currentPage,
            'perPage'          => $perPage,
            'kategoriList'     => $kategoriList,
            'merkList'         => $merkList,
            'selectedKategori' => $selectedKategori,
            'selectedMerk'     => $selectedMerk,
            'selectedStatus'   => $selectedStatus,
            'trashCount'       => $this->itemModel->countDeleted(5),
            'breadcrumbs'      => '
                <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>
                <li class="breadcrumb-item">Master</li>
                <li class="breadcrumb-item active">BHP</li>
            '
        ];

        return view($this->theme->getThemePath() . '/master/bhp/index', $data);
    }

    public function create()
    {
        // Get active data for dropdowns
        $kategoris  = $this->kategoriModel->where('status', '1')->findAll();
        $merks      = $this->merkModel->where('status', '1')->findAll();
        $satuans    = $this->satuanModel->where('status', '1')->findAll();

        $data = [
            'title'         => 'Tambah BHP',
            'Pengaturan'    => $this->pengaturan, 
            'kategoris'     => $kategoris,
            'merks'         => $merks,
            'satuans'       => $satuans,
            'user'          => $this->ionAuth->user()->row(),
            'validation'    => $this->validation,
            'breadcrumbs'   => '
                <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>
                <li class="breadcrumb-item">Master</li>
                <li class="breadcrumb-item"><a href="' . base_url('master/bhp') . '">BHP</a></li>
                <li class="breadcrumb-item active">Tambah</li>
            '
        ];

        return view($this->theme->getThemePath() . '/master/bhp/create', $data);
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
            'kategori' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Kategori harus dipilih'
                ]
            ],
            'item' => [
                'rules' => 'required|max_length[160]',
                'errors' => [
                    'required'      => 'Nama BHP harus diisi',
                    'max_length'    => 'Nama BHP maksimal 160 karakter'
                ]
            ],
            'harga_jual' => [
                'rules' => 'required|numeric|greater_than_equal_to[0]',
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
                'kode'        => $this->itemModel->generateKode(5), // 5 for BHP
                'item'        => $this->request->getPost('item'),
                'item_kand'   => $this->request->getPost('item_kand'),
                'harga_beli'  => format_angka_db($this->request->getVar('harga_beli')),
                'harga_jual'  => format_angka_db($this->request->getVar('harga_jual')),
                'status'      => $this->request->getPost('status'),
                'status_stok' => $this->request->getPost('status_stok'),
                'status_item' => 5, // BHP
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
                
                return redirect()->to(base_url('master/bhp'))
                    ->with('success', 'Data BHP berhasil ditambahkan');
            }

            return redirect()->back()
                ->with('error', 'Gagal menambahkan data BHP')
                ->withInput();

        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', '[BHP::store] ' . $e->getMessage());
            
            return redirect()->back()
                ->withInput()
                ->with('error', ENVIRONMENT === 'development' ? $e->getMessage() : 'Gagal menyimpan data BHP');
        }
    }

    public function edit($id = null)
    {
        if (!$id) {
            return redirect()->back()
                ->with('error', 'ID BHP tidak ditemukan');
        }

        $bhp = $this->itemModel->getItemWithRelations($id);
        if (!$bhp) {
            return redirect()->back()
                ->with('error', 'Data BHP tidak ditemukan');
        }

        // Get active data for dropdowns
        $kategoris  = $this->kategoriModel->where('status', '1')->findAll();
        $merks      = $this->merkModel->where('status', '1')->findAll();
        $satuans    = $this->satuanModel->where('status', '1')->findAll();

        // Get item references
        $item_refs = $this->itemRefModel->getRefsByItem($id);

        $data = [
            'title'         => 'Edit BHP',
            'Pengaturan'    => $this->pengaturan,
            'bhp'           => $bhp,
            'kategoris'     => $kategoris,
            'merks'         => $merks,
            'satuans'       => $satuans,
            'item_refs'     => $item_refs,
            'user'          => $this->ionAuth->user()->row(),
            'validation'    => $this->validation,
            'breadcrumbs'   => '
                <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>
                <li class="breadcrumb-item">Master</li>
                <li class="breadcrumb-item"><a href="' . base_url('master/bhp') . '">BHP</a></li>
                <li class="breadcrumb-item active">Edit</li>
            '
        ];

        return view($this->theme->getThemePath() . '/master/bhp/edit', $data);
    }

    public function update($id = null)
    {
        if (!$id) {
            return redirect()->back()
                ->with('error', 'ID BHP tidak ditemukan');
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
                    'required'      => 'Nama BHP harus diisi',
                    'max_length'    => 'Nama BHP maksimal 160 karakter'
                ]
            ],
            'harga_jual' => [
                'rules' => 'required|numeric|greater_than_equal_to[0]',
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
                'status_stok' => $this->request->getPost('status_stok') ?? '0',
                'id_user'     => $this->ionAuth->user()->row()->id
            ];

            if (!$this->itemModel->update($id, $data)) {
                throw new \Exception('Gagal mengupdate data BHP');
            }

            $this->db->transCommit();
            
            return redirect()->to(base_url('master/bhp'))
                ->with('success', 'Data BHP berhasil diupdate');

        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', '[BHP::update] ' . $e->getMessage());
            
            return redirect()->back()
                ->withInput()
                ->with('error', ENVIRONMENT === 'development' ? $e->getMessage() : 'Gagal mengupdate data BHP');
        }
    }

    public function trash()
    {
        $currentPage = $this->request->getVar('page_bhp') ?? 1;
        $perPage = 10;

        // Get builder instance
        $builder = $this->db->table('tbl_m_item')
            ->select('
                tbl_m_item.id,
                tbl_m_item.kode,
                tbl_m_item.item,
                tbl_m_item.item_alias,
                tbl_m_item.item_kand,
                tbl_m_item.harga_jual,
                tbl_m_item.status,
                tbl_m_item.status_item,
                tbl_m_item.deleted_at
            ')
            ->where('tbl_m_item.status_item', 5)  // 5 for BHP
            ->where('tbl_m_item.status_hps', '1');

        // Filter by name/code/alias
        $item = $this->request->getVar('item');
        if ($item) {
            $builder->groupStart()
                ->like('tbl_m_item.item', $item)
                ->orLike('tbl_m_item.kode', $item)
                ->orLike('tbl_m_item.item_alias', $item)
                ->groupEnd();
        }

        $data = [
            'title'         => 'Sampah BHP',
            'Pengaturan'    => $this->pengaturan,
            'user'          => $this->ionAuth->user()->row(),
            'bhp'           => $builder->get()->getResult(),
            'pager'         => null,
            'currentPage'   => $currentPage,
            'perPage'       => $perPage,
            'breadcrumbs'   => '
                <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>
                <li class="breadcrumb-item">Master</li>
                <li class="breadcrumb-item"><a href="' . base_url('master/bhp') . '">BHP</a></li>
                <li class="breadcrumb-item active">Sampah</li>
            '
        ];

        return view($this->theme->getThemePath() . '/master/bhp/trash', $data);
    }

    public function delete($id = null)
    {
        if (!$id) {
            return redirect()->back()
                ->with('error', 'ID BHP tidak ditemukan');
        }

        try {
            $bhp = $this->itemModel->find($id);
            if (!$bhp) {
                throw new \Exception('Data BHP tidak ditemukan');
            }

            // Soft delete by updating status_hps
            if (!$this->itemModel->update($id, ['status_hps' => '1'])) {
                throw new \Exception('Gagal menghapus data BHP');
            }

            return redirect()->back()
                ->with('success', 'Data BHP berhasil dihapus');

        } catch (\Exception $e) {
            log_message('error', '[BHP::delete] ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', ENVIRONMENT === 'development' ? $e->getMessage() : 'Gagal menghapus data BHP');
        }
    }

    public function restore($id = null)
    {
        if (!$id) {
            return redirect()->back()
                ->with('error', 'ID BHP tidak ditemukan');
        }

        try {
            // Find the BHP with status_hps = 1
            $bhp = $this->itemModel->where([
                'id' => $id,
                'status_item' => 5,  // 5 for BHP
                'status_hps' => '1'
            ])->first();

            if (!$bhp) {
                throw new \Exception('Data BHP tidak ditemukan');
            }

            // Restore by updating status_hps
            if (!$this->itemModel->update($id, ['status_hps' => '0'])) {
                throw new \Exception('Gagal memulihkan data BHP');
            }

            return redirect()->back()
                ->with('success', 'Data BHP berhasil dipulihkan');

        } catch (\Exception $e) {
            log_message('error', '[BHP::restore] ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', ENVIRONMENT === 'development' ? $e->getMessage() : 'Gagal memulihkan data BHP');
        }
    }

    public function delete_permanent($id = null)
    {
        if (!$id) {
            return redirect()->back()
                ->with('error', 'ID BHP tidak ditemukan');
        }

        // Start transaction
        $this->db->transStart();

        try {
            // Find the BHP with status_hps = 1
            $bhp = $this->itemModel->where([
                'id' => $id,
                'status_item' => 5,  // 5 for BHP
                'status_hps' => '1'
            ])->first();

            if (!$bhp) {
                throw new \Exception('Data BHP tidak ditemukan');
            }

            // Delete related records first
            $this->itemStokModel->where('id_item', $id)->delete();
            $this->itemRefModel->where('id_item', $id)->delete();

            // Delete the BHP record permanently
            if (!$this->itemModel->delete($id)) {
                throw new \Exception('Gagal menghapus permanen data BHP');
            }

            $this->db->transCommit();
            
            return redirect()->back()
                ->with('success', 'Data BHP berhasil dihapus permanen');

        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', '[BHP::delete_permanent] ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', ENVIRONMENT === 'development' ? $e->getMessage() : 'Gagal menghapus permanen data BHP');
        }
    }

    public function xls_items()
    {
        $query = $this->itemModel->getBHP();

        // Apply filters
        $item = $this->request->getVar('item');
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

        // Filter by harga_jual
        $hargaJual = $this->request->getVar('harga_jual');
        if ($hargaJual) {
            $hargaJual = format_angka_db($hargaJual);
            $query->where('tbl_m_item.harga_jual', $hargaJual);
        }

        // Filter by status
        $selectedStatus = $this->request->getVar('status');
        if ($selectedStatus !== null && $selectedStatus !== '') {
            $query->where('tbl_m_item.status', $selectedStatus);
        }

        $items = $query->get()->getResult();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set column headers
        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Kode');
        $sheet->setCellValue('C1', 'Kategori');
        $sheet->setCellValue('D1', 'Merk');
        $sheet->setCellValue('E1', 'Nama BHP');
        $sheet->setCellValue('F1', 'Alias');
        $sheet->setCellValue('G1', 'Keterangan');
        $sheet->setCellValue('H1', 'Harga Jual');
        $sheet->setCellValue('I1', 'Status');

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
        ];
        $sheet->getStyle('A1:I1')->applyFromArray($headerStyle);

        // Auto size columns
        foreach (range('A', 'I') as $col) {
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
            $sheet->setCellValue('H' . $row, format_angka_rp($item->harga_jual));
            $sheet->setCellValue('I' . $row, $item->status == '1' ? 'Aktif' : 'Non-Aktif');

            // Style for data rows
            $rowStyle = [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    ],
                ],
            ];
            $sheet->getStyle('A' . $row . ':I' . $row)->applyFromArray($rowStyle);

            $row++;
        }

        // Create Excel file
        $writer = new Xlsx($spreadsheet);
        $filename = 'data_bhp_' . date('Y-m-d_His') . '.xlsx';

        // Set headers for download
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        // Save file to PHP output
        $writer->save('php://output');
        exit();
    }
    

    public function item_ref_save($id_item = null)
    {
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

    public function item_ref_delete($id = null)
    {
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
} 