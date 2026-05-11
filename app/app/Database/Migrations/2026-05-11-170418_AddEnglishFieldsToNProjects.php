<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddEnglishFieldsToNProjects extends Migration
{
    public function up()
    {
        $fields = [
            'name_en' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'after'      => 'name',
                'comment'    => 'Название проекта на английском',
            ],
            'anons_text_en' => [
                'type'       => 'TEXT',
                'null'       => true,
                'after'      => 'anons_text',
                'comment'    => 'Краткое описание на английском',
            ],
            'organizing_committee_en' => [
                'type'       => 'TEXT',
                'null'       => true,
                'after'      => 'organizing_committee',
                'comment'    => 'Оргкомитет на английском',
            ],
            'supported_by_en' => [
                'type'       => 'TEXT',
                'null'       => true,
                'after'      => 'supported_by',
                'comment'    => 'При поддержке на английском',
            ],
            'keywords_en' => [
                'type'       => 'TEXT',
                'null'       => true,
                'after'      => 'keywords',
                'comment'    => 'SEO keywords на английском',
            ],
            'description_en' => [
                'type'       => 'TEXT',
                'null'       => true,
                'after'      => 'description',
                'comment'    => 'SEO description на английском',
            ],
        ];

        $this->forge->addColumn('n_projects', $fields);
    }

    public function down()
    {
        $fields = ['name_en', 'anons_text_en', 'organizing_committee_en', 'supported_by_en', 'keywords_en', 'description_en'];
        $this->forge->dropColumn('n_projects', $fields);
    }
}