<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddMoreInfoToNProjects extends Migration
{
    public function up()
    {
        $fields = [
            'more_info' => [
                'type'    => 'LONGTEXT',
                'null'    => true,
                'after'   => 'anons_text',
                'comment' => 'Подробное описание проекта (HTML)',
            ],
            'more_info_en' => [
                'type'    => 'LONGTEXT',
                'null'    => true,
                'after'   => 'anons_text_en',
                'comment' => 'Подробное описание проекта на английском (HTML)',
            ],
        ];

        $this->forge->addColumn('n_projects', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('n_projects', ['more_info', 'more_info_en']);
    }
}
