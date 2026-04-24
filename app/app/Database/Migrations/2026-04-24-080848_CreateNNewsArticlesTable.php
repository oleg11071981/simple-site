<?php

/**
 * Миграция для создания таблицы новостей n_news_articles
 *
 * @package App\Database\Migrations
 * @noinspection PhpUnused
 */

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateNNewsArticlesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
                'comment'        => 'Уникальный идентификатор новости',
            ],
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => false,
                'comment'    => 'Заголовок новости',
            ],
            'anons_text' => [
                'type'       => 'TEXT',
                'null'       => false,
                'comment'    => 'Краткий текст анонса',
            ],
            'more_info' => [
                'type'       => 'LONGTEXT',
                'null'       => false,
                'comment'    => 'Полный текст новости',
            ],
            'publish' => [
                'type'       => 'TINYINT',
                'constraint' => 2,
                'null'       => false,
                'default'    => 0,
                'comment'    => 'Статус публикации (0-черновик, 1-опубликовано)',
            ],
            'date' => [
                'type'       => 'DATE',
                'null'       => false,
                'comment'    => 'Дата новости',
            ],
            'create' => [
                'type'       => 'DATETIME',
                'null'       => false,
                'comment'    => 'Дата создания записи',
            ],
            'modify' => [
                'type'       => 'DATETIME',
                'null'       => false,
                'comment'    => 'Дата последнего изменения',
            ],
            'path' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'comment'    => 'URL-путь новости',
            ],
            'create_by_user' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => false,
                'default'    => 1,
                'comment'    => 'ID пользователя, создавшего запись',
            ],
            'modify_by_user' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => false,
                'default'    => 1,
                'comment'    => 'ID пользователя, изменившего запись',
            ],
            'keywords' => [
                'type'       => 'TEXT',
                'null'       => true,
                'comment'    => 'SEO ключевые слова',
            ],
            'description' => [
                'type'       => 'TEXT',
                'null'       => true,
                'comment'    => 'SEO описание',
            ],
            'author' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'comment'    => 'Автор новости',
            ],
            'source' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'comment'    => 'Источник публикации',
            ],
            'source_href' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'comment'    => 'Ссылка на источник',
            ],
            'href' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'comment'    => 'Внешняя ссылка',
            ],
            'foto' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => false,
                'default'    => 0,
                'comment'    => 'ID главного изображения',
            ],
            'media' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => false,
                'default'    => 0,
                'comment'    => 'ID галереи',
            ],
            'type' => [
                'type'       => 'TINYINT',
                'constraint' => 2,
                'null'       => false,
                'comment'    => 'Тип новости',
            ],
            'form' => [
                'type'       => 'TINYINT',
                'constraint' => 2,
                'null'       => false,
                'default'    => 0,
                'comment'    => 'ID формы',
            ],
            'show_all' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'null'       => false,
                'default'    => 0,
                'comment'    => 'Показывать на главной',
            ],
            'target' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'unsigned'   => true,
                'null'       => false,
                'default'    => 0,
                'comment'    => 'Открывать в новом окне (0-нет,1-да)',
            ],
            'publish_time' => [
                'type'       => 'DATETIME',
                'null'       => false,
                'comment'    => 'Время публикации',
            ],
            'morder' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
                'default'    => 0,
                'comment'    => 'Порядок сортировки',
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('name', false, false, 'idx_name');
        $this->forge->addKey('publish', false, false, 'idx_publish');
        $this->forge->addKey('date', false, false, 'idx_date');
        $this->forge->addKey('modify', false, false, 'idx_modify');
        $this->forge->addKey('path', false, false, 'idx_path');
        $this->forge->addKey('type', false, false, 'idx_type');
        $this->forge->addKey('publish_time', false, false, 'idx_publish_time');

        $this->forge->createTable('n_news_articles', true);
    }

    public function down()
    {
        $this->forge->dropTable('n_news_articles', true);
    }
}