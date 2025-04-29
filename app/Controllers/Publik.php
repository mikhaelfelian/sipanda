<?php
/**
 * Created by:
 * Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * 2025-01-29
 * 
 * Publik Controller
 * 
 * Controller for handling public endpoints including autocomplete
 */

namespace App\Controllers;

use App\Models\ItemModel;
use App\Models\MedTransDetModel;
use App\Models\MedTransIcdModel;

class Publik extends BaseController
{
    protected $itemModel;
    protected $medTransDetModel;

    public function __construct()
    {
        parent::__construct();
        $this->itemModel = new ItemModel();
        $this->medTransDetModel = new \App\Models\MedTransDetModel();
        $this->medTransIcdModel = new \App\Models\MedTransIcdModel();
    }

    /**
     * Get items for autocomplete
     */
     public function getItemsStock()
     {
         try {
             $term = $this->request->getGet('term');
             
             // Build the query
             $builder = $this->db->table('tbl_m_item');
             $builder->select('
                 tbl_m_item.id,
                 tbl_m_item.kode,
                 tbl_m_item.item,
                 tbl_m_item.item_alias,
                 tbl_m_item.item_kand,
                 tbl_m_item.harga_beli,
                 tbl_m_item.harga_jual,
                 tbl_m_item.status
             ');
             $builder->where('tbl_m_item.status', '1');
             $builder->where('tbl_m_item.status_stok', '1');
             $builder->where('tbl_m_item.status_hps', '0');
             
             // Add search condition if term provided
             if (!empty($term)) {
                 $builder->groupStart()
                     ->like('item', $term)
                     ->orLike('item_kand', $term)
                     ->orLike('item_alias', $term)
                     ->orLike('kode', $term)
                     ->groupEnd();
             }
 
             $query = $builder->get();
             $results = $query->getResult();
 
             // Format the results
             $data = [];
             foreach ($results as $item) {
                 $data[] = [
                     'id'         => $item->id,
                     'kode'       => $item->kode,
                     'label'      => $item->item . ' (' . $item->kode . ')',
                     'item'       => $item->item,
                     'item_alias' => $item->item_alias,
                     'item_kand'  => $item->item_kand,
                     'harga_beli' => (float)$item->harga_beli,
                     'harga_jual' => (float)$item->harga_jual,
                     'status'     => (int)$item->status
                 ];
             }
 
             // Disable CSRF for this request
             if (isset($_COOKIE['csrf_cookie_name'])) {
                 unset($_COOKIE['csrf_cookie_name']);
                 setcookie('csrf_cookie_name', '', time() - 3600, '/');
             }
 
             // Send direct JSON response
             header('Content-Type: application/json; charset=utf-8');
             echo json_encode($data);
             exit();
         } catch (\Exception $e) {
             // Log the error
             log_message('error', '[Publik::getItems] Error: ' . $e->getMessage());
             
             // Send error response
             header('HTTP/1.1 500 Internal Server Error');
             header('Content-Type: application/json; charset=utf-8');
             echo json_encode([
                 'error' => true,
                 'message' => ENVIRONMENT === 'development' ? $e->getMessage() : 'Internal server error'
             ]);
             exit();
         }
     }

    public function getItems()
    {
        try {
            $term = $this->request->getGet('term');
            
            // Build the query
            $builder = $this->db->table('tbl_m_item');
            $builder->select('
                tbl_m_item.id,
                tbl_m_item.kode,
                tbl_m_item.item,
                tbl_m_item.item_alias,
                tbl_m_item.item_kand,
                tbl_m_item.harga_beli,
                tbl_m_item.harga_jual,
                tbl_m_item.status
            ');
            $builder->where('tbl_m_item.status', '1');
            $builder->where('tbl_m_item.status_hps', '0');

            // Add search condition if term provided
            if (!empty($term)) {
                $builder->groupStart()
                    ->like('item', $term)
                    ->orLike('item_kand', $term)
                    ->orLike('item_alias', $term)
                    ->orLike('kode', $term)
                    ->groupEnd();
            }

            $query = $builder->get();
            $results = $query->getResult();

            // Format the results
            $data = [];
            foreach ($results as $item) {
                $data[] = [
                    'id'         => $item->id,
                    'kode'       => $item->kode,
                    'label'      => $item->item . ' (' . $item->kode . ')',
                    'item'       => $item->item,
                    'item_alias' => $item->item_alias,
                    'item_kand'  => $item->item_kand,
                    'harga_beli' => (float)$item->harga_beli,
                    'harga_jual' => (float)$item->harga_jual,
                    'status'     => (int)$item->status
                ];
            }

            // Disable CSRF for this request
            if (isset($_COOKIE['csrf_cookie_name'])) {
                unset($_COOKIE['csrf_cookie_name']);
                setcookie('csrf_cookie_name', '', time() - 3600, '/');
            }

            // Send direct JSON response
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode($data);
            exit();
        } catch (\Exception $e) {
            // Log the error
            log_message('error', '[Publik::getItems] Error: ' . $e->getMessage());
            
            // Send error response
            header('HTTP/1.1 500 Internal Server Error');
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode([
                'error' => true,
                'message' => ENVIRONMENT === 'development' ? $e->getMessage() : 'Internal server error'
            ]);
            exit();
        }
    }

    public function getTindakan($id_medrecs)
    {
        // Disable output buffering
        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        // Set proper headers
        header('Content-Type: application/json');
        header('Cache-Control: no-store, no-cache, must-revalidate');

        try {
            // Get data without session interference
            $items = $this->db->table('tbl_trans_medrecs_det')
                ->select('
                    tbl_trans_medrecs_det.*,
                    tbl_m_item.kode,
                    tbl_m_item.item,
                    COALESCE(tbl_m_satuan.satuanBesar, "1") as satuan
                ')
                ->join('tbl_m_item', 'tbl_m_item.id = tbl_trans_medrecs_det.id_item')
                ->join('tbl_m_satuan', 'tbl_m_satuan.id = tbl_m_item.id_satuan', 'left')
                ->where('tbl_trans_medrecs_det.id_medrecs', $id_medrecs)
                ->where('tbl_trans_medrecs_det.status', 3)
                ->orderBy('tbl_trans_medrecs_det.tgl_simpan', 'DESC')
                ->get()
                ->getResult();

            // Send response and exit immediately
            echo json_encode([
                'success' => true,
                'data' => $items
            ]);
            exit();

        } catch (\Exception $e) {
            log_message('error', '[Get Tindakan] Error: ' . $e->getMessage());
            
            echo json_encode([
                'success' => false,
                'message' => 'Gagal memuat data: ' . $e->getMessage()
            ]);
            exit();
        }
    }

    public function deleteTindakan($id = null)
    {
        // Disable output buffering
        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        try {
            if (!$id) {
                throw new \Exception('ID tidak valid');
            }

            $deleted = $this->medTransDetModel->delete($id);
            
            if (!$deleted) {
                throw new \Exception('Gagal menghapus data');
            }

            // Send response
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'message' => 'Data berhasil dihapus'
            ]);
            exit();

        } catch (\Exception $e) {
            log_message('error', '[deleteTindakan] Error: ' . $e->getMessage());
            
            header('HTTP/1.1 500 Internal Server Error');
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'Gagal menghapus data: ' . $e->getMessage()
            ]);
            exit();
        }
    }

public function getIcd($id = null)
{
    try {
        if (!$id) {
            header('HTTP/1.1 500 Internal Server Error');
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'Gagal memuat data'
            ]);
        }

        // Fetch data from MedTransIcdModel
        $icdData = $this->medTransIcdModel->asObject()->where('id_medrecs', $id)->findAll();

        if (!$icdData) {
            throw new \Exception('Data tidak ditemukan');
        }

        // Send response
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'data' => $icdData
        ]);
        exit();

    } catch (\Exception $e) {
        log_message('error', '[getIcd] Error: ' . $e->getMessage());

        header('HTTP/1.1 500 Internal Server Error');
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => 'Gagal memuat data: ' . $e->getMessage()
        ]);
        exit();
    }
}

} 