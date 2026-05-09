<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateNProjectsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
                'comment'        => 'Уникальный идентификатор проекта',
            ],
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => false,
                'comment'    => 'Название проекта',
            ],
            'path' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'comment'    => 'URL-путь (ЧПУ)',
            ],
            'anons_text' => [
                'type'       => 'TEXT',
                'null'       => true,
                'comment'    => 'Краткое описание проекта',
            ],
            'organizing_committee' => [
                'type'       => 'TEXT',
                'null'       => true,
                'comment'    => 'Оргкомитет',
            ],
            'supported_by' => [
                'type'       => 'TEXT',
                'null'       => true,
                'comment'    => 'Проводится при поддержке',
            ],
            'foto' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'default'    => 0,
                'comment'    => 'ID главного изображения',
            ],
            'media' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'default'    => 0,
                'comment'    => 'ID галереи (связь с категорией файлов)',
            ],
            'publish' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
                'comment'    => 'Статус публикации (0-черновик, 1-опубликовано)',
            ],
            'priority' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
                'comment'    => 'Порядок сортировки (чем меньше, тем выше)',
            ],
            'date_start' => [
                'type'       => 'DATE',
                'null'       => true,
                'comment'    => 'Дата начала проекта',
            ],
            'date_end' => [
                'type'       => 'DATE',
                'null'       => true,
                'comment'    => 'Дата окончания проекта',
            ],
            'keywords' => [
                'type'       => 'TEXT',
                'null'       => true,
                'comment'    => 'SEO ключевые слова',
            ],
            'description' => [
                'type'       => 'TEXT',
                'null'       => true,
                'comment'    => 'SEO мета-описание',
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
                'default'    => 0,
                'comment'    => 'ID пользователя, который создал',
            ],
            'modify_by_user' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
                'comment'    => 'ID пользователя, который изменил',
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('path', false, false, 'idx_path');
        $this->forge->addKey('publish', false, false, 'idx_publish');
        $this->forge->addKey('priority', false, false, 'idx_priority');
        $this->forge->addKey('date_start', false, false, 'idx_date_start');

        $this->forge->createTable('n_projects', true);
    }

    public function down()
    {
        $this->forge->dropTable('n_projects', true);
    }
}