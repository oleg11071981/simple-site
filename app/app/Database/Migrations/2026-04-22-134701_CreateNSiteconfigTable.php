<?php

/**
 * Миграция для создания таблицы настроек сайта n_siteconfig
 *
 * Таблица для хранения параметров конфигурации сайта
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
 * Миграция для создания таблицы настроек сайта
 */
class CreateNSiteconfigTable extends Migration
{
    /**
     * Создает таблицу n_siteconfig
     *
     * @return void
     */
    public function up()
    {
        // Поля таблицы настроек (соответствие оригиналу)
        $this->forge->addField([
            'parameter' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => false,
                'comment'    => 'Название параметра (ключ)',
            ],
            'value' => [
                'type'       => 'LONGTEXT',
                'null'       => true,
                'comment'    => 'Значение параметра (текст, HTML, JS)',
            ],
        ]);

        // Добавляем первичный ключ на parameter
        $this->forge->addKey('parameter', true);

        // Создаем таблицу
        $this->forge->createTable('n_siteconfig', true);
    }

    /**
     * Удаляет таблицу n_siteconfig
     *
     * @return void
     */
    public function down()
    {
        $this->forge->dropTable('n_siteconfig', true);
    }
}