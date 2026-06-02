<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Исправляет тип колонки timestamp в таблице сессий.
 *
 * CodeIgniter DatabaseHandler записывает NOW(), а не Unix timestamp.
 * При типе INT UNSIGNED значение переполняется до 4294967295,
 * из-за чего ломается очистка устаревших сессий (GC).
 */
class FixTimestampColumnInNUserSessions extends Migration
{
    private function dropIndexIfExists(string $table, string $index): void
    {
        $result = $this->db->query("SHOW INDEX FROM `{$table}` WHERE Key_name = '{$index}'");

        if ($result->getNumRows() > 0) {
            $this->db->query("ALTER TABLE `{$table}` DROP INDEX `{$index}`");
        }
    }

    public function up()
    {
        if (!$this->db->tableExists('n_user_sessions')) {
            return;
        }

        if (!$this->db->fieldExists('timestamp', 'n_user_sessions')) {
            return;
        }

        $this->dropIndexIfExists('n_user_sessions', 'idx_timestamp');

        // Прямая конвертация INT → DATETIME ломается на значении 4294967295,
        // поэтому создаём новую колонку и заменяем старую.
        if (!$this->db->fieldExists('timestamp_new', 'n_user_sessions')) {
            $this->db->query(
                'ALTER TABLE `n_user_sessions` ADD COLUMN `timestamp_new` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `ip_address`'
            );
        }

        $this->db->query('UPDATE `n_user_sessions` SET `timestamp_new` = CURRENT_TIMESTAMP');

        $this->db->query('ALTER TABLE `n_user_sessions` DROP COLUMN `timestamp`');
        $this->db->query(
            'ALTER TABLE `n_user_sessions` CHANGE COLUMN `timestamp_new` `timestamp` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT \'Время последней активности сессии (для GC)\''
        );

        $this->db->query('ALTER TABLE `n_user_sessions` ADD INDEX `idx_timestamp` (`timestamp`)');
    }

    public function down()
    {
        if (!$this->db->tableExists('n_user_sessions')) {
            return;
        }

        if (!$this->db->fieldExists('timestamp', 'n_user_sessions')) {
            return;
        }

        $this->dropIndexIfExists('n_user_sessions', 'idx_timestamp');

        if (!$this->db->fieldExists('timestamp_old', 'n_user_sessions')) {
            $this->db->query(
                'ALTER TABLE `n_user_sessions` ADD COLUMN `timestamp_old` INT UNSIGNED NOT NULL DEFAULT 0 AFTER `ip_address`'
            );
        }

        $this->db->query('UPDATE `n_user_sessions` SET `timestamp_old` = UNIX_TIMESTAMP(`timestamp`)');

        $this->db->query('ALTER TABLE `n_user_sessions` DROP COLUMN `timestamp`');
        $this->db->query(
            'ALTER TABLE `n_user_sessions` CHANGE COLUMN `timestamp_old` `timestamp` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT \'Временная метка (Unix timestamp)\''
        );

        $this->db->query('ALTER TABLE `n_user_sessions` ADD INDEX `idx_timestamp` (`timestamp`)');
    }
}
