<?php

/**
 * Seeder для начальных настроек сайта
 *
 * Заполняет таблицу n_siteconfig базовыми параметрами
 *
 * @package App\Database\Seeds
 * @category Seeds
 * @author  Your Name
 * @license MIT
 * @link    http://localhost
 * @noinspection PhpUnused
 */

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Seeder для таблицы настроек сайта
 */
class SiteconfigSeeder extends Seeder
{
    /**
     * Добавляет начальные настройки
     *
     * @return void
     */
    public function run()
    {
        // Базовые настройки сайта
        $data = [
            [
                'parameter' => 'SiteName',
                'value'     => 'Мой сайт на n-cms',
            ],
            [
                'parameter' => 'AdminEmail',
                'value'     => 'admin@example.com',
            ],
            [
                'parameter' => 'Keywords',
                'value'     => 'cms, сайт, управление контентом',
            ],
            [
                'parameter' => 'Description',
                'value'     => 'Сайт на системе управления n-cms',
            ],
            [
                'parameter' => 'Slogan',
                'value'     => 'Добро пожаловать на наш сайт!',
            ],
            [
                'parameter' => 'MainText',
                'value'     => '<p>Добро пожаловать на наш сайт. Здесь вы найдете много интересного.</p>',
            ],
            [
                'parameter' => 'foto',
                'value'     => '0',
            ],
            [
                'parameter' => 'Counters',
                'value'     => '',
            ],
            [
                'parameter' => 'Email',
                'value'     => 'info@example.com',
            ],
            [
                'parameter' => 'Adress',
                'value'     => 'Москва, ул. Примерная, д. 1',
            ],
            [
                'parameter' => 'Phone',
                'value'     => '+7 (999) 123-45-67',
            ],
            [
                'parameter' => 'WorkSchedule',
                'value'     => 'Пн-Пт 9:00 - 18:00',
            ],
            [
                'parameter' => 'additional_field1',
                'value'     => null,
            ],
            [
                'parameter' => 'additional_field2',
                'value'     => null,
            ],
        ];

        // Вставляем данные
        foreach ($data as $item) {
            $this->db->table('n_siteconfig')->insert($item);
        }

        echo "\n✅ Начальные настройки сайта добавлены:\n";
        echo "   - SiteName: Мой сайт на n-cms\n";
        echo "   - AdminEmail: admin@example.com\n";
        echo "   - Email: info@example.com\n";
        echo "   - Phone: +7 (999) 123-45-67\n\n";
    }
}