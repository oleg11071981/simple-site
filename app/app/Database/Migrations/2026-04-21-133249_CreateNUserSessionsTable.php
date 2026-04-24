<?php

/**
 * Миграция для создания таблицы сессий n_user_sessions
 *
 * Таблица для хранения сессий пользователей в базе данных.
 * Обеспечивает безопасное хранение и управление сессиями.
 *
 * @package App\Database\Migrations
 * @category Migrations
 * @author  Your Name
 * @license MIT
 * @link    http://localhost
 * @noinspection PhpUnused
 */

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Миграция для создания таблицы сессий
 */
class CreateNUserSessionsTable extends Migration
{
    /**
     * Создает таблицу n_user_sessions
     *
     * @return void
     */
    public function up()
    {
        // Поля таблицы сессий
        $this->forge->addField([
            'id' => [
                'type'       => 'VARCHAR',
                'constraint' => 128,
                'comment'    => 'Уникальный идентификатор сессии',
            ],
            'ip_address' => [
                'type'       => 'VARCHAR',
                'constraint' => 45,
                'comment'    => 'IP-адрес пользователя',
            ],
            'timestamp' => [
                'type'       => 'INT',
                'constraint' => 10,
                'unsigned'   => true,
                'default'    => 0,
                'comment'    => 'Временная метка (Unix timestamp)',
            ],
            'data' => [
                'type'       => 'BLOB',
                'comment'    => 'Данные сессии (сериализованный массив)',
            ],
        ]);

        // Добавляем первичный ключ
        $this->forge->addKey('id', true);

        // Добавляем индекс для ускорения очистки устаревших сессий
        $this->forge->addKey('timestamp', false, false, 'idx_timestamp');

        // Создаем таблицу
        $this->forge->createTable('n_user_sessions', true);
    }

    /**
     * Удаляет таблицу n_user_sessions
     *
     * @return void
     */
    public function down()
    {
        $this->forge->dropTable('n_user_sessions', true);
    }
}