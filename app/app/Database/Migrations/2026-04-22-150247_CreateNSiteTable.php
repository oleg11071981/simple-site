<?php

/**
 * Миграция для создания таблицы страниц сайта n_site
 *
 * Таблица для управления страницами с иерархической структурой
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
 * Миграция для создания таблицы страниц сайта
 */
class CreateNSiteTable extends Migration
{
    /**
     * Создает таблицу n_site
     *
     * @return void
     */
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
                'comment'        => 'Уникальный идентификатор страницы',
            ],
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'comment'    => 'Название страницы',
            ],
            'more_info' => [
                'type'       => 'LONGTEXT',
                'null'       => true,
                'comment'    => 'Полное содержимое страницы',
            ],
            'keywords' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'comment'    => 'SEO ключевые слова',
            ],
            'description' => [
                'type'       => 'TEXT',
                'null'       => true,
                'comment'    => 'SEO описание',
            ],
            'publish' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'null'       => false,
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
            'href' => [
                'type'       => 'TEXT',
                'null'       => true,
                'comment'    => 'Внешняя ссылка (если есть)',
            ],
            'path' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'comment'    => 'Путь/URL страницы',
            ],
            'parent' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => false,
                'default'    => 0,
                'comment'    => 'ID родительской страницы (0 - корневая)',
            ],
            'priority' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => false,
                'default'    => 0,
                'comment'    => 'Приоритет/порядок сортировки',
            ],
            'media' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => false,
                'default'    => 0,
                'comment'    => 'ID медиафайла',
            ],
            'project' => [
                'type'       => 'TINYINT',
                'constraint' => 4,
                'null'       => false,
                'default'    => 0,
                'comment'    => 'Проект/раздел',
            ],
            'form' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => false,
                'default'    => 0,
                'comment'    => 'ID формы (если есть)',
            ],
            'show_in_menu' => [
                'type'       => 'TINYINT',
                'constraint' => 2,
                'null'       => false,
                'default'    => 1,
                'comment'    => 'Показывать в меню (0-нет, 1-да)',
            ],
            'type' => [
                'type'       => 'TINYINT',
                'constraint' => 2,
                'null'       => false,
                'default'    => 0,
                'comment'    => 'Тип страницы (0-обычная, 1-внешняя ссылка, 2-модуль)',
            ],
            'foto' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => false,
                'default'    => 0,
                'comment'    => 'ID изображения для анонса',
            ],
            'anons_text' => [
                'type'       => 'TEXT',
                'null'       => true,
                'comment'    => 'Текст анонса',
            ],
            'target' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'null'       => false,
                'default'    => 1,
                'comment'    => 'Открывать в новом окне (0-нет, 1-да)',
            ],
            'show_in_dop_menu' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => false,
                'default'    => 0,
                'comment'    => 'Показывать в дополнительном меню',
            ],
            'favorite' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'unsigned'   => true,
                'null'       => false,
                'default'    => 0,
                'comment'    => 'Избранное (0-нет, 1-да)',
            ],
            'foliant' => [
                'type'       => 'VARCHAR',
                'constraint' => 10,
                'null'       => true,
                'comment'    => 'Идентификатор для фолианта',
            ],
            'foliant_filter' => [
                'type'       => 'TEXT',
                'null'       => true,
                'comment'    => 'Фильтр фолианта',
            ],
            'new_on_site' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'unsigned'   => true,
                'null'       => false,
                'default'    => 0,
                'comment'    => 'Новинка на сайте',
            ],
            'x' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'comment'    => 'Координата X (для карты сайта)',
            ],
            'y' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'comment'    => 'Координата Y (для карты сайта)',
            ],
        ]);

        // Первичный ключ
        $this->forge->addKey('id', true);

        // Индексы для ускорения запросов
        $this->forge->addKey('name', false, false, 'idx_name');
        $this->forge->addKey('publish', false, false, 'idx_publish');
        $this->forge->addKey('modify', false, false, 'idx_modify');
        $this->forge->addKey('create_by_user', false, false, 'idx_create_by_user');
        $this->forge->addKey('path', false, false, 'idx_path');
        $this->forge->addKey('parent', false, false, 'idx_parent');
        $this->forge->addKey('priority', false, false, 'idx_priority');
        $this->forge->addKey('project', false, false, 'idx_project');
        $this->forge->addKey('show_in_menu', false, false, 'idx_show_in_menu');
        $this->forge->addKey('type', false, false, 'idx_type');
        $this->forge->addKey('show_in_dop_menu', false, false, 'idx_show_in_dop_menu');

        $this->forge->createTable('n_site', true);
    }

    /**
     * Удаляет таблицу n_site
     *
     * @return void
     */
    public function down()
    {
        $this->forge->dropTable('n_site', true);
    }
}