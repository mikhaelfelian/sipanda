<?php
/**
 * Created by:
 * Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * 2025-02-01
 * 
 * Stock Controller
 * 
 * Controller for managing stock/inventory data
 */

namespace App\Controllers\Gudang;

use App\Controllers\BaseController;
use App\Models\GudangModel;
use App\Models\ItemModel;
use App\Models\ItemStokModel;
use App\Models\ItemBatchModel;
use App\Models\ItemHistModel;
use App\Models\PengaturanModel;
use App\Models\MerkModel;
use App\Models\KategoriModel;

class Stock extends BaseController
{
    protected $itemModel;
    protected $gudangModel;
    protected $itemBatchModel;
    protected $itemHistModel;
    protected $pengaturanModel;
    protected $merkModel;
    protected $kategoriModel;
    protected $itemStokModel;
    protected $validation;

    public function __construct()
    {
        $this->gudangModel      = new GudangModel();
        $this->itemModel        = new ItemModel();
        $this->itemStokModel    = new ItemStokModel();
        $this->itemBatchModel   = new ItemBatchModel();
        $this->itemHistModel    = new ItemHistModel();
        $this->pengaturanModel  = new PengaturanModel();
        $this->merkModel        = new MerkModel();
        $this->kategoriModel    = new KategoriModel();
        $this->validation       = \Config\Services::validation();
    }
    /**
     * Menampilkan item yang dapat di-stock
     * 
     * @return string
     */
    public function items()
    {
        $kategori = $this->kategoriModel->where('status', '1')->findAll();
        $merk     = $this->merkModel->where('status', '1')->findAll();

        // Mendapatkan filter dari URL
        $filters = [
            'kategori' => $this->request->getGet('filter_kategori'),
            'merk'     => $this->request->getGet('filter_merk'),
            'item'     => $this->request->getGet('filter_item'),
            'harga'    => $this->request->getGet('filter_harga')
        ];

        // Konfigurasi paginasi
        $page = $this->request->getGet('page') ?? 1;
        $perPage = 10;

        // Mendapatkan total baris untuk paginasi
        $total = $this->itemModel->getStockable($filters, true);

        // Mendapatkan data yang dipaginasi
        $items = $this->itemModel->getStockable($filters, false, $perPage, ($page - 1) * $perPage);

        // Membuat pager
        $pager = service('pager');
        $pager->setPath('stock/items');
        $pager->makeLinks($page, $perPage, $total, 'default_full');

        $data = [
            'title'          => 'Data Item Stok',
            'kategoris'      => $kategori,
            'merks'          => $merk,
            'items'          => $items,
            'pager'          => $pager,
            'Pengaturan'     => $this->pengaturan,
            'user'           => $this->ionAuth->user()->row(),
            'itemStockModel' => $this->itemStokModel,
        ];
            
        return $this->view($this->theme->getThemePath() . '/gudang/item/index', $data);
    }

    /**
     * Menampilkan detail item
     */
    public function detail($id = null)
    {
        if (!$id) {
            return redirect()->to('stock/items')
                           ->with('error', 'ID item tidak ditemukan');
        }

        $item = $this->itemModel->getItemDetail($id);
        if (!$item) {
            return redirect()->to('stock/items')
                           ->with('error', 'Data item tidak ditemukan');
        }

        $data = [
            'title'           => 'Detail Item Stok',
            'item'            => $item,
            'stockDetails'    => $this->itemStokModel->getStockByItem($id),
            'itemHists'       => $this->itemHistModel->getWithRelations($id),
            'gudangs'         => $this->gudangModel->where('status', '1')->findAll(),
            'batches'         => $this->itemBatchModel->getBatchesByItem($id),
            'Pengaturan'      => $this->pengaturan,
            'user'            => $this->ionAuth->user()->row(),
            'itemStockModel'  => $this->itemStokModel
        ];

        return $this->view($this->theme->getThemePath() . '/gudang/item/detail', $data);
    }

    /**
     * Memperbarui jumlah stok dan mencatat riwayat
     */
    public function update($id = null)
    {
        if (!$id) {
            return redirect()->back()
                           ->with('error', 'ID stok tidak valid');
        }

        // Mendapatkan data stok
        $stock = $this->itemStokModel->find($id);
        if (!$stock) {
            return redirect()->back()
                           ->with('error', 'Data stok tidak ditemukan');
        }

        // Mendapatkan data item
        $item = $this->itemModel->getItemDetail($stock->id_item);
        if (!$item) {
            return redirect()->back()
                           ->with('error', 'Data item tidak ditemukan');
        }

        // Validasi input
        $rules = [
            'jumlah' => [
                'rules' => 'required|numeric|greater_than_equal_to[0]',
                'errors' => [
                    'required' => 'Jumlah harus diisi',
                    'numeric' => 'Jumlah harus berupa angka',
                    'greater_than_equal_to' => 'Jumlah tidak boleh negatif'
                ]
            ]
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                           ->with('errors', $this->validator->getErrors());
        }

        $newQty = $this->request->getPost('jumlah');
        
        $this->db->transBegin();

        // Memperbarui stok
        $updateData = ['jml' => $newQty];
        if (!$this->itemStokModel->update($id, $updateData)) {
            $this->db->transRollback();
            return redirect()->back()
                           ->with('error', 'Gagal mengupdate stok');
        }

        // Mencatat riwayat
        $historyData = [
            'id_gudang'  => $stock->id_gudang,
            'id_item'    => $item->id,
            'id_user'    => $this->ionAuth->user()->row()->id,
            'tgl_masuk'  => date('Y-m-d H:i:s'),
            'kode'       => $item->kode,
            'item'       => $item->item,
            'keterangan' => 'Ubah stok manual oleh ' . $this->ionAuth->user()->row()->username,
            'jml'        => $newQty,
            'satuan'     => $item->satuan,
            'status'     => '2'
        ];

        if (!$this->itemHistModel->insert($historyData)) {
            $this->db->transRollback();
            return redirect()->back()
                           ->with('error', 'Gagal menyimpan riwayat stok');
        }

        $this->db->transCommit();
        return redirect()->back()
                       ->with('success', 'Stok berhasil diupdate');
    }

    public function delete_hist($id)
    {
        try {
            // Memulai transaksi
            $this->db->transStart();
            
            // Mendapatkan catatan riwayat
            $history = $this->itemHistModel->find($id);
            if (!$history) {
                throw new \RuntimeException('Catatan riwayat tidak ditemukan');
            }

            // Hanya mengizinkan penghapusan catatan stok keluar (2) dan penyesuaian manual (7)
            if (!in_array($history->status, ['2', '7'])) {
                throw new \RuntimeException('Hanya catatan stok keluar dan penyesuaian manual yang dapat dihapus');
            }

            // Mendapatkan stok saat ini
            $currentStock = $this->itemStokModel->where([
                'id_item' => $history->id_item,
                'id_gudang' => $history->id_gudang
            ])->first();

            if (!$currentStock) {
                throw new \RuntimeException('Catatan stok tidak ditemukan');
            }

            // Menghitung jumlah stok yang dipulihkan berdasarkan jenis riwayat
            $restoredAmount = $history->jml;
            if ($history->status == '2') { 
                // Stok keluar
                // Ketika menghapus catatan stok keluar, kita perlu menambahkan stok kembali
                $newAmount = $currentStock->jml - abs($restoredAmount);
            } else if ($history->status == '7') { 
                // Penyesuaian manual
                // Untuk penyesuaian manual, kita perlu membalikkan penyesuaian
                // Jika itu adalah penyesuaian positif, kurangi
                // Jika itu adalah penyesuaian negatif, tambahkan kembali
                $newAmount = $currentStock->jml + $restoredAmount;
            }

            // Memastikan stok tidak menjadi negatif
            if ($newAmount < 0) {
                throw new \RuntimeException('Penghapusan akan mengakibatkan stok negatif');
            }

            // Memperbarui stok
            $this->itemStokModel->update($currentStock->id, [
                'jml' => $newAmount,
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            // Menghapus catatan riwayat
            $this->itemHistModel->delete($id);

            // Menyelesaikan transaksi
            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw new \RuntimeException('Gagal memproses penghapusan');
            }

            return redirect()
                ->back()
                ->with('success', 'Riwayat berhasil dihapus dan stok berhasil dipulihkan');

        } catch (\Exception $e) {
            // Membatalkan transaksi jika terjadi kesalahan
            $this->db->transRollback();
            
            log_message('error', '[Stock::delete_hist] ' . $e->getMessage());
            
            return redirect()
                ->back()
                ->with('error', 'Gagal menghapus riwayat: ' . $e->getMessage());
        }
    }
}