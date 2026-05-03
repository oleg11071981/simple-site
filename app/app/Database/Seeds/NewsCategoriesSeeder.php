<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class NewsCategoriesSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'name'     => 'Новости комитета',
                'slug'     => 'news-committee',
                'parent'   => 0,
                'priority' => 10,
                'create'   => date('Y-m-d H:i:s'),
                'modify'   => date('Y-m-d H:i:s'),
            ],
            [
                'name'     => 'Новости в РФ и мире',
                'slug'     => 'news-russia-world',
                'parent'   => 0,
                'priority' => 20,
                'create'   => date('Y-m-d H:i:s'),
                'modify'   => date('Y-m-d H:i:s'),
            ],
        ];

        foreach ($data as $item) {
            $this->db->table('n_news_categories')->insert($item);
        }

        echo "✅ Добавлены категории новостей:\n";
        echo "   - Новости комитета (ID: 1)\n";
        echo "   - Новости в РФ и мире (ID: 2)\n";
    }
}