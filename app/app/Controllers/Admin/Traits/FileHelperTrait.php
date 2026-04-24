<?php

/**
 * Трейт с вспомогательными методами для работы с файлами
 *
 * @package App\Controllers\Admin\Traits
 * @category Traits
 * @author  Your Name
 * @license MIT
 * @link    http://localhost
 * @noinspection PhpUnused
 */

namespace App\Controllers\Admin\Traits;

trait FileHelperTrait
{
    /**
     * Форматировать размер файла
     *
     * @param int $bytes Размер в байтах
     * @return string Отформатированный размер
     */
    protected function formatFileSize(int $bytes): string
    {
        if ($bytes < 1024) {
            return $bytes . ' Б';
        }
        if ($bytes < 1048576) {
            return round($bytes / 1024, 1) . ' КБ';
        }
        if ($bytes < 1073741824) {
            return round($bytes / 1048576, 1) . ' МБ';
        }
        return round($bytes / 1073741824, 1) . ' ГБ';
    }

    /**
     * Получить иконку для типа файла
     *
     * @param string $fileType Тип файла
     * @return string Иконка
     */
    protected function getFileIcon(string $fileType): string
    {
        $icons = [
            'jpg' => '🖼️', 'jpeg' => '🖼️', 'png' => '🖼️', 'gif' => '🖼️',
            'pdf' => '📄', 'doc' => '📝', 'docx' => '📝', 'xls' => '📊',
            'xlsx' => '📊', 'zip' => '📦', 'rar' => '📦', 'txt' => '📃',
            'mp3' => '🎵', 'mp4' => '🎬', 'avi' => '🎬'
        ];
        return $icons[strtolower($fileType)] ?? '📁';
    }

    /**
     * Проверить, является ли файл изображением
     *
     * @param string $fileType Тип файла
     * @return bool
     */
    protected function isImage(string $fileType): bool
    {
        return in_array(strtolower($fileType), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
    }
}