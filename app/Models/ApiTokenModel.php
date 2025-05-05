<?php
/**
 * ApiTokenModel
 * 
 * This model handles the API tokens used for various external services.
 * It provides methods to retrieve, store, and manage API tokens.
 *
 * @author    Mikhael Felian Waskito <mikhaelfelian@gmail.com>
 * @created   2025-04-30
 */

namespace App\Models;

use CodeIgniter\Model;

class ApiTokenModel extends Model
{
    /**
     * Table name
     *
     * @var string
     */
    protected $table            = 'tbl_pengaturan_api';
    
    /**
     * Primary key field
     *
     * @var string
     */
    protected $primaryKey       = 'id';
    
    /**
     * Use auto increment for primary key
     *
     * @var bool
     */
    protected $useAutoIncrement = true;
    
    /**
     * Return type of model methods
     *
     * @var string
     */
    protected $returnType       = 'object';
    
    /**
     * Enable soft deletes
     *
     * @var bool
     */
    protected $useSoftDeletes   = true;
    
    /**
     * Enable field protection
     *
     * @var bool
     */
    protected $protectFields    = true;
    
    /**
     * Fields that can be mass assigned
     *
     * @var array
     */
    protected $allowedFields    = [
        'id_pengaturan', 'name', 'tokens'
    ];

    /**
     * Enable timestamps
     *
     * @var bool
     */
    protected $useTimestamps = true;
    
    /**
     * Date format
     *
     * @var string
     */
    protected $dateFormat    = 'datetime';
    
    /**
     * Created at field name
     *
     * @var string
     */
    protected $createdField  = 'created_at';
    
    /**
     * Updated at field name
     *
     * @var string
     */
    protected $updatedField  = 'updated_at';
    
    /**
     * Deleted at field name
     *
     * @var string
     */
    protected $deletedField  = 'deleted_at';

    /**
     * Get active token for a provider
     * 
     * @param string $provider Provider name
     * @return string|null Token string or null if not found
     */
    public function getActiveToken($provider)
    {
        $result = $this->where('name', $provider)
                       ->where('deleted_at IS NULL')
                       ->first();
        
        return $result ? $result->tokens : null;
    }
    
    /**
     * Store or update token for a provider
     * 
     * @param string $provider Provider name
     * @param string $token Token string
     * @param string $description Optional description for the token
     * @return bool Success status
     */
    public function storeToken($provider, $token, $description = null)
    {
        // Check if token exists
        $exists = $this->where('name', $provider)->countAllResults() > 0;
        
        // Get default pengaturan ID
        $pengaturanId = 1; // Default ID
        
        // Data to insert/update
        $data = [
            'id_pengaturan' => $pengaturanId,
            'name' => $provider,
            'tokens' => $token,
            'created_date' => date('Y-m-d H:i:s')
        ];
        
        if ($exists) {
            // Update the existing token
            return $this->where('name', $provider)->set($data)->update();
        } else {
            // Insert new token
            return $this->insert($data) !== false;
        }
    }
    
    /**
     * Deactivate token for a provider
     * 
     * @param string $provider Provider name
     * @return bool Success status
     */
    public function deactivateToken($provider)
    {
        return $this->where('name', $provider)
                    ->delete();
    }
    
    /**
     * Get all tokens for a provider
     * 
     * @param string $provider Provider name
     * @return array Tokens
     */
    public function getTokensByProvider($provider)
    {
        return $this->where('name', $provider)
                    ->findAll();
    }
    
    /**
     * Get all providers with active tokens
     * 
     * @return array Providers
     */
    public function getActiveProviders()
    {
        $results = $this->select('name')
                        ->where('deleted_at IS NULL')
                        ->findAll();
        
        return array_column((array)$results, 'name');
    }
} 