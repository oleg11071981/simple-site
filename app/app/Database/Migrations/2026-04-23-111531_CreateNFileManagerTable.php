<?php

/**
 * Миграция для создания таблицы файлового менеджера n_file_manager
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

class CreateNFileManagerTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
                'comment'        => 'Уникальный идентификатор файла',
            ],
            'file_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'comment'    => 'Оригинальное имя файла',
            ],
            'file_type' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'comment'    => 'Тип файла (jpg, png, pdf и т.д.)',
            ],
            'mime_type' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'comment'    => 'MIME тип файла',
            ],
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'comment'    => 'Название/заголовок файла',
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
            'category' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => false,
                'default'    => 0,
                'comment'    => 'ID категории',
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
            'width' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => false,
                'default'    => 0,
                'comment'    => 'Ширина изображения',
            ],
            'height' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => false,
                'default'    => 0,
                'comment'    => 'Высота изображения',
            ],
            'file_size' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => false,
                'default'    => 0,
                'comment'    => 'Размер файла в байтах',
            ],
            'thumb_width' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => false,
                'default'    => 0,
                'comment'    => 'Ширина превью',
            ],
            'thumb_height' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => false,
                'default'    => 0,
                'comment'    => 'Высота превью',
            ],
            'thumb_file_size' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => false,
                'default'    => 0,
                'comment'    => 'Размер превью в байтах',
            ],
            'foto' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => false,
                'default'    => 0,
                'comment'    => 'Связь с фото',
            ],
            'cnt' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => false,
                'default'    => 0,
                'comment'    => 'Счетчик скачиваний',
            ],
            'title' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'comment'    => 'Подпись к изображению',
            ],
            'priority' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
                'default'    => 0,
                'comment'    => 'Приоритет сортировки',
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('file_type', false, false, 'idx_file_type');
        $this->forge->addKey('category', false, false, 'idx_category');
        $this->forge->addKey('create', false, false, 'idx_create');
        $this->forge->addKey('modify', false, false, 'idx_modify');
        $this->forge->addKey('create_by_user', false, false, 'idx_create_by_user');

        $this->forge->createTable('n_file_manager', true);
    }

    public function down()
    {
        $this->forge->dropTable('n_file_manager', true);
    }
}