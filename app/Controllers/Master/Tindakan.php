<?php
/**
 * Tindakan Controller
 * 
 * Controller for managing medical procedures/treatments (tindakan)
 * Handles CRUD operations and other related functionalities
 * 
 * @author    Mikhael Felian Waskito <mikhaelfelian@gmail.com>
 * @date      2025-01-12
 */

namespace App\Controllers\Master;

use App\Controllers\BaseController;
use App\Models\ItemModel;
use App\Models\ItemRefModel;
use App\Models\KategoriModel;
use App\Models\SatuanModel;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Tindakan extends BaseController
{
    protected $itemModel;
    protected $kategoriModel;
    protected $itemRefModel;
    protected $validation;

    public function __construct()
    {
        $this->itemModel        = new ItemModel();
        $this->itemRefModel     = new ItemRefModel();
        $this->kategoriModel    = new KategoriModel();
        $this->satuanModel      = new SatuanModel();
        $this->validation       = \Config\Services::validation();
    }

    public function index()
    {
        try {
            $currentPage = $this->request->getVar('page') ?? 1;
            $perPage = $this->pengaturan->pagination_limit ?? 10;

            // Get items with pagination
            $query = $this->itemModel
                ->select('tbl_m_item.*, tbl_m_satuan.satuanBesar as satuan')
                ->join('tbl_m_satuan', 'tbl_m_satuan.id = tbl_m_item.id_satuan', 'left')
                ->where('tbl_m_item.status_hps', '0')
                ->where('tbl_m_item.status_item', '2') // For tindakan only
                ->orderBy('tbl_m_item.kode', 'ASC');

            $data = [
                'title'       => 'Data Tindakan',
                'Pengaturan'  => $this->pengaturan,
                'user'        => $this->ionAuth->user()->row(),
                'tindakans'   => $query->paginate($perPage),
                'pager'       => $this->itemModel->pager,
                'currentPage' => $currentPage,
                'perPage'     => $perPage,
                'breadcrumbs' => '
                    <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>
                    <li class="breadcrumb-item">Master</li>
                    <li class="breadcrumb-item active">Data Tindakan</li>
                '
            ];

            return view($this->theme->getThemePath() . '/master/tindakan/index', $data);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function create()
    {
        // Get kategori data
        $kategoris = $this->kategoriModel->where('status', '1')->findAll();

        $data = [
            'title'         => 'Tambah Tindakan',
            'Pengaturan'    => $this->pengaturan, 
            'kategoris'     => $kategoris,
            'user'          => $this->ionAuth->user()->row(),
            'validation'    => $this->validation,
            'breadcrumbs'   => '
                <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>
                <li class="breadcrumb-item">Master</li>
                <li class="breadcrumb-item"><a href="' . base_url('master/tindakan') . '">Tindakan</a></li>
                <li class="breadcrumb-item active">Tambah</li>
            '
        ];

        return view($this->theme->getThemePath() . '/master/tindakan/create', $data);
    }

    public function store()
    {
        // Validation rules
        $rules = [
            'item' => [
                'rules' => 'required|max_length[160]',
                'errors' => [
                    'required' => 'Nama tindakan harus diisi',
                    'max_length' => 'Nama tindakan maksimal 160 karakter'
                ]
            ],
            'harga_jual' => [
                'rules' => 'required|numeric|greater_than_equal_to[0]',
                'errors' => [
                    'required' => 'Harga jual harus diisi',
                    'numeric' => 'Harga jual harus berupa angka',
                    'greater_than_equal_to' => 'Harga jual tidak boleh negatif'
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

        // Start transaction
        $this->db->transStart();

        try {
            $data = [
                'id_kategori' => $this->request->getVar(index: 'kategori'),
                'kode'        => $this->itemModel->generateKode(2),
                'item'        => $this->request->getPost('item'),
                'item_kand'   => $this->request->getPost('item_kand'),
                'harga_jual'  => format_angka_db($this->request->getPost('harga_jual')),
                'status'      => $this->request->getPost('status'),
                'status_item' => 2, // TINDAKAN
                'id_user'     => $this->ionAuth->user()->row()->id
            ];

            // Always save remun_tipe even if empty
            $data['remun_tipe'] = (string)$this->request->getVar('remun_tipe');

            // If remun_tipe is percentage (1), calculate nominal amount from percentage
            if ($this->request->getPost('remun_tipe') == '1') {
                $hargaJual = format_angka_db($this->request->getPost('harga_jual'));
                $remunPerc = format_angka_db($this->request->getPost('remun_perc'));
                
                $data['remun_perc'] = $this->request->getPost('remun_perc') ? format_angka_db($this->request->getPost('remun_perc')) : 0;
                $data['remun_nom'] = round(($hargaJual * $remunPerc) / 100);
            } else if ($this->request->getPost('remun_tipe') == '2') {
                $hargaJual = format_angka_db($this->request->getPost('harga_jual'));
                $remunNom = format_angka_db($this->request->getPost('remun_nom'));
                
                $data['remun_nom'] = $this->request->getPost('remun_nom') ? format_angka_db($this->request->getPost('remun_nom')) : 0;
                $data['remun_perc'] = $hargaJual > 0 ? min(round(($remunNom * 100) / $hargaJual), 100) : 0;
            }

            // Always save apres_tipe even if empty
            $data['apres_tipe'] = (string)$this->request->getPost('apres_tipe');
            
            // If apres_tipe is percentage (1), calculate nominal amount from percentage
            if ($this->request->getPost('apres_tipe') == '1') {
                $hargaJual = format_angka_db($this->request->getPost('harga_jual'));
                $apresPerc = format_angka_db($this->request->getPost('apres_perc'));
                
                $data['apres_perc'] = $this->request->getPost('apres_perc') ? format_angka_db($this->request->getPost('apres_perc')) : 0;
                $data['apres_nom']  = round(($hargaJual * $apresPerc) / 100);
            } else if ($this->request->getPost('apres_tipe') == '2') {
                $hargaJual = format_angka_db($this->request->getPost('harga_jual')); 
                $apresNom = format_angka_db($this->request->getPost('apres_nom'));
                
                $data['apres_nom'] = $this->request->getPost('apres_nom') ? format_angka_db($this->request->getPost('apres_nom')) : 0;
                $data['apres_perc'] = $hargaJual > 0 ? min(round(($apresNom * 100) / $hargaJual), 100) : 0;
            }

            if (!$this->itemModel->insert($data)) {
                throw new \Exception('Gagal menyimpan data tindakan');
            }

            $this->db->transCommit();

            return redirect()->to(base_url('master/tindakan'))
                ->with('success', 'Berhasil menyimpan data tindakan');

        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', '[Tindakan::store] ' . $e->getMessage());
            
            return redirect()->back()
                ->withInput()
                ->with('error', ENVIRONMENT === 'development' ? $e->getMessage() : 'Gagal menyimpan data tindakan');
        }
    }

    public function edit($id = null)
    {
        if (!$id) {
            return redirect()->to(base_url('master/tindakan'))
                ->with('error', 'ID tindakan tidak ditemukan');
        }

        $kategoris = $this->kategoriModel->where('status', '1')->findAll();

        $tindakan = $this->itemModel->find($id);
        if (!$tindakan || $tindakan->status_item != 2) {
            return redirect()->to(base_url('master/tindakan'))
                ->with('error', 'Data tindakan tidak ditemukan');
        }

        // Get item references
        $item_refs = $this->itemRefModel->getRefsByItem($id);

        $data = [
            'title'         => 'Edit Tindakan',
            'Pengaturan'    => $this->pengaturan,
            'kategoris'     => $kategoris,
            'user'          => $this->ionAuth->user()->row(),
            'validation'    => $this->validation,
            'tindakan'      => $tindakan,
            'item_refs'     => $item_refs,
            'breadcrumbs'   => '
                <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>
                <li class="breadcrumb-item">Master</li>
                <li class="breadcrumb-item"><a href="' . base_url('master/tindakan') . '">Tindakan</a></li>
                <li class="breadcrumb-item active">Edit</li>
            '
        ];

        return view($this->theme->getThemePath() . '/master/tindakan/edit', $data);
    }

    public function update($id = null)
    {
        if (!$id) {
            return redirect()->to(base_url('master/tindakan'))
                ->with('error', 'ID tindakan tidak ditemukan');
        }

        // Validation rules
        $rules = [
            'item' => [
                'rules' => 'required|max_length[160]',
                'errors' => [
                    'required' => 'Nama tindakan harus diisi',
                    'max_length' => 'Nama tindakan maksimal 160 karakter'
                ]
            ],
            'harga_jual' => [
                'rules' => 'required|numeric|greater_than_equal_to[0]',
                'errors' => [
                    'required' => 'Harga jual harus diisi',
                    'numeric' => 'Harga jual harus berupa angka',
                    'greater_than_equal_to' => 'Harga jual tidak boleh negatif'
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

        // Start transaction
        $this->db->transStart();

        try {
            $data = [
                'id_kategori' => $this->request->getVar('kategori'),
                'item'        => $this->request->getPost('item'),
                'harga_jual'  => format_angka_db($this->request->getPost('harga_jual')), 
                'status'      => $this->request->getPost('status'),
                'id_user'     => $this->ionAuth->user()->row()->id
            ];
            
            // Always save remun_tipe even if empty
            $data['remun_tipe'] = (string)$this->request->getVar('remun_tipe');

            // If remun_tipe is percentage (1), calculate nominal amount from percentage
            if ($this->request->getPost('remun_tipe') == '1') {
                $hargaJual = format_angka_db($this->request->getPost('harga_jual'));
                $remunPerc = format_angka_db($this->request->getPost('remun_perc'));
                
                $data['remun_perc'] = $this->request->getPost('remun_perc') ? format_angka_db($this->request->getPost('remun_perc')) : 0;
                $data['remun_nom'] = round(($hargaJual * $remunPerc) / 100);
            } else if ($this->request->getPost('remun_tipe') == '2') {
                $hargaJual = format_angka_db($this->request->getPost('harga_jual'));
                $remunNom = format_angka_db($this->request->getPost('remun_nom'));
                
                $data['remun_nom'] = $this->request->getPost('remun_nom') ? format_angka_db($this->request->getPost('remun_nom')) : 0;
                $data['remun_perc'] = $hargaJual > 0 ? min(round(($remunNom * 100) / $hargaJual), 100) : 0;
            }

            // Always save apres_tipe even if empty
            $data['apres_tipe'] = (string)$this->request->getPost('apres_tipe');
            
            // If apres_tipe is percentage (1), calculate nominal amount from percentage
            if ($this->request->getPost('apres_tipe') == '1') {
                $hargaJual = format_angka_db($this->request->getPost('harga_jual'));
                $apresPerc = format_angka_db($this->request->getPost('apres_perc'));
                
                $data['apres_perc'] = $this->request->getPost('apres_perc') ? format_angka_db($this->request->getPost('apres_perc')) : 0;
                $data['apres_nom']  = round(($hargaJual * $apresPerc) / 100);
            } else if ($this->request->getPost('apres_tipe') == '2') {
                $hargaJual = format_angka_db($this->request->getPost('harga_jual')); 
                $apresNom = format_angka_db($this->request->getPost('apres_nom'));
                
                $data['apres_nom'] = $this->request->getPost('apres_nom') ? format_angka_db($this->request->getPost('apres_nom')) : 0;
                $data['apres_perc'] = $hargaJual > 0 ? min(round(($apresNom * 100) / $hargaJual), 100) : 0;
            }

            if (!$this->itemModel->update($id, $data)) {
                throw new \Exception('Gagal mengupdate data tindakan');
            }

            $this->db->transCommit();

            return redirect()->to(base_url('master/tindakan'))
                ->with('success', 'Berhasil mengupdate data tindakan');

        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', '[Tindakan::update] ' . $e->getMessage());
            
            return redirect()->back()
                ->withInput()
                ->with('error', ENVIRONMENT === 'development' ? $e->getMessage() : 'Gagal mengupdate data tindakan');
        }
    }

    public function delete($id = null)
    {
        if (!$id) {
            return redirect()->to(base_url('master/tindakan'))
                ->with('error', 'ID tindakan tidak ditemukan');
        }

        try {
            $tindakan = $this->itemModel->find($id);
            if (!$tindakan || $tindakan->status_item != 2) {
                throw new \Exception('Data tindakan tidak ditemukan');
            }

            if (!$this->itemModel->delete($id)) {
                throw new \Exception('Gagal menghapus data tindakan');
            }

            return redirect()->to(base_url('master/tindakan'))
                ->with('success', 'Berhasil menghapus data tindakan');

        } catch (\Exception $e) {
            log_message('error', '[Tindakan::delete] ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', ENVIRONMENT === 'development' ? $e->getMessage() : 'Gagal menghapus data tindakan');
        }
    }

    public function trash()
    {
        $currentPage = $this->request->getVar('page_tindakan') ?? 1;
        $perPage = 10;
        $offset = ($currentPage - 1) * $perPage;
        
        // Get the base query
        $builder = $this->itemModel->getTindakanTrash();

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
        $total = $builder->countAllResults(false);  // false to not reset the query builder

        // Get paginated results
        $tindakan = $builder->limit($perPage, $offset)->get()->getResult();

        // Create pager
        $pager = service('pager');
        $pager->setPath('master/tindakan/trash');
        $pager->makeLinks($currentPage, $perPage, $total, 'adminlte_pagination');

        $data = [
            'title'         => 'Data Sampah Tindakan',
            'Pengaturan'    => $this->pengaturan,
            'user'          => $this->ionAuth->user()->row(),
            'tindakan'      => $tindakan,
            'pager'         => $pager,
            'currentPage'   => $currentPage,
            'perPage'       => $perPage,
            'total'         => $total,
            'breadcrumbs'   => '
                <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>
                <li class="breadcrumb-item">Master</li>
                <li class="breadcrumb-item"><a href="' . base_url('master/tindakan') . '">Tindakan</a></li>
                <li class="breadcrumb-item active">Sampah</li>
            '
        ];

        return view($this->theme->getThemePath() . '/master/tindakan/trash', $data);
    }

    public function restore($id = null)
    {
        if (!$id) {
            return redirect()->to(base_url('master/tindakan/trash'))
                ->with('error', 'ID tindakan tidak ditemukan');
        }

        try {
            $data = [
                'status_hps' => '0',
                'deleted_at' => null,
                'updated_at' => date('Y-m-d H:i:s')
            ];

            if (!$this->itemModel->update($id, $data)) {
                throw new \Exception('Gagal memulihkan data tindakan');
            }

            return redirect()->to(base_url('master/tindakan/trash'))
                ->with('success', 'Berhasil memulihkan data tindakan');

        } catch (\Exception $e) {
            log_message('error', '[Tindakan::restore] ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', ENVIRONMENT === 'development' ? $e->getMessage() : 'Gagal memulihkan data tindakan');
        }
    }

    public function delete_permanent($id = null)
    {
        if (!$id) {
            return redirect()->to(base_url('master/tindakan/trash'))
                ->with('error', 'ID tindakan tidak ditemukan');
        }

        // Start transaction
        $this->db->transStart();

        try {
            // Permanently delete the item
            if (!$this->itemModel->delete($id, true)) {
                throw new \Exception('Gagal menghapus permanen data tindakan');
            }

            $this->db->transCommit();
            
            return redirect()->to(base_url('master/tindakan/trash'))
                ->with('success', 'Berhasil menghapus permanen data tindakan');

        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', '[Tindakan::delete_permanent] ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', ENVIRONMENT === 'development' ? $e->getMessage() : 'Gagal menghapus permanen data tindakan');
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
                ->setTitle('Data Tindakan')
                ->setSubject('Data Tindakan ' . $this->pengaturan->judul_app)
                ->setDescription('Data Tindakan ' . date('Y-m-d H:i:s'));

            // Add header row
            $sheet->setCellValue('A1', 'No');
            $sheet->setCellValue('B1', 'Kode');
            $sheet->setCellValue('C1', 'Nama Tindakan');
            $sheet->setCellValue('D1', 'Alias');
            $sheet->setCellValue('E1', 'Keterangan');
            $sheet->setCellValue('F1', 'Harga');
            $sheet->setCellValue('G1', 'Status');

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
            $sheet->getStyle('A1:G1')->applyFromArray($headerStyle);

            // Get data with filters
            $query = $this->itemModel->getTindakan();

            // Filter by name/code/alias
            $item = $this->request->getVar('item');
            if ($item) {
                $query->groupStart()
                    ->like('tbl_m_item.item', $item)
                    ->orLike('tbl_m_item.kode', $item)
                    ->orLike('tbl_m_item.item_alias', $item)
                    ->groupEnd();
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

            $items = $query->orderBy('tbl_m_item.item', 'ASC')->findAll();

            // Add data rows
            $row = 2;
            foreach ($items as $i => $item) {
                $sheet->setCellValue('A' . $row, $i + 1);
                $sheet->setCellValue('B' . $row, $item->kode);
                $sheet->setCellValue('C' . $row, $item->item);
                $sheet->setCellValue('D' . $row, $item->item_alias);
                $sheet->setCellValue('E' . $row, $item->item_kand);
                $sheet->setCellValue('F' . $row, $item->harga_jual);
                $sheet->setCellValue('G' . $row, $item->status == '1' ? 'Aktif' : 'Non-Aktif');

                // Format currency cells
                $sheet->getStyle('F' . $row)->getNumberFormat()->setFormatCode('#,##0');

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
            $sheet->getStyle('A2:G' . ($row - 1))->applyFromArray($dataStyle);

            // Auto-size columns
            foreach (range('A', 'G') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            // Create your filename
            $filename = 'Data_Tindakan_' . date('Y-m-d_H-i-s') . '.xlsx';

            // Redirect output to client browser
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $filename . '"');
            header('Cache-Control: max-age=0');

            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
            exit();

        } catch (\Exception $e) {
            log_message('error', '[Tindakan::xls_items] ' . $e->getMessage());
            return redirect()->back()
                ->with('error', ENVIRONMENT === 'development' ? $e->getMessage() : 'Gagal mengekspor data tindakan');
        }
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