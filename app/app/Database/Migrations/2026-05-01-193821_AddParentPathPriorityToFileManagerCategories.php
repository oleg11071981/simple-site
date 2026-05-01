<?php

/**
 * Миграция для добавления полей в таблицу n_file_manager_categories
 */

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddParentPathPriorityToFileManagerCategories extends Migration
{
    public function up()
    {
        // Проверяем, существует ли таблица
        if ($this->db->tableExists('n_file_manager_categories')) {

            // Добавляем поле parent
            if (!$this->db->fieldExists('parent', 'n_file_manager_categories')) {
                $this->forge->addColumn('n_file_manager_categories', [
                    'parent' => [
                        'type'       => 'INT',
                        'constraint' => 11,
                        'unsigned'   => true,
                        'default'    => 0,
                        'after'      => 'name',
                        'comment'    => 'ID родительской категории (0 - корневая)',
                    ],
                ]);
            }

            // Добавляем поле path
            if (!$this->db->fieldExists('path', 'n_file_manager_categories')) {
                $this->forge->addColumn('n_file_manager_categories', [
                    'path' => [
                        'type'       => 'VARCHAR',
                        'constraint' => 255,
                        'null'       => true,
                        'after'      => 'parent',
                        'comment'    => 'Уникальный путь для URL',
                    ],
                ]);
            }

            // Добавляем поле priority
            if (!$this->db->fieldExists('priority', 'n_file_manager_categories')) {
                $this->forge->addColumn('n_file_manager_categories', [
                    'priority' => [
                        'type'       => 'INT',
                        'constraint' => 11,
                        'unsigned'   => true,
                        'default'    => 0,
                        'after'      => 'path',
                        'comment'    => 'Порядок сортировки',
                    ],
                ]);
            }

            // Добавляем поле description
            if (!$this->db->fieldExists('description', 'n_file_manager_categories')) {
                $this->forge->addColumn('n_file_manager_categories', [
                    'description' => [
                        'type'       => 'TEXT',
                        'null'       => true,
                        'after'      => 'priority',
                        'comment'    => 'Описание категории',
                    ],
                ]);
            }

            // Добавляем индексы
            $this->forge->addKey('parent', false, false, 'idx_cat_parent');
            $this->forge->addKey('path', false, false, 'idx_cat_path');
            $this->forge->addKey('priority', false, false, 'idx_cat_priority');
        }
    }

    public function down()
    {
        // Удаляем индексы
        $this->forge->dropKey('n_file_manager_categories', 'idx_cat_parent');
        $this->forge->dropKey('n_file_manager_categories', 'idx_cat_path');
        $this->forge->dropKey('n_file_manager_categories', 'idx_cat_priority');

        // Удаляем поля
        $this->forge->dropColumn('n_file_manager_categories', 'parent');
        $this->forge->dropColumn('n_file_manager_categories', 'path');
        $this->forge->dropColumn('n_file_manager_categories', 'priority');
        $this->forge->dropColumn('n_file_manager_categories', 'description');
    }
}