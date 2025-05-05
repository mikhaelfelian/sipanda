<?php
/**
 * ApiTokens Controller
 * 
 * This controller manages API tokens for various external services.
 * It provides functionality to view, add, edit, and delete API tokens.
 *
 * @author    Mikhael Felian Waskito <mikhaelfelian@gmail.com>
 * @created   2025-04-30
 */

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ApiTokenModel;

class ApiTokens extends BaseController
{
    /**
     * API Token model
     *
     * @var ApiTokenModel
     */
    protected $tokenModel;
    
    /**
     * Constructor
     */
    public function __construct()
    {
        // Initialize the API Token model
        $this->tokenModel = new ApiTokenModel();
        
        // Load helpers
        helper(['form', 'url', 'theme']);
    }
    
    /**
     * Index page for API Tokens
     */
    public function index()
    {
        $data = [
            'title' => 'API Tokens',
            'active_menu' => 'api_tokens',
            'Pengaturan' => $this->pengaturan,
            'user' => $this->ionAuth->user()->row(),
            'tokens' => $this->tokenModel->findAll(),
            'isMenuActive' => isMenuActive('pengaturan/api-tokens') ? 'active' : ''
        ];
        
        return view($this->theme->getThemePath() . '/pengaturan/api_tokens/index', $data);
    }
    
    /**
     * Add a new API token
     */
    public function add()
    {
        $data = [
            'title' => 'Add API Token',
            'active_menu' => 'api_tokens',
            'Pengaturan' => $this->pengaturan,
            'user' => $this->ionAuth->user()->row(),
            'validation' => \Config\Services::validation(),
            'isMenuActive' => isMenuActive('pengaturan/api-tokens') ? 'active' : ''
        ];
        
        // If form is submitted
        if ($this->request->getMethod() === 'post') {
            // Set validation rules
            $rules = [
                'provider' => 'required|alpha_dash|max_length[50]',
                'token' => 'required',
                'description' => 'permit_empty|max_length[255]'
            ];
            
            // Run validation
            if ($this->validate($rules)) {
                // Get form data
                $provider = $this->request->getPost('provider');
                $token = $this->request->getPost('token');
                $description = $this->request->getPost('description');
                
                // Store token
                $success = $this->tokenModel->storeToken($provider, $token, $description);
                
                if ($success) {
                    return redirect()->to('pengaturan/api-tokens')
                        ->with('success', 'API token added successfully.');
                } else {
                    return redirect()->back()
                        ->with('error', 'Failed to add API token.')
                        ->withInput();
                }
            } else {
                // Return to form with validation errors
                return view($this->theme->getThemePath() . '/pengaturan/api_tokens/form', $data);
            }
        }
        
        // Display form
        return view($this->theme->getThemePath() . '/pengaturan/api_tokens/form', $data);
    }
    
    /**
     * Edit an API token
     * 
     * @param int $id Token ID
     */
    public function edit($id = null)
    {
        if (empty($id)) {
            return redirect()->to('pengaturan/api-tokens')
                ->with('error', 'Invalid token ID.');
        }
        
        // Get token data
        $token = $this->tokenModel->find($id);
        
        if (empty($token)) {
            return redirect()->to('pengaturan/api-tokens')
                ->with('error', 'Token not found.');
        }
        
        $data = [
            'title' => 'Edit API Token',
            'active_menu' => 'api_tokens',
            'Pengaturan' => $this->pengaturan,
            'user' => $this->ionAuth->user()->row(),
            'validation' => \Config\Services::validation(),
            'token' => $token,
            'isMenuActive' => isMenuActive('pengaturan/api-tokens') ? 'active' : ''
        ];
        
        // If form is submitted
        if ($this->request->getMethod() === 'post') {
            // Set validation rules
            $rules = [
                'provider' => 'required|alpha_dash|max_length[50]',
                'token' => 'required',
                'description' => 'permit_empty|max_length[255]',
                'is_active' => 'permit_empty|in_list[0,1]'
            ];
            
            // Run validation
            if ($this->validate($rules)) {
                // Get form data
                $provider = $this->request->getPost('provider');
                $token = $this->request->getPost('token');
                $description = $this->request->getPost('description');
                $isActive = $this->request->getPost('is_active') ?? 0;
                
                // Update token
                $success = $this->tokenModel->update($id, [
                    'provider' => $provider,
                    'token' => $token,
                    'description' => $description,
                    'is_active' => $isActive
                ]);
                
                if ($success) {
                    return redirect()->to('pengaturan/api-tokens')
                        ->with('success', 'API token updated successfully.');
                } else {
                    return redirect()->back()
                        ->with('error', 'Failed to update API token.')
                        ->withInput();
                }
            } else {
                // Return to form with validation errors
                return view($this->theme->getThemePath() . '/pengaturan/api_tokens/form', $data);
            }
        }
        
        // Display form
        return view($this->theme->getThemePath() . '/pengaturan/api_tokens/form', $data);
    }
    
    /**
     * Delete an API token
     * 
     * @param int $id Token ID
     */
    public function delete($id = null)
    {
        if (empty($id)) {
            return redirect()->to('pengaturan/api-tokens')
                ->with('error', 'Invalid token ID.');
        }
        
        // Check if token exists
        $token = $this->tokenModel->find($id);
        
        if (empty($token)) {
            return redirect()->to('pengaturan/api-tokens')
                ->with('error', 'Token not found.');
        }
        
        // Delete token
        $success = $this->tokenModel->delete($id);
        
        if ($success) {
            return redirect()->to('pengaturan/api-tokens')
                ->with('success', 'API token deleted successfully.');
        } else {
            return redirect()->to('pengaturan/api-tokens')
                ->with('error', 'Failed to delete API token.');
        }
    }
    
    /**
     * Toggle API token status
     * 
     * @param int $id Token ID
     */
    public function toggle($id = null)
    {
        if (empty($id)) {
            return redirect()->to('pengaturan/api-tokens')
                ->with('error', 'Invalid token ID.');
        }
        
        // Get token data
        $token = $this->tokenModel->find($id);
        
        if (empty($token)) {
            return redirect()->to('pengaturan/api-tokens')
                ->with('error', 'Token not found.');
        }
        
        // Toggle status
        $newStatus = $token['is_active'] ? 0 : 1;
        
        $success = $this->tokenModel->update($id, [
            'is_active' => $newStatus
        ]);
        
        if ($success) {
            $status = $newStatus ? 'activated' : 'deactivated';
            return redirect()->to('pengaturan/api-tokens')
                ->with('success', 'API token ' . $status . ' successfully.');
        } else {
            return redirect()->to('pengaturan/api-tokens')
                ->with('error', 'Failed to update API token status.');
        }
    }
} 