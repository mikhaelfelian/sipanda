<?php
/**
 * Dashboard Controller
 * 
 * Created by Mikhael Felian Waskito
 * Created at 2024-01-09
 */

namespace App\Controllers;
use App\Models\MedTransModel;

class Dashboard extends BaseController
{
    protected $medTransModel;

    public function __construct()
    {
        $this->medTransModel = new MedTransModel();
    }
    public function index()
    {
        // Check if user is logged in
        if (!$this->ionAuth->loggedIn()) {
            return redirect()->to('/auth/login');
        }
        
        $data = [
            'title'         => 'Dashboard',
            'Pengaturan'    => $this->pengaturan,
            'user'          => $this->ionAuth->user()->row(),
            'isMenuActive'  => isMenuActive('dashboard') ? 'active' : '',
            'total_users'   => 1
        ];

        return view($this->theme->getThemePath() . '/dashboard', $data);
    }

    public function test($id)
    {
        $medrec = $this->medTransModel->getTransById($id);
        pre($medrec);
    }
} 