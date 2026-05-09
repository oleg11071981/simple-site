<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RenameDescriptionToMoreInfoInProjectEvents extends Migration
{
    public function up()
    {
        // Проверяем, существует ли поле description
        if ($this->db->fieldExists('description', 'n_project_events')) {
            // Переименовываем поле description в more_info
            $this->forge->modifyColumn('n_project_events', [
                'description' => [
                    'name'       => 'more_info',
                    'type'       => 'LONGTEXT',
                    'null'       => true,
                    'comment'    => 'Полное описание мероприятия (HTML)',
                ],
            ]);
        }
    }

    public function down()
    {
        // Откат: переименовываем обратно
        if ($this->db->fieldExists('more_info', 'n_project_events')) {
            $this->forge->modifyColumn('n_project_events', [
                'more_info' => [
                    'name'       => 'description',
                    'type'       => 'TEXT',
                    'null'       => true,
                    'comment'    => 'Описание мероприятия',
                ],
            ]);
        }
    }
}