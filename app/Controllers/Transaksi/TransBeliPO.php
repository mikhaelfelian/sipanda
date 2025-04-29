<?php
/**
 * Created by:
 * Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * 2025-01-29
 * 
 * TransBeliPO Controller
 * 
 * Controller for managing Purchase Orders
 */

namespace App\Controllers\Transaksi;

use App\Controllers\BaseController;
use App\Models\TransBeliPOModel;
use App\Models\TransBeliPODetModel;
use App\Models\ItemModel;
use App\Models\SupplierModel;
use App\Models\PengaturanModel;
use App\Models\SatuanModel;
use FPDF;

class TransBeliPO extends BaseController
{
    protected $transBeliPOModel;
    protected $transBeliPODetModel;
    protected $itemModel;
    protected $supplierModel;
    protected $pengaturanModel;
    protected $satuanModel;
    protected $db;
    protected $validation;

    public function __construct()
    {
        $this->transBeliPOModel = new TransBeliPOModel();
        $this->transBeliPODetModel = new TransBeliPODetModel();
        $this->itemModel = new ItemModel();
        $this->supplierModel = new SupplierModel();
        $this->pengaturanModel = new PengaturanModel();
        $this->satuanModel = new SatuanModel();
        $this->db = \Config\Database::connect();
        $this->validation = \Config\Services::validation();
    }

    /**
     * Display list of purchase orders
     */
    public function index()
    {
        $filters = [
            'supplier' => $this->request->getGet('supplier'),
            'status'   => $this->request->getGet('status'),
            'q'        => $this->request->getGet('q')
        ];

        $data = [
            'title'         => 'Data Purchase Order',
            'Pengaturan'    => $this->pengaturan,
            'user'          => $this->ionAuth->user()->row(),
            'po_list'       => $this->transBeliPOModel->getWithRelations($filters)->paginate(10, 'po'),
            'pager'         => $this->transBeliPOModel->pager,
            'suppliers'     => $this->supplierModel->where('status_hps', '0')->findAll(),
            'filters'       => $filters,
            'validation'    => $this->validation,
            'request'       => $this->request,
            'transBeliPOModel' => $this->transBeliPOModel
        ];

        return $this->view($this->theme->getThemePath() . '/transaksi/po/index', $data);
    }

    /**
     * Display purchase order creation form
     */
    public function create()
    {
        $data = [
            'title'      => 'Buat Purchase Order',
            'Pengaturan' => $this->pengaturan,
            'user'       => $this->ionAuth->user()->row(),
            'suppliers'  => $this->supplierModel->where('status_hps', '0')->findAll(),
            'validation' => $this->validation,
            'po_number'  => $this->transBeliPOModel->generateNoNota()
        ];

        return $this->view($this->theme->getThemePath() . '/transaksi/po/trans_po', $data);
    }

    /**
     * Store new purchase order
     */
    public function store()
    {
        // Validation rules
        $rules = [
            'supplier_id' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Supplier harus dipilih'
                ]
            ],
            'tgl_po' => [
                'rules' => 'required|valid_date',
                'errors' => [
                    'required' => 'Tanggal PO harus diisi',
                    'valid_date' => 'Format tanggal tidak valid'
                ]
            ],
            'alamat_pengiriman' => [
                'rules' => 'required|max_length[160]',
                'errors' => [
                    'required' => 'Alamat pengiriman harus diisi',
                    'max_length' => 'Alamat pengiriman maksimal 160 karakter'
                ]
            ]
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                            ->withInput()
                            ->with('errors', $this->validation->getErrors());
        }

        try {
            $supplier = $this->supplierModel->find($this->request->getPost('supplier_id'));
            if (!$supplier) {
                throw new \RuntimeException('Supplier tidak ditemukan');
            }

            $data = [
                'id_supplier'   => $supplier->id,
                'id_user'       => $this->ionAuth->user()->row()->id,
                'tgl_masuk'     => $this->request->getPost('tgl_po'),
                'no_nota'       => $this->transBeliPOModel->generateNoNota(),
                'supplier'      => $supplier->nama,
                'keterangan'    => $this->request->getPost('keterangan'),
                'pengiriman'    => $this->request->getPost('alamat_pengiriman'),
                'status'        => 0 // Draft status
            ];

            $this->db->transStart();

            if (!$this->transBeliPOModel->insert($data)) {
                throw new \RuntimeException('Gagal menyimpan PO');
            }

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw new \RuntimeException('Gagal menyimpan PO');
            }

            return redirect()->to('transaksi/po')
                            ->with('success', 'Purchase Order berhasil dibuat');

        } catch (\Exception $e) {
            log_message('error', '[TransBeliPO::store] ' . $e->getMessage());
            return redirect()->back()
                            ->withInput()
                            ->with('error', 'Gagal membuat Purchase Order: ' . $e->getMessage());
        }
    }

    /**
     * Display purchase order edit form
     */
    public function edit($id = null)
    {
        // try {
            // Get PO data
            $po = $this->transBeliPOModel->find($id);
            if (!$po) {
                throw new \RuntimeException('Data PO tidak ditemukan');
            }

            // Get PO items
            $items = $this->transBeliPODetModel->where('id_pembelian', $id)->findAll();

            $data = [
                'title'      => 'Edit Purchase Order',
                'Pengaturan' => $this->pengaturan,
                'user'       => $this->ionAuth->user()->row(),
                'po'         => $po,
                'po_details' => $items,
                'suppliers'  => $this->supplierModel->where('status_hps', '0')->findAll(),
                'satuans'    => $this->satuanModel->findAll(),
                'validation' => $this->validation
            ];

            return $this->view($this->theme->getThemePath() . '/transaksi/po/trans_po_edit', $data);

        // } catch (\Exception $e) {
        //     log_message('error', '[TransBeliPO::edit] ' . $e->getMessage());
        //     return redirect()->back()
        //                    ->with('error', 'Gagal memuat form edit PO');
        // }
    }

    /**
     * Update purchase order
     */
    public function update($id)
    {
        // Validation rules
        $rules = [
            'supplier_id' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Supplier harus dipilih'
                ]
            ],
            'tgl_po' => [
                'rules' => 'required|valid_date',
                'errors' => [
                    'required' => 'Tanggal PO harus diisi',
                    'valid_date' => 'Format tanggal tidak valid'
                ]
            ],
            'alamat_pengiriman' => [
                'rules' => 'required|max_length[160]',
                'errors' => [
                    'required' => 'Alamat pengiriman harus diisi',
                    'max_length' => 'Alamat pengiriman maksimal 160 karakter'
                ]
            ]
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                            ->withInput()
                            ->with('errors', $this->validation->getErrors());
        }

        try {
            $po = $this->transBeliPOModel->find($id);
            if (!$po) {
                throw new \RuntimeException('Data PO tidak ditemukan');
            }

            // Only allow editing draft POs
            if ($po->status != 0) {
                throw new \RuntimeException('Hanya PO dengan status draft yang dapat diedit');
            }

            $supplier = $this->supplierModel->find($this->request->getPost('supplier_id'));
            if (!$supplier) {
                throw new \RuntimeException('Supplier tidak ditemukan');
            }

            $data = [
                'id_supplier'   => $supplier->id,
                'tgl_masuk'     => $this->request->getPost('tgl_po'),
                'supplier'      => $supplier->nama,
                'keterangan'    => $this->request->getPost('keterangan'),
                'pengiriman'    => $this->request->getPost('alamat_pengiriman')
            ];

            $this->db->transStart();

            if (!$this->transBeliPOModel->update($id, $data)) {
                throw new \RuntimeException('Gagal mengupdate PO');
            }

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw new \RuntimeException('Gagal mengupdate PO');
            }

            return redirect()->to('transaksi/po')
                            ->with('success', 'Purchase Order berhasil diupdate');

        } catch (\Exception $e) {
            log_message('error', '[TransBeliPO::update] ' . $e->getMessage());
            return redirect()->back()
                            ->withInput()
                            ->with('error', 'Gagal mengupdate Purchase Order: ' . $e->getMessage());
        }
    }

    /**
     * Display purchase order details
     */
    public function detail($id)
    {
        try {
            $po = $this->transBeliPOModel->getWithRelations(['tbl_trans_beli_po.id' => $id]);
            if (!$po) {
                throw new \RuntimeException('Data PO tidak ditemukan');
            }

            // Get PO items
            $items = $this->transBeliPODetModel->getWithRelations($id);

            $data = [
                'title'         => 'Detail Purchase Order',
                'Pengaturan'    => $this->pengaturan,
                'user'          => $this->ionAuth->user()->row(),
                'po'            => $po,
                'items'         => $items
            ];

            return $this->view($this->theme->getThemePath() . '/transaksi/po/trans_po_detail', $data);

        } catch (\Exception $e) {
            log_message('error', '[TransBeliPO::detail] ' . $e->getMessage());
            return redirect()->back()
                           ->with('error', 'Gagal memuat detail PO');
        }
    }

    /**
     * Add item to PO cart
     * 
     * @param int $po_id The PO ID
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function cart_add($po_id = null)
    {
        try {
            // Validate PO ID
            if (!$po_id) {
                throw new \RuntimeException('ID PO tidak valid');
            }

            // Check if PO exists and is editable
            $po = $this->transBeliPOModel->find($po_id);
            if (!$po) {
                throw new \RuntimeException('Data PO tidak ditemukan');
            }
            if ($po->status != 0) {
                throw new \RuntimeException('PO tidak dapat diubah');
            }

            // Validate input
            $rules = [
                'id_item' => [
                    'label' => 'Item',
                    'rules' => 'required',
                    'errors' => [
                        'required' => '{field} harus dipilih'
                    ]
                ],
                'jumlah' => [
                    'label' => 'Jumlah',
                    'rules' => 'required|numeric|greater_than[0]',
                    'errors' => [
                        'required' => '{field} harus diisi',
                        'numeric' => '{field} harus berupa angka',
                        'greater_than' => '{field} harus lebih dari 0'
                    ]
                ],
                'satuan' => [
                    'label' => 'Satuan',
                    'rules' => 'required',
                    'errors' => [
                        'required' => '{field} harus dipilih'
                    ]
                ]
            ];

            if (!$this->validate($rules)) {
                return redirect()->back()
                               ->withInput()
                               ->with('errors', $this->validator->getErrors());
            }

            // Get validated data
            $item_id    = $this->request->getPost('id_item');
            $jumlah     = $this->request->getPost('jumlah');
            $satuan     = $this->request->getPost('satuan');
            $keterangan = $this->request->getPost('keterangan');

            // Check if item exists
            $item = $this->itemModel->find($item_id);
            if (!$item) {
                throw new \RuntimeException('Item tidak ditemukan');
            }

            $satuan = $this->satuanModel->find($satuan);
            if (!$satuan) {
                throw new \RuntimeException('Satuan tidak ditemukan');
            }

            // Begin transaction
            $this->db->transBegin();

            // Add item to PO detail
            $data = [
                'id_pembelian' => $po_id,
                'id_item'      => $item_id,
                'id_satuan'    => $satuan->id,
                'id_user'      => $this->ionAuth->user()->row()->id,
                'tgl_masuk'    => $po->tgl_masuk,
                'kode'         => $item->kode,
                'item'         => $item->item,
                'jml'          => (int)$jumlah,
                'jml_satuan'   => (int)$satuan->jml,
                'satuan'       => $satuan->satuanBesar,
                'keterangan'   => $keterangan
            ];

            if (!$this->transBeliPODetModel->insert($data)) {
                throw new \RuntimeException('Gagal menambahkan item');
            }

            // Commit transaction
            if ($this->db->transStatus() === false) {
                $this->db->transRollback();
                throw new \RuntimeException('Gagal menambahkan item');
            }
            $this->db->transCommit();

            return redirect()->back()
                           ->with('success', 'Item berhasil ditambahkan');
        } catch (\Exception $e) {
            // Log error
            log_message('error', '[TransBeliPO::cart_add] ' . $e->getMessage());
            
            // Rollback transaction if active
            if ($this->db->transStatus() === false) {
                $this->db->transRollback();
            }

            return redirect()->back()
                           ->withInput()
                           ->with('error', ENVIRONMENT === 'development' ? $e->getMessage() : 'Gagal menambahkan item');
        }
    }

    /**
     * Delete item from PO cart permanently
     * 
     * @param int $id ID of PO detail item to delete
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function cart_delete($id)
    {
        try {
            // Get the PO ID from query string
            $po_id = $this->request->getGet('id');
            
            if (!$po_id) {
                throw new \Exception('ID PO tidak ditemukan');
            }

            // Delete the item permanently
            $this->transBeliPODetModel->delete($id);

            return redirect()->to("transaksi/po/edit/$po_id")
                           ->with('success', 'Item berhasil dihapus !!');
                           
        } catch (\Exception $e) {
            return redirect()->back()
                           ->with('error', 'Gagal menghapus item: ' . $e->getMessage());
        }
    }

    /**
     * Delete PO
     */
    public function delete($id = null)
    {
        if (!$id) {
            return redirect()->to('transaksi/po')
                           ->with('error', 'ID PO tidak ditemukan');
        }

        try {
            $po = $this->transBeliPOModel->find($id);
            if (!$po) {
                throw new \RuntimeException('Data PO tidak ditemukan');
            }

            // Only allow deletion of draft POs
            if ($po->status != 0) {
                throw new \RuntimeException('Hanya PO draft yang dapat dihapus');
            }

            // Start transaction
            $this->db->transStart();

            // Delete PO details first
            $this->transBeliPODetModel->where('id_pembelian', $id)->delete();

            // Delete PO
            $this->transBeliPOModel->delete($id);

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw new \RuntimeException('Gagal menghapus PO');
            }

            return redirect()->to('transaksi/po')
                           ->with('success', 'PO berhasil dihapus');

        } catch (\Exception $e) {
            log_message('error', '[TransBeliPO::delete] ' . $e->getMessage());
            return redirect()->back()
                           ->with('error', $e->getMessage());
        }
    }

    /**
     * Process PO and update its status
     * 
     * @param int $id PO ID to process
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function proses($id)
    {
        try {
            // Get PO data
            $po = $this->transBeliPOModel->find($id);
            
            if (!$po) {
                throw new \Exception('PO tidak ditemukan');
            }

            // Update PO status to processed (2)
            $this->transBeliPOModel->update($id, [
                'status' => '4'  // 2 = Processed
            ]);

            return redirect()->to('transaksi/po')
                           ->with('success', 'PO berhasil diproses');
                           
        } catch (\Exception $e) {
            return redirect()->back()
                           ->with('error', 'Gagal memproses PO: ' . $e->getMessage());
        }
    }

    /**
     * Create invoice from PO
     * 
     * @param int $id PO ID
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function buatFaktur($id)
    {
        try {
            // Start transaction
            $this->db->transStart();

            // Get PO data
            $po = $this->transBeliPOModel->find($id);
            if (!$po) {
                throw new \RuntimeException('Data PO tidak ditemukan');
            }

            // Verify PO is in processed status
            if ($po->status != '1') {
                throw new \RuntimeException('Hanya PO yang sudah diproses yang dapat dibuatkan faktur');
            }

            // Get PO details
            $poDetails = $this->transBeliPODetModel->where('id_pembelian', $id)->findAll();
            if (empty($poDetails)) {
                throw new \RuntimeException('PO tidak memiliki item');
            }

            // Generate invoice number
            $invoiceNumber = $this->generateInvoiceNumber();

            // Create invoice header
            $invoiceData = [
                'no_faktur' => $invoiceNumber,
                'id_po' => $po->id,
                'id_supplier' => $po->id_supplier,
                'tgl_faktur' => date('Y-m-d'),
                'total' => 0, // Will be calculated from details
                'status' => '0', // Draft status
                'created_at' => date('Y-m-d H:i:s')
            ];

            // Insert invoice header
            $this->db->table('tbl_trans_beli_faktur')->insert($invoiceData);
            $invoiceId = $this->db->insertID();

            // Calculate total and create invoice details
            $total = 0;
            foreach ($poDetails as $detail) {
                $detailData = [
                    'id_faktur'     => $invoiceId,
                    'id_item'       => $detail->id_item,
                    'jml'           => $detail->jml,
                    'harga'         => $detail->harga ?? 0,
                    'subtotal'      => ($detail->jml * ($detail->harga ?? 0)),
                    'created_at'    => date('Y-m-d H:i:s')
                ];
                
                $this->db->table('tbl_trans_beli_faktur_det')->insert($detailData);
                $total += $detailData['subtotal'];
            }

            // Update invoice total
            $this->db->table('tbl_trans_beli_faktur')
                     ->where('id', $invoiceId)
                     ->update(['total' => $total]);

            // Update PO status to invoice created (2)
            $this->transBeliPOModel->update($id, [
                'status' => '2',
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            // Commit transaction
            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw new \RuntimeException('Gagal membuat faktur');
            }

            return redirect()->to("transaksi/pembelian/faktur/edit/{$invoiceId}")
                            ->with('success', 'Faktur berhasil dibuat');

        } catch (\Exception $e) {
            // Rollback transaction on error
            $this->db->transRollback();
            
            log_message('error', '[TransBeliPO::buatFaktur] ' . $e->getMessage());
            
            return redirect()->back()
                            ->with('error', $e->getMessage());
        }
    }

    /**
     * Generate invoice number
     * 
     * @return string
     */
    private function generateInvoiceNumber()
    {
        $prefix = 'FKT' . date('Ym');
        
        // Get last invoice number
        $lastInvoice = $this->db->table('tbl_trans_beli_faktur')
                               ->select('no_faktur')
                               ->like('no_faktur', $prefix, 'after')
                               ->orderBy('id', 'DESC')
                               ->get()
                               ->getRow();

        if (!$lastInvoice) {
            return $prefix . '0001';
        }

        // Extract number and increment
        $lastNumber = (int)substr($lastInvoice->no_faktur, -4);
        $newNumber = $lastNumber + 1;
        
        return $prefix . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Generate PDF for Purchase Order
     * 
     * @param int $id PO ID to print
     * @return mixed
     */
    public function print($id)
    {
        try {
            // Get PO data with relations
            $po = $this->transBeliPOModel->getWithRelations(['id' => $id])->first();
            if (!$po) {
                throw new \Exception('PO tidak ditemukan');
            }

            // Get PO details
            $po_details = $this->transBeliPODetModel->select('
                    tbl_trans_beli_po_det.*,
                    tbl_m_item.kode as kode,
                    tbl_m_item.item as deskripsi
                ')
                ->join('tbl_m_item', 'tbl_m_item.id = tbl_trans_beli_po_det.id_item')
                ->where('id_pembelian', $id)
                ->findAll();

            // Initialize PDF
            $pdf = new FPDF('P', 'mm', 'A4');
            $pdf->AddPage();
            
            // Set margins
            $pdf->SetMargins(20, 20, 20);
            
            // Header
            $pdf->SetFont('Arial', 'B', 12);
            $pdf->Cell(120, 6, 'KLINIK UTAMA dan LABORATORIUM "ESENSIA"', 0, 0, 'L');
            $pdf->Cell(50, 6, 'PURCHASE ORDER', 0, 1, 'R');
            
            $pdf->SetFont('Arial', '', 10);
            $pdf->Cell(170, 5, 'Perum Mutiara Pandanaran Blok D11', 0, 1, 'L');
            $pdf->Line(20, 33, 190, 33);
            $pdf->Ln(5);

            // APA & SIPA
            $pdf->Cell(15, 5, 'APA', 0, 0);
            $pdf->Cell(5, 5, ':', 0, 0);
            $pdf->Cell(170, 5, 'APT. UNGSARI RIZKI EKA PURWANTO, M.SC', 0, 1);
            
            $pdf->Cell(15, 5, 'SIPA', 0, 0);
            $pdf->Cell(5, 5, ':', 0, 0);
            $pdf->Cell(170, 5, '449.1/61/DPM-PTSP/SIPA/II/2022', 0, 1);
            $pdf->Ln(5);

            // Supplier Info & PO Details
            $pdf->Cell(20, 5, 'Kepada Yth.', 0, 1);
            $pdf->Cell(100, 5, $po->supplier_name, 0, 0);
            $pdf->Cell(25, 5, 'No. PO', 0, 0);
            $pdf->Cell(5, 5, ':', 0, 0);
            $pdf->Cell(40, 5, $po->no_nota, 0, 1);
            
            $pdf->Cell(100, 5, $po->supplier_address, 0, 0);
            $pdf->Cell(25, 5, 'Tanggal', 0, 0);
            $pdf->Cell(5, 5, ':', 0, 0);
            $pdf->Cell(40, 5, '2025-02-19', 0, 1);
            
            $pdf->Cell(100, 5, '', 0, 0);
            $pdf->Cell(25, 5, 'Oleh', 0, 0);
            $pdf->Cell(5, 5, ':', 0, 0);
            $pdf->Cell(40, 5, '', 0, 1);
            $pdf->Ln(5);

            // Table Header
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->Cell(35, 7, 'KODE', 1, 0, 'C');
            $pdf->Cell(85, 7, 'DESKRIPSI', 1, 0, 'C');
            $pdf->Cell(20, 7, 'JML', 1, 0, 'C');
            $pdf->Cell(30, 7, 'KETERANGAN', 1, 1, 'C');

            // Table Content
            $pdf->SetFont('Arial', '', 10);
            foreach ($po_details as $item) {
                $pdf->Cell(35, 7, $item->kode, 1, 0, 'L');
                $pdf->Cell(85, 7, $item->deskripsi, 1, 0, 'L');
                $pdf->Cell(20, 7, $item->jml, 1, 0, 'C');
                $pdf->Cell(30, 7, $item->keterangan, 1, 1, 'L');
            }

            // Footer
            $pdf->Ln(20);
            $pdf->Cell(85, 5, 'Pemesan', 0, 0, 'C');
            $pdf->Cell(85, 5, 'Semarang, ' . date('d F Y', strtotime($po->tgl_masuk)), 0, 1, 'C');
            
            $pdf->Ln(20);
            $pdf->Cell(85, 5, 'APT. UNGSARI RIZKI EKA PURWANTO, M.SC', 0, 1, 'C');

            // Output PDF
            $pdf->Output('PO_' . $po->no_nota . '.pdf', 'I');
            exit;
        } catch (\Exception $e) {
            return redirect()->back()
                           ->with('error', 'Gagal mencetak PO: ' . $e->getMessage());
        }
    }
} 