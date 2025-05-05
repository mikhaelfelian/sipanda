<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSearchHistoryTable20240510000002 extends Migration
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
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'null'           => false,
            ],
            'search_type' => [
                'type'           => 'VARCHAR',
                'constraint'     => 50,
                'null'           => false,
                'comment'        => 'Type of search: web, social, image, etc',
            ],
            'search_query' => [
                'type'           => 'VARCHAR',
                'constraint'     => 255,
                'null'           => false,
            ],
            'search_engine' => [
                'type'           => 'VARCHAR',
                'constraint'     => 50,
                'null'           => false,
                'comment'        => 'google, twitter, instagram, etc',
            ],
            'result_count' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'null'           => true,
                'default'        => 0,
            ],
            'search_date' => [
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
        $this->forge->addKey(['user_id', 'search_date']);
        $this->forge->addKey('search_type');
        $this->forge->addKey('search_engine');
        $this->forge->createTable('tbl_search_history');
    }

    public function down()
    {
        $this->forge->dropTable('tbl_search_history');
    }
} 