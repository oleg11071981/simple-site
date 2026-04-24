<?php

/** @noinspection PhpUnused */

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Seeder для добавления супер-администратора
 *
 * @noinspection PhpUnused
 */
class SuperAdministratorSeeder extends Seeder
{
    /**
     * Добавляет супер-администратора
     *
     * @return void
     */
    public function run()
    {
        // Проверяем, существует ли уже супер-администратор
        $existing = $this->db->table('n_users')
            ->where('login', 'superadministrator')
            ->get()
            ->getRow();

        if ($existing) {
            echo "\n⚠️  Супер-администратор уже существует! (ID: $existing->id)\n";
            return;
        }

        // Данные супер-администратора
        $data = [
            'name'           => 'Super Administrator',
            'login'          => 'superadministrator',     // Логин супер-администратора
            'email'          => 'superadmin@example.com',  // Email для связи
            'password'       => password_hash('123456', PASSWORD_DEFAULT),  // Хеш пароля 123456
            'description'    => 'Главный администратор системы с полными правами доступа',
            'site'           => '',
            'media'          => null,
            'publish'        => 1,                         // Активен
            'type'           => 1,                         // Тип: администратор
            'create'         => date('Y-m-d H:i:s'),       // Текущая дата и время
            'modify'         => date('Y-m-d H:i:s'),       // Текущая дата и время
            'create_by_user' => 0,                         // 0 = системная запись
            'modify_by_user' => 0,                         // 0 = системная запись
        ];

        // Вставляем данные
        $this->db->table('n_users')->insert($data);

        $id = $this->db->insertID();

        echo "\n✅ Супер-администратор успешно создан! (ID: $id)\n";
        echo "   Логин: superadministrator\n";
        echo "   Пароль: 123456\n";
        echo "   Email: superadmin@example.com\n";
        echo "   ⚠️  Обязательно измените пароль после первого входа в систему!\n\n";
    }
}