<?php
/**
 * Migration to create API tokens table
 *
 * @author    Mikhael Felian Waskito <mikhaelfelian@gmail.com>
 * @created   2025-04-30
 */

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateApiTokensTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'provider' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'comment'    => 'API provider name (e.g., apify, google, etc.)',
            ],
            'token' => [
                'type'       => 'TEXT',
                'comment'    => 'API token or key',
            ],
            'description' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'comment'    => 'Optional description for the token',
            ],
            'is_active' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
                'comment'    => '1 = active, 0 = inactive',
            ],
            'created_at' => [
                'type'       => 'DATETIME',
                'null'       => true,
            ],
            'updated_at' => [
                'type'       => 'DATETIME',
                'null'       => true,
            ],
            'deleted_at' => [
                'type'       => 'DATETIME',
                'null'       => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('provider');
        $this->forge->createTable('tbl_api_tokens');
        
        // Insert the initial APIFY token
        $this->db->table('tbl_api_tokens')->insert([
            'provider'   => 'apify',
            'token'      => 'apify_api_OxNjPCt40Mf6Wuie3LimiXzmatDLNV1zG5tI',
            'description' => 'Default APIFY API token for X.com scraping',
            'is_active'  => 1,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);
    }

    public function down()
    {
        $this->forge->dropTable('tbl_api_tokens');
    }
} 