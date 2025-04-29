<?php
/**
 * Created by:
 * Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * 2025-02-21
 * 
 * MedTransFile Controller
 * Handles medical record file upload operations
 */

namespace App\Controllers\Medrecords;

use App\Controllers\BaseController;
use App\Models\MedTransFileModel;
use App\Models\MedTransModel;

class MedTransFile extends BaseController
{
    protected $medTransFileModel;
    protected $medTransModel;

    public function __construct()
    {
        $this->medTransFileModel = new MedTransFileModel();
        $this->medTransModel = new MedTransModel();
    }

    /**
     * Handle file upload for medical records
     * 
     * @param int $id Medical record ID
     * @return \CodeIgniter\HTTP\Response
     */
    public function upload($id)
    {
        try {
            // Validate medical record exists
            $medrec = $this->medTransModel->getTransById($id);
            if (!$medrec) {
                throw new \Exception('Medical record not found');
            }

            // Get uploaded file
            $file = $this->request->getFile('file');
            if (!$file->isValid()) {
                throw new \Exception('Invalid file upload');
            }

            // Validate file size (max 5MB)
            if ($file->getSize() > 5242880) {
                throw new \Exception('File size exceeds 5MB limit');
            }

            // Validate file type
            $allowedTypes = ['image/jpeg', 'image/png', 'application/pdf'];
            if (!in_array($file->getMimeType(), $allowedTypes)) {
                throw new \Exception('Invalid file type. Only JPG, PNG, and PDF files are allowed');
            }

            // Set up file path
            $no_rm = $medrec->no_pasien;
            $filePath = 'public/file/pasien/'.$no_rm.'/';

            // Create directory if it doesn't exist
            if (!is_dir($filePath)) {
                mkdir($filePath, 0777, true);
            }

            // Generate unique filename
            $fileName = date('YmdHis') . '_' . $file->getRandomName();

            // Move file to upload directory
            if (!$file->move($filePath, $fileName)) {
                throw new \Exception('Failed to move uploaded file');
            }

            // Prepare data for database
            $data = [
                'id_medrecs'    => $id,
                'id_pasien'     => $medrec->id_pasien,
                'id_user'       => $this->ionAuth->user()->row()->id,
                'judul'         => $this->request->getPost('judul'),
                'keterangan'    => $this->request->getPost('keterangan'),
                'file_name_ori' => $file->getClientName(),
                'file_name'     => $fileName,
                'file_ext'      => $file->getExtension(),
                'file_type'     => $file->getMimeType(),
                'status'        => $this->request->getPost('status') ?? '1',
                'sp'            => '0'
            ];

            // Save to database
            $this->medTransFileModel->insert($data);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'File uploaded successfully',
                'data' => [
                    'filename' => $fileName,
                    'original_name' => $file->getClientName(),
                    'path' => $filePath . $fileName
                ]
            ]);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to upload file: ' . $e->getMessage()
            ]);
        }
    }
} 