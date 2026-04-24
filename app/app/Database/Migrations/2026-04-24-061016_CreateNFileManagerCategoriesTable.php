<?php

/**
 * Миграция для создания таблицы категорий файлов n_file_manager_categories
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

class CreateNFileManagerCategoriesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
                'comment'        => 'Уникальный идентификатор категории',
            ],
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'comment'    => 'Название категории',
            ],
            'create' => [
                'type'       => 'DATETIME',
                'null'       => true,
                'comment'    => 'Дата создания',
            ],
            'modify' => [
                'type'       => 'DATETIME',
                'null'       => true,
                'comment'    => 'Дата изменения',
            ],
            'modify_by_user' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => false,
                'default'    => 0,
                'comment'    => 'ID пользователя, который изменил',
            ],
            'create_by_user' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => false,
                'default'    => 0,
                'comment'    => 'ID пользователя, который создал',
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('name', false, false, 'idx_name');
        $this->forge->addKey('create_by_user', false, false, 'idx_create_by_user');
        $this->forge->addKey('create', false, false, 'idx_create');

        $this->forge->createTable('n_file_manager_categories', true);
    }

    public function down()
    {
        $this->forge->dropTable('n_file_manager_categories', true);
    }
}