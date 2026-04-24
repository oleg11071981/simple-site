<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Миграция для создания таблицы n_users
 *
 * Таблица хранит информацию о пользователях системы
 *
 * @package App\Database\Migrations
 * @noinspection PhpUnused
 */
class CreateNUsersTable extends Migration
{
    /**
     * Выполняет миграцию - создает таблицу n_users
     *
     * @return void
     */
    public function up()
    {
        // Добавляем поля таблицы
        $this->forge->addField([
            // Первичный ключ - уникальный идентификатор пользователя
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,      // Только положительные числа
                'auto_increment' => true,       // Автоматическое увеличение
                'comment'        => 'Уникальный идентификатор пользователя',
            ],

            // Имя пользователя (реальное имя, не логин)
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,           // Может быть пустым
                'comment'    => 'Имя пользователя (реальное имя)',
            ],

            // Логин для входа в систему
            'login' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'comment'    => 'Логин пользователя для авторизации',
            ],

            // Email пользователя
            'email' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'comment'    => 'Email адрес пользователя',
            ],

            // Пароль в зашифрованном виде
            'password' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'comment'    => 'Хеш пароля (хранится в зашифрованном виде)',
            ],

            // Описание/биография пользователя
            'description' => [
                'type'       => 'TEXT',
                'null'       => true,
                'comment'    => 'Описание профиля или биография пользователя',
            ],

            // Сайт пользователя
            'site' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'comment'    => 'Личный сайт или блог пользователя',
            ],

            // ID медиафайла (аватарка)
            'media' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
                'comment'    => 'ID медиафайла (аватар пользователя)',
            ],

            // Статус публикации
            'publish' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,              // По умолчанию скрыт
                'comment'    => 'Статус публикации (0 - скрыт, 1 - опубликован)',
            ],

            // Тип пользователя
            'type' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,              // По умолчанию обычный пользователь
                'comment'    => 'Тип пользователя (0 - обычный, 1 - администратор, 2 - модератор)',
            ],

            // Дата создания записи
            'create' => [
                'type'       => 'DATETIME',
                'null'       => true,
                'comment'    => 'Дата и время создания записи',
            ],

            // Дата изменения записи
            'modify' => [
                'type'       => 'DATETIME',
                'null'       => true,
                'comment'    => 'Дата и время последнего изменения записи',
            ],

            // ID пользователя, который создал запись
            'create_by_user' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => false,
                'comment'    => 'ID пользователя, который создал эту запись',
            ],

            // ID пользователя, который изменил запись
            'modify_by_user' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => false,
                'comment'    => 'ID пользователя, который последним изменил запись',
            ],
        ]);

        // Добавляем первичный ключ
        $this->forge->addKey('id', true);

        // Добавляем индексы для ускорения поиска
        $this->forge->addKey('login', false, false, 'idx_login');     // Индекс для поиска по логину
        $this->forge->addKey('email', false, false, 'idx_email');     // Индекс для поиска по email
        $this->forge->addKey('publish', false, false, 'idx_publish'); // Индекс для фильтрации по статусу
        $this->forge->addKey('type', false, false, 'idx_type');       // Индекс для фильтрации по типу

        // Создаем таблицу
        // IF NOT EXISTS - проверка существования таблицы
        $this->forge->createTable('n_users', true);
    }

    /**
     * Откатывает миграцию - удаляет таблицу n_users
     *
     * @return void
     */
    public function down()
    {
        // Удаляем таблицу, если она существует
        // IF EXISTS - проверка существования перед удалением
        $this->forge->dropTable('n_users', true);
    }
}