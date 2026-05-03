<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateNNewsCategoriesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
                'comment'        => 'Уникальный идентификатор',
            ],
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => false,
                'comment'    => 'Название категории',
            ],
            'slug' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'comment'    => 'URL-псевдоним',
            ],
            'parent' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'default'    => 0,
                'comment'    => 'Родительская категория (0 - корневая)',
            ],
            'description' => [
                'type'       => 'TEXT',
                'null'       => true,
                'comment'    => 'Описание категории',
            ],
            'priority' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'default'    => 0,
                'comment'    => 'Порядок сортировки',
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
            'create_by_user' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => false,
                'default'    => 0,
                'comment'    => 'Кто создал',
            ],
            'modify_by_user' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => false,
                'default'    => 0,
                'comment'    => 'Кто изменил',
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('parent', false, false, 'idx_parent');
        $this->forge->addKey('slug', false, false, 'idx_slug');
        $this->forge->addKey('priority', false, false, 'idx_priority');

        $this->forge->createTable('n_news_categories', true);
    }

    public function down()
    {
        $this->forge->dropTable('n_news_categories', true);
    }
}