<?php
/**
 * Created by:
 * Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * 2025-02-20
 * 
 * MedTrans Controller
 * Handles medical record transaction operations
 */

namespace App\Controllers\Medrecords;

use App\Controllers\BaseController;
use App\Models\MedTransModel;
use App\Models\MedTransDetModel;
use App\Models\MedDaftarModel;
use App\Models\PoliModel;
use App\Models\KaryawanModel;
use App\Models\PasienModel;
use IonAuth\Libraries\IonAuth;
use App\Models\ItemModel;
use App\Models\IcdModel;
use App\Models\MedTransIcdModel;
use FPDF;

class MedTrans extends BaseController
{
    protected $medTransModel;
    protected $medTransDetModel;
    protected $medDaftarModel;
    protected $poliModel;
    protected $karyawanModel;
    protected $pasienModel;
    protected $ionAuth;
    protected $pengaturan;
    protected $itemModel;
    protected $icdModel;
    protected $medTransIcdModel;

    public function __construct()
    {
        $this->medTransModel = new MedTransModel();
        $this->medTransDetModel = new MedTransDetModel();
        $this->medDaftarModel = new MedDaftarModel();
        $this->poliModel = new PoliModel();
        $this->karyawanModel = new KaryawanModel();
        $this->pasienModel = new PasienModel();
        $this->ionAuth = new IonAuth();
        $this->itemModel = new ItemModel();
        $this->icdModel = new IcdModel();
        $this->medTransIcdModel = new MedTransIcdModel();
        
        // Get settings from parent constructor
        parent::__construct();
    }

    /**
     * Display list of medical record transactions
     */
    public function index()
    {
        $currentPage = $this->request->getVar('page') ?? 1;
        $perPage = $this->pengaturan->pagination_limit ?? 10;

        // Get transactions with joins
        $query = $this->medTransModel
            ->select('tbl_trans_medrecs.*, tbl_m_poli.poli, tbl_pendaftaran.no_urut, tbl_m_pasien.jns_klm, tbl_m_pasien.nik, tbl_m_pasien.tgl_lahir, tbl_m_pasien.no_hp')
            ->join('tbl_m_poli', 'tbl_m_poli.id = tbl_trans_medrecs.id_poli', 'left')
            ->join('tbl_pendaftaran', 'tbl_pendaftaran.id = tbl_trans_medrecs.id_dft', 'left')
            ->join('tbl_m_pasien', 'tbl_m_pasien.id = tbl_trans_medrecs.id_pasien', 'left')
            ->where('tbl_trans_medrecs.tipe', '1')
            ->orderBy('tbl_trans_medrecs.created_at', 'DESC');

        $data = [
            'title'       => 'Data Medical Records',
            'Pengaturan'  => $this->pengaturan,
            'user'        => $this->ionAuth->user()->row(),
            'medrecs'     => $query->paginate($perPage),
            'pager'       => $this->medTransModel->pager,
            'currentPage' => $currentPage,
            'perPage'     => $perPage,
            'breadcrumbs' => '
                <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>
                <li class="breadcrumb-item">Medical Records</li>
                <li class="breadcrumb-item active">Data Medical Records</li>
            '
        ];

        return view($this->theme->getThemePath() . '/medrecords/med_trans_index', $data);
    }

    

    public function rawat_inap()
    {
        $currentPage = $this->request->getVar('page') ?? 1;
        $perPage = $this->pengaturan->pagination_limit ?? 10;

        // Get transactions with joins
        $query = $this->medTransModel
            ->select('tbl_trans_medrecs.*, tbl_m_poli.poli, tbl_pendaftaran.no_urut, tbl_m_pasien.jns_klm, tbl_m_pasien.nik, tbl_m_pasien.tgl_lahir, tbl_m_pasien.no_hp')
            ->join('tbl_m_poli', 'tbl_m_poli.id = tbl_trans_medrecs.id_poli', 'left')
            ->join('tbl_pendaftaran', 'tbl_pendaftaran.id = tbl_trans_medrecs.id_dft', 'left')
            ->join('tbl_m_pasien', 'tbl_m_pasien.id = tbl_trans_medrecs.id_pasien', 'left')
            ->where('tbl_trans_medrecs.tipe', '2')
            ->orderBy('tbl_trans_medrecs.created_at', 'DESC');

        $data = [
            'title'       => 'Data Medical Records',
            'Pengaturan'  => $this->pengaturan,
            'user'        => $this->ionAuth->user()->row(),
            'medrecs'     => $query->paginate($perPage),
            'pager'       => $this->medTransModel->pager,
            'currentPage' => $currentPage,
            'perPage'     => $perPage,
            'breadcrumbs' => '
                <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>
                <li class="breadcrumb-item">Medical Records</li>
                <li class="breadcrumb-item active">Data Medical Records</li>
            '
        ];

        return view($this->theme->getThemePath() . '/medrecords/med_trans_index_ranap', $data);
    }

    public function create($id = null)
    {
        if ($id === null) {
            return redirect()->to('medrecords/antrian')->with('error', 'Data pendaftaran tidak ditemukan');
        }

        // Get registration data
        $daftar = $this->medDaftarModel->find($id);
        if (!$daftar) {
            return redirect()->to('medrecords/antrian')->with('error', 'Data pendaftaran tidak ditemukan');
        }

        // Get patient data
        $pasien = $this->pasienModel->find($daftar->id_pasien);
        if (!$pasien) {
            return redirect()->to('medrecords/antrian')->with('error', 'Data pasien tidak ditemukan');
        }

        // Get doctors (karyawan with group ID 7)
        $dokters = $this->karyawanModel->getByGroup(7);

        $data = [
            'title' => 'Form Medical Checkup',
            'Pengaturan'    => $this->pengaturan,
            'user' => $this->ionAuth->user()->row(),
            'daftar' => $daftar,
            'pasien' => $pasien,
            'poliModel' => $this->poliModel,
            'dokters' => $dokters,
            'breadcrumbs' => '
                <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>
                <li class="breadcrumb-item">Medical Records</li>
                <li class="breadcrumb-item active">Form Medical Checkup</li>
            '
        ];

        return view($this->theme->getThemePath() . '/medrecords/med_trans', $data);
    }

    /**
     * Store medical record transaction
     */
    public function store()
    {
        try {
            $this->medTransModel->db->transBegin();

            // Get and validate id_dft first
            $id_dft = $this->request->getPost('id_dft');
            if (!$id_dft) {
                throw new \Exception('Data pendaftaran tidak ditemukan (ID: kosong)');
            }


            // Get registration data first and validate
            $daftar = $this->medDaftarModel->find($id_dft);
            if (!$daftar) {
                throw new \Exception('Data pendaftaran tidak ditemukan (ID: ' . $id_dft . ')');
            }

            // Get form data
            $data = [
                'id_user'        => $this->ionAuth->user()->row()->id,
                'id_dokter'      => $this->request->getPost('id_dokter'),
                'id_pasien'      => $this->request->getPost('id_pasien'),
                'id_poli'        => $this->request->getPost('id_poli'),
                'id_dft'         => $id_dft,
                'tgl_masuk'      => date('Y-m-d H:i:s'),
                'no_rm'          => $this->medTransModel->generateKode(),
                'keluhan'        => $this->request->getPost('keluhan'),
                'ttv_st'         => $this->request->getPost('ttv_st'),
                'ttv_bb'         => $this->request->getPost('ttv_bb'),
                'ttv_tb'         => $this->request->getPost('ttv_tb'),
                'ttv_sistole'    => $this->request->getPost('ttv_sistole'),
                'ttv_diastole'   => $this->request->getPost('ttv_diastole'),
                'ttv_nadi'       => $this->request->getPost('ttv_nadi'),
                'ttv_laju'       => $this->request->getPost('ttv_laju'),
                'ttv_saturasi'   => $this->request->getPost('ttv_saturasi'),
                'ttv_skala'      => $this->request->getPost('ttv_skala'),
                'tipe'           => $this->request->getPost('tipe'),
                'status'         => '1', // Set initial status to anamnesa
                'status_periksa' => '0',
                'status_resep'   => '0',
                'status_pos'     => '0',
                'status_hps'     => '0'
            ];

            // Add registration data
            $data['pasien']         = $daftar->nama_pgl;
            $data['pasien_alamat']  = $daftar->alamat;
            $data['pasien_nik']     = $daftar->nik;
            $data['tipe_bayar']     = $daftar->tipe_bayar;

            // Get poli data
            $poli = $this->poliModel->find($data['id_poli']);
            if ($poli) {
                $data['poli'] = $poli->poli;
            }

            // Get doctor data
            $dokter = $this->karyawanModel->find($data['id_dokter']);
            if ($dokter) {
                $data['dokter']     = $dokter->nama_pgl;
                $data['dokter_nik'] = $dokter->nik;
            }

            // Insert medical record data
            $result = $this->medTransModel->insert($data);
            if (!$result) {
                throw new \Exception('Gagal menyimpan data medical record');
            }

            // // Update registration status with explicit where clause
            // $updateResult = $this->medDaftarModel
            //     ->where('id', $id_dft)
            //     ->set(['status_periksa' => '1'])
            //     ->update();

            // if ($updateResult === false) {
            //     throw new \Exception('Gagal mengupdate status pendaftaran (ID: ' . $id_dft . ')');
            // }

            $this->medTransModel->db->transCommit();

            return redirect()->to('medrecords/rawat_jalan')
                ->with('success', 'Data pemeriksaan berhasil disimpan');

        } catch (\Exception $e) {
            $this->medTransModel->db->transRollback();
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal menyimpan data: ' . $e->getMessage());
        }
    }

    public function store_periksa()
    {
        if ($this->request->getMethod() !== 'post') {
            return redirect()->back()->with('error', 'Invalid request method');
        }

        $id = $this->request->getPost('id');
        $data = [
            'id_dokter'     => $this->ionAuth->user()->row()->id,
            'updated_at'    => date('Y-m-d H:i:s'),
            'diagnosa'      => $this->request->getPost('diagnosa'),
            'anamnesa'      => $this->request->getPost('anamnesa'),
            'pemeriksaan'   => $this->request->getPost('pemeriksaan'),
            'program'       => $this->request->getPost('program'),
            'alergi'        => $this->request->getPost('alergi')
        ];

        try {
            $this->medTransModel->update($id, $data);
            return redirect()->to('medrecords/rawat_jalan')
                ->with('success', 'Data pemeriksaan berhasil diperbarui');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal memperbarui data: ' . $e->getMessage());
        }
    }

    /**
     * Handle medical record actions
     * 
     * @param int $id Medical record ID
     * @return mixed
     */
    public function aksi($id = null)
    {
        if (!$id) {
            return redirect()->back()->with('error', 'ID rekam medis tidak ditemukan');
        }

        // Get medical record data with joins
        $medRecord = $this->medTransModel
            ->select('
                tbl_trans_medrecs.*,
                tbl_m_poli.poli,
                tbl_pendaftaran.no_urut,
                tbl_m_pasien.kode as no_pasien,
                tbl_m_pasien.nama_pgl as nama_pasien,
                tbl_m_pasien.jns_klm,
                tbl_m_pasien.nik,
                tbl_m_pasien.tgl_lahir,
                tbl_m_pasien.file_foto,
                tbl_m_pasien.id as id_pasien,
                tbl_trans_medrecs.created_at,
                tbl_trans_medrecs.tgl_masuk,
                tbl_trans_medrecs.tgl_keluar
            ')
            ->join('tbl_m_poli', 'tbl_m_poli.id = tbl_trans_medrecs.id_poli', 'left')
            ->join('tbl_pendaftaran', 'tbl_pendaftaran.id = tbl_trans_medrecs.id_dft', 'left')
            ->join('tbl_m_pasien', 'tbl_m_pasien.id = tbl_trans_medrecs.id_pasien', 'left')
            ->find($id);

        if (!$medRecord) {
            return redirect()->back()->with('error', 'Data rekam medis tidak ditemukan');
        }

        // Get items from ItemModel using getStockable method
        $itemModel = new \App\Models\ItemModel();
        $items = $itemModel->getStockable();

        $data = [
            'title'       => 'Data Medical Records',
            'Pengaturan'  => $this->pengaturan,
            'user'        => $this->ionAuth->user()->row(),
            'medrec'      => $medRecord,
            'tindakan'    => $this->itemModel->getTindakan(),
            'sql_icd'     => $this->icdModel->getIcdList(),
            'items'       => $items,
            'breadcrumbs' => '
                <li class="breadcrumb-item"><a href="'.base_url().'">Home</a></li>
                <li class="breadcrumb-item"><a href="'.base_url('medrecords/rawat_jalan').'">Medical Records</a></li>
                <li class="breadcrumb-item active">Aksi</li>
            '
        ];

        return view($this->theme->getThemePath() . '/medrecords/med_trans_aksi', $data);
    }

    /**
     * Store tindakan to cart
     * 
     * @return \CodeIgniter\HTTP\ResponseInterface
     */
    public function cart_tindakan()
    {
        // Disable output buffering
		/*
        while (ob_get_level() > 0) {
            ob_end_clean();
        }
		*/
        
        // Set headers
        header('Content-Type: application/json');
        header('Cache-Control: no-store, no-cache, must-revalidate');
        
        try {
            // Get input data
            $id_trans = $this->request->getPost('id_trans');
            $kode = $this->request->getPost('kode');
            $ket = $this->request->getPost('ket');
            $jml = $this->request->getPost('jml');
            $harga = $this->request->getPost('harga');

            // Validate required fields
            if (!$id_trans || !$kode || !$jml || !$harga) {
                throw new \Exception('Semua field harus diisi');
            }

            // Get item details
            $item = $this->db->table('tbl_m_item')
                            ->select('tbl_m_item.*, tbl_m_satuan.satuanBesar')
                            ->join('tbl_m_satuan', 'tbl_m_satuan.id = tbl_m_item.id_satuan', 'left')
                            ->where('tbl_m_item.id', $kode)
                            ->get()
                            ->getRow();

            if (!$item) {
                throw new \Exception('Item tidak ditemukan');
            }

            // Format numbers
            $jml        = (int) $jml;
            $harga      = format_angka_db($harga);
            $subtotal   = $jml * $harga;

            // Prepare data
            $data = [
                'id_medrecs'   => $id_trans,
                'id_item'      => $item->id,
                'id_item_kat'  => $item->id_kategori,
                'id_item_sat'  => $item->id_satuan,
                'id_user'      => $this->ionAuth->user()->row()->id,
                'tgl_simpan'   => date('Y-m-d H:i:s'),
                'tgl_masuk'    => date('Y-m-d H:i:s'),
                'kode'         => $item->kode,
                'item'         => $item->item,
                'keterangan'   => $ket,
                'jml'          => $jml,
                'satuan'       => $item->satuanBesar ?? '1', // Default to '1' if satuan is null
                'harga'        => $harga,
                'subtotal'     => $subtotal,
                'status'       => 3,
                'status_post'  => '0',
                'status_pj'    => '0',
                'status_rc'    => '0',
                'status_rf'    => '0',
                'status_pkt'   => '0',
                'sp'           => '0'
            ];

            // Insert to detail table
            $inserted = $this->medTransDetModel->insert($data);

            if (!$inserted) {
                throw new \Exception('Gagal menyimpan data');
            }

            // Output JSON directly
            echo json_encode([
                'success' => true,
                'message' => 'Tindakan berhasil ditambahkan'
            ]);
            exit;

        } catch (\Exception $e) {
            log_message('error', '[Cart Tindakan] Error: ' . $e->getMessage());
            
            // Send error response
            die(json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]));
        }
    }
	
    public function cart_tindakan_del($id)
    {
        try {
            $deleted = $this->medTransDetModel->delete($id);
            
            if ($deleted) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Tindakan berhasil dihapus'
                ]);
            } else {
                throw new \Exception('Gagal menghapus tindakan');
            }
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal menghapus data: ' . $e->getMessage()
            ]);
        }
    }

    public function cart_icd()
    {
        try {
            $icdId = $this->request->getPost('icd_id');
            $medId = $this->request->getPost('med_id');

            // Get ICD details
            $icd = $this->icdModel->find($icdId);
            if (!$icd) {
                return $this->response->setJSON(['success' => false, 'message' => 'ICD tidak ditemukan']);
            }

            // Get Medrec details
            $medrec = $this->medTransModel->find($medId);
            if (!$medrec) {
                return $this->response->setJSON(['success' => false, 'message' => 'Rekam medis tidak ditemukan']);
            }

            // Prepare data
            $data = [
                'id_medrecs' => $medId,
                'id_icd'     => $icdId,
                'id_dokter'  => $medrec->id_dokter,
                'id_user'    => $this->ionAuth->user()->row()->id,
                'kode'       => $icd->kode,
                'icd'        => $icd->icd,
                'status_icd' => '0',
                'created_at' => date('Y-m-d H:i:s'),
            ];

            // Insert data
            $this->medTransIcdModel->insert($data);

            return $this->response->setJSON(['success' => true, 'message' => 'Diagnosa berhasil ditambahkan']);
        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'message' => 'Gagal menambahkan diagnosa: ' . $e->getMessage()]);
        }
    }

    /**
     * Generate PDF patient label
     */
    public function pdf_label($id)
    {
        try {
            // Clean any existing output buffers
            while (ob_get_level() > 0) {
                ob_end_clean();
            }

            // Get patient data
            $patient = $this->pasienModel->find($id);
            if (!$patient) {
                throw new \Exception('Patient not found');
            }

            // Create PDF
            $pdf = new FPDF('L', 'mm', array(101.6, 50.8)); // 4x2 inches label size
            $pdf->AddPage();
            $pdf->SetMargins(5, 5, 5);
            $pdf->SetAutoPageBreak(false);

            // Set background color (white)
            $pdf->SetFillColor(255, 255, 255);
            $pdf->Rect(0, 0, $pdf->GetPageWidth(), $pdf->GetPageHeight(), 'F');

            // Add clinic logo from settings
            if (!empty($this->pengaturan->logo)) {
                $logoPath = FCPATH . $this->pengaturan->logo;
                if (file_exists($logoPath)) {
                    $pdf->Image($logoPath, 5, 2, 15);
                }
            }

            // Clinic name from settings
            $pdf->SetFont('Arial', 'B', 14);
            $pdf->SetXY(22, 2);
            $pdf->Cell(0, 6, strtoupper($this->pengaturan->judul), 0, 1, 'L');

            // Clinic address from settings
            $pdf->SetFont('Arial', '', 8);
            $pdf->SetXY(22, 8);
            $pdf->MultiCell(0, 4, $this->pengaturan->alamat, 0, 'L');

            // Add horizontal line
            $pdf->Line(5, 15, 95, 15);

            // Patient Name (Large and Bold)
            $pdf->SetFont('Arial', 'B', 14);
            $pdf->SetXY(5, 17);
            $pdf->Cell(0, 7, strtoupper($patient->nama_pgl), 0, 1, 'L');

            // Medical Record Number
            $pdf->SetFont('Arial', 'B', 12);
            $pdf->SetXY(5, 25);
            $pdf->Cell(25, 6, 'No. RM:', 0, 0, 'L');
            $pdf->SetFont('Arial', '', 12);
            $pdf->Cell(0, 6, $patient->kode, 0, 1, 'L');

            // Patient Details (2 columns)
            $pdf->SetFont('Arial', '', 9);
            
            // Left Column
            $x1 = 5;
            $y = 32;
            
            // Gender
            $pdf->SetXY($x1, $y);
            $pdf->Cell(20, 4, 'Gender', 0, 0, 'L');
            $pdf->Cell(2, 4, ':', 0, 0, 'C');
            $pdf->Cell(23, 4, $patient->jns_klm == 'L' ? 'Laki-laki' : 'Perempuan', 0, 1, 'L');
            
            // Birth Date
            $pdf->SetXY($x1, $y + 5);
            $pdf->Cell(20, 4, 'Tgl Lahir', 0, 0, 'L');
            $pdf->Cell(2, 4, ':', 0, 0, 'C');
            $pdf->Cell(23, 4, date('d-m-Y', strtotime($patient->tgl_lahir)), 0, 1, 'L');
            
            // Age
            $pdf->SetXY($x1, $y + 10);
            $pdf->Cell(20, 4, 'Usia', 0, 0, 'L');
            $pdf->Cell(2, 4, ':', 0, 0, 'C');
            $pdf->Cell(23, 4, usia($patient->tgl_lahir), 0, 1, 'L');

            // Right Column
            $x2 = 55;
            
            // Phone
            $pdf->SetXY($x2, $y);
            $pdf->Cell(20, 4, 'Telepon', 0, 0, 'L');
            $pdf->Cell(2, 4, ':', 0, 0, 'C');
            $pdf->Cell(0, 4, $patient->no_hp ?? '-', 0, 1, 'L');
            
            // NIK
            $pdf->SetXY($x2, $y + 5);
            $pdf->Cell(20, 4, 'NIK', 0, 0, 'L');
            $pdf->Cell(2, 4, ':', 0, 0, 'C');
            $pdf->Cell(0, 4, $patient->nik ?? '-', 0, 1, 'L');

            // Print date at bottom right
            $pdf->SetFont('Arial', 'I', 7);
            $pdf->SetXY(5, 45);
            $pdf->Cell(0, 4, 'Printed: ' . date('d-m-Y H:i'), 0, 0, 'R');

            // Output PDF
            ob_start();
            $pdf->Output('I', 'label_pasien_' . $patient->kode . '.pdf');
            exit();

        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Generate PDF patient card
     * 
     * @param int $id Patient ID
     * @return mixed
     */
    public function pdf_kartu_pasien($id)
    {
        try {
            // Clean any existing output buffers
            while (ob_get_level() > 0) {
                ob_end_clean();
            }

            // Get patient data
            $patient = $this->pasienModel->find($id);
            if (!$patient) {
                throw new \Exception('Patient not found');
            }

            // Create PDF
            $pdf = new FPDF('L', 'mm', array(85.6, 54)); // Credit card size
            $pdf->AddPage();
            $pdf->SetMargins(5, 5, 5);
            $pdf->SetAutoPageBreak(false);

            // Set background color (white)
            $pdf->SetFillColor(255, 255, 255);
            $pdf->Rect(0, 0, $pdf->GetPageWidth(), $pdf->GetPageHeight(), 'F');

            // Add clinic logo from settings
            if (!empty($this->pengaturan->logo)) {
                $logoPath = FCPATH . $this->pengaturan->logo;
                if (file_exists($logoPath)) {
                    $pdf->Image($logoPath, 5, 2, 15);
                }
            }

            // Clinic name from settings
            $pdf->SetFont('Arial', 'B', 12);
            $pdf->SetXY(22, 2);
            $pdf->Cell(0, 6, strtoupper($this->pengaturan->judul), 0, 1, 'L');

            // Clinic address from settings
            $pdf->SetFont('Arial', '', 6);
            $pdf->SetXY(22, 8);
            $pdf->MultiCell(0, 3, $this->pengaturan->alamat, 0, 'L');

            // Patient photo if exists
            if (!empty($patient->file_foto) && file_exists(FCPATH . $patient->file_foto)) {
                $pdf->Image(FCPATH . $patient->file_foto, 5, 17, 20, 20);
            }

            // Patient details
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->SetXY(27, 17);
            $pdf->Cell(0, 5, strtoupper($patient->nama_pgl), 0, 1, 'L');

            $pdf->SetFont('Arial', '', 8);
            
            // No. RM
            $pdf->SetXY(27, 22);
            $pdf->Cell(25, 5, 'No. RM', 0, 0, 'L');
            $pdf->Cell(2, 5, ':', 0, 0, 'C');
            $pdf->Cell(0, 5, $patient->kode, 0, 1, 'L');

            // Birth date
            $pdf->SetXY(27, 27);
            $pdf->Cell(25, 5, 'Tgl Lahir', 0, 0, 'L');
            $pdf->Cell(2, 5, ':', 0, 0, 'C');
            $pdf->Cell(0, 5, date('d-m-Y', strtotime($patient->tgl_lahir)), 0, 1, 'L');

            // Gender
            $pdf->SetXY(27, 32);
            $pdf->Cell(25, 5, 'Jenis Kelamin', 0, 0, 'L');
            $pdf->Cell(2, 5, ':', 0, 0, 'C');
            $pdf->Cell(0, 5, $patient->jns_klm == 'L' ? 'Laki-laki' : 'Perempuan', 0, 1, 'L');

            // Note at bottom
            $pdf->SetFont('Arial', 'I', 6);
            $pdf->SetXY(5, 45);
            $pdf->MultiCell(0, 3, 'Kartu ini adalah milik ' . $this->pengaturan->judul . '. Jika menemukan kartu ini, mohon kembalikan ke alamat yang tertera.', 0, 'L');

            // Output PDF
            $pdf->Output('I', 'kartu_pasien_' . $patient->kode . '.pdf');
            exit();

        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function radiologi()
    {
        try {
            $currentPage = $this->request->getVar('page') ?? 1;
            $perPage = $this->pengaturan->pagination_limit ?? 10;

            // Use query builder for pagination
            $builder = $this->medTransModel
                ->select('tbl_trans_medrecs.*, tbl_m_poli.poli, tbl_pendaftaran.no_urut, tbl_m_pasien.jns_klm, tbl_m_pasien.nik, tbl_m_pasien.tgl_lahir, tbl_m_pasien.no_hp')
                ->join('tbl_m_poli', 'tbl_m_poli.id = tbl_trans_medrecs.id_poli', 'left')
                ->join('tbl_pendaftaran', 'tbl_pendaftaran.id = tbl_trans_medrecs.id_dft', 'left')
                ->join('tbl_m_pasien', 'tbl_m_pasien.id = tbl_trans_medrecs.id_pasien', 'left')
                ->where('tbl_trans_medrecs.tipe', '4')
                ->orderBy('tbl_trans_medrecs.created_at', 'DESC');

            $data = [
                'title'       => 'Data Radiologi',
                'Pengaturan'  => $this->pengaturan,
                'user'        => $this->ionAuth->user()->row(),
                'medrecs'     => $builder->paginate($perPage, 'default'),
                'pager'       => $this->medTransModel->pager,
                'currentPage' => $currentPage,
                'perPage'     => $perPage,
                'breadcrumbs' => '
                    <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>
                    <li class="breadcrumb-item">Medical Records</li>
                    <li class="breadcrumb-item active">Data Radiologi</li>
                '
            ];

            return view($this->theme->getThemePath() . '/medrecords/med_trans_index_rad', $data);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function laboratorium()
    {
        try {
            $currentPage = $this->request->getVar('page') ?? 1;
            $perPage = $this->pengaturan->pagination_limit ?? 10;

            // Use query builder for pagination
            $builder = $this->medTransModel
                ->select('tbl_trans_medrecs.*, tbl_m_poli.poli, tbl_pendaftaran.no_urut, tbl_m_pasien.jns_klm, tbl_m_pasien.nik, tbl_m_pasien.tgl_lahir, tbl_m_pasien.no_hp')
                ->join('tbl_m_poli', 'tbl_m_poli.id = tbl_trans_medrecs.id_poli', 'left')
                ->join('tbl_pendaftaran', 'tbl_pendaftaran.id = tbl_trans_medrecs.id_dft', 'left')
                ->join('tbl_m_pasien', 'tbl_m_pasien.id = tbl_trans_medrecs.id_pasien', 'left')
                ->where('tbl_trans_medrecs.tipe', '3')
                ->orderBy('tbl_trans_medrecs.created_at', 'DESC');

            $data = [
                'title'       => 'Data Laboratorium',
                'Pengaturan'  => $this->pengaturan,
                'user'        => $this->ionAuth->user()->row(),
                'medrecs'     => $builder->paginate($perPage, 'default'),
                'pager'       => $this->medTransModel->pager,
                'currentPage' => $currentPage,
                'perPage'     => $perPage,
                'breadcrumbs' => '
                    <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>
                    <li class="breadcrumb-item">Medical Records</li>
                    <li class="breadcrumb-item active">Data Laboratorium</li>
                '
            ];

            return view($this->theme->getThemePath() . '/medrecords/med_trans_index_lab', $data);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
} 