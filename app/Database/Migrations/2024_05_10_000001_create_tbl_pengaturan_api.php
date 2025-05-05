<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Migration_2024_05_10_000001_create_pengaturan_api_table extends Migration
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
            'id_pengaturan' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'null'           => false,
            ],
            'name' => [
                'type'           => 'VARCHAR',
                'constraint'     => 100,
                'null'           => false,
            ],
            'tokens' => [
                'type'           => 'TEXT',
                'null'           => true,
            ],
            'created_date' => [
                'type'           => 'DATETIME',
                'null'           => true,
                'default'        => null,
            ],
            'created_at' => [
                'type'           => 'DATETIME',
                'null'           => true,
                'default'        => null,
            ],
            'updated_at' => [
                'type'           => 'DATETIME',
                'null'           => true,
                'default'        => null,
            ],
            'deleted_at' => [
                'type'           => 'DATETIME',
                'null'           => true,
                'default'        => null,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('tbl_pengaturan_api');
    }

    public function down()
    {
        $this->forge->dropTable('tbl_pengaturan_api');
    }
} 