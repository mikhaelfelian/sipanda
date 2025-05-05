<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Migration_2024_03_21_000000_create_tbl_keywords extends Migration
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
            'user_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'keyword' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'search_count' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 1,
            ],
            'last_searched' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('user_id');
        $this->forge->addKey('keyword');
        $this->forge->createTable('tbl_keywords');
    }

    public function down()
    {
        $this->forge->dropTable('tbl_keywords');
    }
} 