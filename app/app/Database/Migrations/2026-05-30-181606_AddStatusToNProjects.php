<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddStatusToNProjects extends Migration
{
    public function up()
    {
        // Добавляем поле status, если его нет
        if (!$this->db->fieldExists('status', 'n_projects')) {
            $this->forge->addColumn('n_projects', [
                'status' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 20,
                    'default'    => 'active',
                    'after'      => 'publish',
                    'comment'    => 'Статус проекта: active - активный, completed - завершённый',
                ],
            ]);
        }
    }

    public function down()
    {
        // Удаляем поле status, если оно существует
        if ($this->db->fieldExists('status', 'n_projects')) {
            $this->forge->dropColumn('n_projects', 'status');
        }
    }
}