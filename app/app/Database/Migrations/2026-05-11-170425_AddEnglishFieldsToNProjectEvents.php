<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddEnglishFieldsToNProjectEvents extends Migration
{
    public function up()
    {
        $fields = [
            'name_en' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'after'      => 'name',
                'comment'    => 'Название мероприятия на английском',
            ],
            'anons_text_en' => [
                'type'       => 'TEXT',
                'null'       => true,
                'after'      => 'anons_text',
                'comment'    => 'Краткое описание на английском',
            ],
            'more_info_en' => [
                'type'       => 'LONGTEXT',
                'null'       => true,
                'after'      => 'more_info',
                'comment'    => 'Полное описание на английском',
            ],
            'location_en' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'after'      => 'location',
                'comment'    => 'Место проведения на английском',
            ],
        ];

        $this->forge->addColumn('n_project_events', $fields);
    }

    public function down()
    {
        $fields = ['name_en', 'anons_text_en', 'more_info_en', 'location_en'];
        $this->forge->dropColumn('n_project_events', $fields);
    }
}