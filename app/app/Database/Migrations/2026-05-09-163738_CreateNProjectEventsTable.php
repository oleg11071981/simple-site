<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateNProjectEventsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
                'comment'        => 'Уникальный идентификатор мероприятия',
            ],
            'project_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'default'    => 0,
                'comment'    => 'ID проекта (связь с n_projects)',
            ],
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => false,
                'comment'    => 'Название мероприятия',
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
                'comment'    => 'Краткое описание мероприятия',
            ],
            'description' => [
                'type'       => 'LONGTEXT',
                'null'       => true,
                'comment'    => 'Полное описание мероприятия (HTML)',
            ],
            'foto' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'default'    => 0,
                'comment'    => 'ID главного изображения мероприятия',
            ],
            'media' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'default'    => 0,
                'comment'    => 'ID галереи мероприятия (связь с категорией файлов)',
            ],
            'date_start' => [
                'type'       => 'DATE',
                'null'       => true,
                'comment'    => 'Дата начала мероприятия',
            ],
            'date_end' => [
                'type'       => 'DATE',
                'null'       => true,
                'comment'    => 'Дата окончания мероприятия',
            ],
            'location' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'comment'    => 'Место проведения',
            ],
            'link' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'comment'    => 'Внешняя ссылка (если есть)',
            ],
            'priority' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
                'comment'    => 'Порядок сортировки внутри проекта',
            ],
            'publish' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
                'comment'    => 'Статус публикации (0-черновик, 1-опубликовано)',
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
        $this->forge->addKey('project_id', false, false, 'idx_project_id');
        $this->forge->addKey('path', false, false, 'idx_path');
        $this->forge->addKey('publish', false, false, 'idx_publish');
        $this->forge->addKey('priority', false, false, 'idx_priority');
        $this->forge->addKey('date_start', false, false, 'idx_date_start');

        $this->forge->createTable('n_project_events', true);
    }

    public function down()
    {
        $this->forge->dropTable('n_project_events', true);
    }
}