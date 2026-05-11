<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddEnglishFieldsToNNewsArticles extends Migration
{
    public function up()
    {
        $fields = [
            'name_en' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'after'      => 'name',
                'comment'    => 'Заголовок на английском',
            ],
            'anons_text_en' => [
                'type'       => 'TEXT',
                'null'       => true,
                'after'      => 'anons_text',
                'comment'    => 'Анонс на английском',
            ],
            'more_info_en' => [
                'type'       => 'LONGTEXT',
                'null'       => true,
                'after'      => 'more_info',
                'comment'    => 'Полный текст на английском',
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

        $this->forge->addColumn('n_news_articles', $fields);
    }

    public function down()
    {
        $fields = ['name_en', 'anons_text_en', 'more_info_en', 'keywords_en', 'description_en'];
        $this->forge->dropColumn('n_news_articles', $fields);
    }
}