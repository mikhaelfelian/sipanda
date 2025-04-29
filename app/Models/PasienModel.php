<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Created by:
 * Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * 2025-02-21
 * 
 * Pasien Model
 * Handles patient data operations
 */
class PasienModel extends Model
{
    protected $table            = 'tbl_m_pasien';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $allowedFields    = ['*'];

    // Dates
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Get base query with gelar join
     */
    protected function getBaseQuery()
    {
        return $this->select('tbl_m_pasien.*, tbl_m_gelar.gelar')
                    ->join('tbl_m_gelar', 'tbl_m_gelar.id = tbl_m_pasien.id_gelar', 'left');
    }

    /**
     * Override find to include gelar data
     */
    public function find($id = null)
    {
        if ($id === null) {
            return null;
        }

        // Reset any existing query
        $this->builder()->resetQuery();
        
        return $this->db->table($this->table . ' p')
                       ->select('p.*, g.gelar')
                       ->join('tbl_m_gelar g', 'g.id = p.id_gelar', 'left')
                       ->where('p.id', $id)
                       ->get()
                       ->getRow();
    }

    /**
     * Override findAll to exclude soft deleted records
     */
    public function findAll(?int $limit = null, int $offset = 0)
    {
        // Reset any existing query
        $this->builder()->resetQuery();
        
        return $this->db->table($this->table . ' p')
                       ->select('p.*, g.gelar')
                       ->join('tbl_m_gelar g', 'g.id = p.id_gelar', 'left')
                       ->where('p.status_hps', '0')
                       ->get($limit, $offset)
                       ->getResult();
    }

    /**
     * Implement soft delete
     */
    public function delete($id = null, bool $purge = false)
    {
        if ($id === null) {
            return false;
        }

        // Use raw SQL to avoid Time class
        $sql = "UPDATE {$this->table} 
                SET status_hps = '1', 
                    deleted_at = NOW() 
                WHERE id = ?";
                
        return $this->db->query($sql, [$id]);
    }

    /**
     * Count trashed records
     */
    public function countTrash(): int
    {
        return $this->where('status_hps', '1')->countAllResults();
    }

    /**
     * Get paginated trashed records
     */
    public function paginateTrash(int $perPage, int $currentPage = 1)
    {
        // Reset any existing query
        $this->builder()->resetQuery();

        // Initialize pager
        $this->pager = service('pager');
        
        $builder = $this->db->table($this->table . ' p')
                           ->select('p.*, g.gelar')
                           ->join('tbl_m_gelar g', 'g.id = p.id_gelar', 'left')
                           ->where('p.status_hps', '1')
                           ->orderBy('p.id', 'DESC');

        // Get total count
        $total = $builder->countAllResults(false);

        // Calculate offset
        $offset = ($currentPage - 1) * $perPage;

        // Get paginated results
        $data = $builder->get($perPage, $offset)->getResult();

        // Set up pagination
        $this->pager->makeLinks($currentPage, $perPage, $total, 'adminlte_pagination', 0, 'pasien');

        return $data;
    }

    /**
     * Generate new patient code
     */
    public function generateKode()
    {
        $prefix     = '';
        $yearMonth  = date('ym'); // Get current year and month (2501 for 2025-01)
        $random     = sprintf('%02d', rand(1, 99)); // Generate 4 digit random number

        // Get last order number for current month
        $lastKode = $this->db->table($this->table)
            ->select('COUNT(created_at) AS kode')
            ->where('YEAR(created_at)', date('Y')) // Match year part
            ->where('MONTH(created_at)', date('m')) // Match month part
            ->orderBy('created_at', 'DESC')
            ->limit(1)
            ->get()
            ->getRow();

        if ($lastKode) {
            // Extract the last 3 digits (order number)
            $lastOrder  = $lastKode->kode; // (int) substr($lastKode->kode, -2);
            $newOrder   = $lastOrder + 1;
        } else {
            $newOrder   = 1;
        }

        // Format order number to 3 digits with leading zeros
        $orderNumber = str_pad($newOrder, 3, '0', STR_PAD_LEFT);

        // Combine all parts
        return $prefix . $yearMonth . $orderNumber . $random;
    }

    /**
     * Restore trashed record
     */
    public function restore($id)
    {
        if ($id === null) {
            return false;
        }

        // Use raw SQL to avoid Time class
        $sql = "UPDATE {$this->table} 
                SET status_hps = '0', 
                    deleted_at = NULL 
                WHERE id = ?";
                
        return $this->db->query($sql, [$id]);
    }

    /**
     * Check if patient has associated user account
     */
    public function hasUserAccount($id_user)
    {
        if (!$id_user) {
            return false;
        }

        $db = \Config\Database::connect();
        return $db->table('tbl_ion_users')
                 ->where('id', $id_user)
                 ->countAllResults() > 0;
    }
} 