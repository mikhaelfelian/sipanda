<?php
/**
 * Created by:
 * Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * 2025-02-06
 * 
 * MedDaftar Controller
 * Handles patient registration operations
 */

namespace App\Controllers\Medrecords;

use App\Controllers\BaseController;
use App\Models\MedDaftarModel;
use App\Models\GelarModel;
use App\Models\PoliModel;
use App\Models\PlatformModel;
use App\Models\PasienModel;
use App\Models\PenjaminModel;

class MedDaftar extends BaseController
{
    protected $medDaftarModel;
    protected $gelarModel;
    protected $poliModel;
    protected $platformModel;
    protected $pasienModel;
    protected $penjaminModel;

    public function __construct()
    {
        $this->medDaftarModel   = new MedDaftarModel();
        $this->gelarModel       = new GelarModel();
        $this->poliModel        = new PoliModel();
        $this->platformModel    = new PlatformModel();
        $this->pasienModel      = new PasienModel();
        $this->penjaminModel    = new PenjaminModel();
        
        helper(['image', 'tanggalan']);
    }

    /**
     * Display patient registration form
     */
    public function create()
    {
        $id_pasien = $this->request->getVar('id_pasien');
        $currentPage = $this->request->getVar('page_pasien') ?? 1;

        // Get pagination limit from settings 
        $perPage = $this->pengaturan->pagination_limit ?? 10;

        // Start with the model query
        $query = $this->pasienModel;

        // Filter by name/code/nik
        $search = $this->request->getVar('search');
        if ($search) {
            $query->groupStart()
                ->like('nama', $search)
                ->orLike('kode', $search)
                ->orLike('nik', $search)
                ->groupEnd();
        }

        // Filter by status
        $selectedStatus = $this->request->getVar('status');
        if ($selectedStatus !== null && $selectedStatus !== '') {
            $query->where('status', $selectedStatus);
        }

        $data = [
            'title'         => 'Pendaftaran Pasien',
            'Pengaturan'    => $this->pengaturan,
            'user'          => $this->ionAuth->user()->row(),
            'gelar'         => $this->gelarModel->findAll(),
            'poli'          => $this->poliModel->where('status', '1')->findAll(),
            'platform'      => $this->platformModel->findAll(),
            'gelars'        => $this->gelarModel->findAll(),
            'pasien'        => $this->pasienModel->find($id_pasien),
            'pasien_kode'   => $this->pasienModel->generateKode(),
            'pasiens'       => $query->paginate($perPage, 'pasien'),
            'pager'         => $this->pasienModel->pager,
            'page'          => $currentPage,
            'perPage'       => $perPage,
            'search'        => $search,
            'tipe_pas'      => $this->request->getVar('tipe_pas'),
            'breadcrumbs'   => '
                <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>
                <li class="breadcrumb-item">Medical Records</li>
                <li class="breadcrumb-item active">Pendaftaran</li>
            ',
            'penjamins'     => $this->penjaminModel->findAll(),
            'tipe_layanan'  => [
                1 => 'Rawat Jalan',
                2 => 'Rawat Inap',
                3 => 'Laboratorium',
                4 => 'Radiologi'
            ],
            'poliModel' => $this->poliModel,
            'penjaminModel' => $this->penjaminModel
        ];

        return view($this->theme->getThemePath() . '/medrecords/med_daftar', $data);
    }


    /**
     * Store new patient registration
     */
    public function store()
    {
        // Validation rules
        $rules = [
            'nik' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'NIK harus diisi',
                    'min_length' => 'NIK harus 16 digit',
                    'max_length' => 'NIK harus 16 digit',
                    'numeric' => 'NIK harus berupa angka'
                ]
            ],
            'id_gelar' => [
                'rules' => 'permit_empty|numeric',
                'errors' => [
                    'numeric' => 'Gelar tidak valid'
                ]
            ],
            'jns_klm' => [
                'rules' => 'required|in_list[L,P]',
                'errors' => [
                    'required' => 'Jenis kelamin harus dipilih',
                    'in_list' => 'Jenis kelamin tidak valid'
                ]
            ],
            'tmp_lahir' => [
                'rules' => 'required|min_length[3]|max_length[100]',
                'errors' => [
                    'required' => 'Tempat lahir harus diisi',
                    'min_length' => 'Tempat lahir terlalu pendek',
                    'max_length' => 'Tempat lahir terlalu panjang'
                ]
            ],
            'tgl_lahir' => [
                'rules' => 'required|valid_date',
                'errors' => [
                    'required' => 'Tanggal lahir harus diisi',
                    'valid_date' => 'Format tanggal lahir tidak valid'
                ]
            ],
            'alamat' => [
                'rules' => 'required|min_length[10]',
                'errors' => [
                    'required' => 'Alamat harus diisi',
                    'min_length' => 'Alamat terlalu pendek'
                ]
            ],
            'id_poli' => [
                'rules' => 'required|numeric|is_not_unique[tbl_m_poli.id]',
                'errors' => [
                    'required' => 'Poli harus dipilih',
                    'numeric' => 'Poli tidak valid',
                    'is_not_unique' => 'Poli tidak ditemukan'
                ]
            ],
            'tipe_bayar' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Penjamin harus dipilih',
                    'numeric' => 'Penjamin tidak valid',
                    'is_not_unique' => 'Penjamin tidak ditemukan'
                ]
            ],
            'tipe_rawat' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Tipe harus dipilih',
                    'in_list' => 'Tipe tidak valid'
                ]
            ],
            'tgl_masuk' => [
                'rules' => 'required|valid_date',
                'errors' => [
                    'required' => 'Tanggal masuk harus diisi',
                    'valid_date' => 'Format tanggal masuk tidak valid'
                ]
            ],
            'foto_pasien' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Foto pasien harus diunggah'
                ]
            ],
            'foto_ktp' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Foto KTP harus diunggah'
                ]
            ]
        ];

        // Run validation
        if (!$this->validate($rules)) {
            return redirect()->back()
                            ->withInput()
                            ->with('errors', $this->validator->getErrors());
        }

        try {
            // // Start transaction
            // $this->medDaftarModel->db->transStart();
            
            // Get current date in Y-m-d format
            $today = date('Y-m-d');

            // Get queue number for the selected poli today
            $id_poli = $this->request->getPost('id_poli');
            $queueNumber = $this->medDaftarModel->db->table('tbl_pendaftaran')
                ->where('id_poli', $id_poli)
                ->where('DATE(created_at)', $today)
                ->countAllResults() + 1;

            $kode           = $this->medDaftarModel->generateKode();
            $base_path      = WRITEPATH . 'uploads/pendaftaran';
            $patient_path   = $base_path . '/';

            $gelar          = $this->gelarModel->find($this->request->getPost('id_gelar'));
            $nama_pgl       = strtoupper($gelar->gelar . ' ' . $this->request->getPost('nama'));

            // Prepare data
            $data = [
                'id_gelar'       => $this->request->getPost('id_gelar'),
                'id_pasien'      => $this->request->getPost('id_pasien'),
                'id_poli'        => $this->request->getPost('id_poli'),
                'nama'           => $this->request->getPost('nama'),
                'nama_pgl'       => $nama_pgl,
                'nik'            => $this->request->getPost('nik'),
                'jns_klm'        => $this->request->getPost('jns_klm'),
                'alamat'         => $this->request->getPost('alamat'),
                'alamat_dom'     => $this->request->getPost('alamat_domisili'),
                'kontak'         => $this->request->getPost('no_hp'),
                'rt'             => $this->request->getPost('rt'),
                'rw'             => $this->request->getPost('rw'),
                'kelurahan'      => $this->request->getPost('kelurahan'),
                'kecamatan'      => $this->request->getPost('kecamatan'),
                'kota'           => $this->request->getPost('kota'),
                'tmp_lahir'      => $this->request->getPost('tmp_lahir'),
                'tgl_masuk'      => tgl_indo_sys($this->request->getPost('tgl_masuk')) . ' ' . date('H:i:s'),
                'tgl_lahir'      => tgl_indo_sys($this->request->getPost('tgl_lahir')),
                'pekerjaan'      => $this->request->getPost('pekerjaan'),
                'kode'           => $kode,
                'no_urut'        => $queueNumber,
                'tipe'           => $this->request->getPost('tipe_pas'),
                'tipe_rawat'     => $this->request->getPost('tipe_rawat'),
                'tipe_bayar'     => $this->request->getPost('tipe_bayar'),
                'status_dft'     => '1', // Offline registration
                'status'         => '1',
                'status_akt'     => '0', // Pending
            ];

            // Process patient photo
            if ($foto_pasien = $this->request->getPost('foto_pasien')) {
                if (strpos($foto_pasien, 'data:image') === false) {
                    throw new \Exception('Invalid patient photo format');
                }

                $prefix_foto = strtolower($kode) . '_dft_profile_';

                $filename = base64_to_image(
                    $foto_pasien,
                    $patient_path,
                    $prefix_foto,
                    strtolower($kode)
                );

                if (!$filename) {
                    throw new \Exception('Failed to save patient photo');
                }

                $filePath = 'uploads/pendaftaran/' . $prefix_foto . $kode . '.png';
                $data['file_base64'] = $filePath;
            }

            // Process KTP photo
            if ($foto_ktp = $this->request->getPost('foto_ktp')) {
                if (strpos($foto_ktp, 'data:image') === false) {
                    throw new \Exception('Invalid KTP photo format');
                }

                $prefix_ktp = strtolower($kode) . '_dft_ktp_';

                $filename = base64_to_image(
                    $foto_ktp,
                    $patient_path,
                    $prefix_ktp,
                    strtolower($kode)
                );

                if (!$filename) {
                    throw new \Exception('Failed to save KTP photo');
                }

                $filePath = 'uploads/pendaftaran/' . $prefix_ktp . $kode . '.png';
                $data['file_base64_id'] = $filePath;
            }

            // Insert registration data
            $this->medDaftarModel->insert($data);

            $this->medDaftarModel->db->transComplete();

            if ($this->medDaftarModel->db->transStatus() === false) {
                throw new \Exception('Gagal menyimpan pendaftaran');
            }

            return redirect()->to('medrecords/antrian')
                            ->with('success', 'Pendaftaran berhasil. Nomor antrian: ' . $queueNumber);
        } catch (\Exception $e) {
            $this->medDaftarModel->db->transRollback();
            
            return redirect()->back()
                            ->withInput()
                            ->with('error', 'Gagal menyimpan pendaftaran: ' . $e->getMessage());
        }
    }

    /**
     * Confirm patient registration
     */
    public function konfirm($id)
    {
        try {
            $this->medDaftarModel->update($id, ['status_akt' => '1']);
            return redirect()->back()->with('success', 'Status antrian berhasil diubah');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengubah status antrian');
        }
    }
} 