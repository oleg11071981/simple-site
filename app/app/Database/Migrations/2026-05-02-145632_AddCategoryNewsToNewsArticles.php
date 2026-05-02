<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddCategoryNewsToNewsArticles extends Migration
{
    public function up()
    {
        $this->forge->addColumn('n_news_articles', [
            'category_news' => [
                'type'       => 'TINYINT',
                'constraint' => 2,
                'null'       => false,
                'default'    => 1,
                'after'      => 'type',
                'comment'    => 'Категория новости: 1-Новости комитета, 2-Новости в РФ и мире',
            ],
        ]);

        // Добавляем индекс
        $this->forge->addKey('category_news', false, false, 'idx_category_news');
    }

    public function down()
    {
        $this->forge->dropColumn('n_news_articles', 'category_news');
    }
}