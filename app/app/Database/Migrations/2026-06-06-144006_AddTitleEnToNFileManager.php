<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddTitleEnToNFileManager extends Migration
{
    public function up()
    {
        if (!$this->db->fieldExists('title_en', 'n_file_manager')) {
            $this->forge->addColumn('n_file_manager', [
                'title_en' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 255,
                    'null'       => true,
                    'after'      => 'title',
                    'comment'    => 'Подпись к изображению на английском',
                ],
            ]);
        }
    }

    public function down()
    {
        if ($this->db->fieldExists('title_en', 'n_file_manager')) {
            $this->forge->dropColumn('n_file_manager', 'title_en');
        }
    }
}