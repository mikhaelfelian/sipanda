<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ApiTokensSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'id_pengaturan' => 1,
                'name'         => 'apify',
                'tokens'       => 'apify_api_OxNjPCt40Mf6Wuie3LimiXzmatDLNV1zG5tI',
                'created_date' => date('Y-m-d H:i:s'),
                'created_at'   => date('Y-m-d H:i:s'),
                'updated_at'   => date('Y-m-d H:i:s')
            ],
            [
                'id_pengaturan' => 1,
                'name'         => 'serpapi',
                'tokens'       => 'YOUR_SERPAPI_KEY_HERE',
                'created_date' => date('Y-m-d H:i:s'),
                'created_at'   => date('Y-m-d H:i:s'),
                'updated_at'   => date('Y-m-d H:i:s')
            ],
            [
                'id_pengaturan' => 1,
                'name'         => 'openai',
                'tokens'       => 'YOUR_OPENAI_API_KEY_HERE',
                'created_date' => date('Y-m-d H:i:s'),
                'created_at'   => date('Y-m-d H:i:s'),
                'updated_at'   => date('Y-m-d H:i:s')
            ]
        ];

        // Check if data exists
        $exists = $this->db->table('tbl_pengaturan_api')->where('name', 'apify')->get()->getRow();
        
        if (!$exists) {
            $this->db->table('tbl_pengaturan_api')->insertBatch($data);
        }
    }
} 