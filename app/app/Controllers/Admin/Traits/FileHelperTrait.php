<?php

/**
 * Трейт с вспомогательными методами для работы с файлами
 *
 * @package App\Controllers\Admin\Traits
 * @category Traits
 * @license MIT
 * @link    http://localhost
 * @noinspection PhpUnused
 */

namespace App\Controllers\Admin\Traits;

use CodeIgniter\HTTP\Files\UploadedFile;

trait FileHelperTrait
{
    /**
     * Допустимые MIME-типы по расширению
     *
     * @var array<string, list<string>>
     */
    private array $mimeMap = [
        'jpg'  => ['image/jpeg', 'image/pjpeg'],
        'jpeg' => ['image/jpeg', 'image/pjpeg'],
        'png'  => ['image/png'],
        'gif'  => ['image/gif'],
        'webp' => ['image/webp'],
        'pdf'  => ['application/pdf'],
        'doc'  => ['application/msword'],
        'docx' => ['application/vnd.openxmlformats-officedocument.wordprocessingml.document'],
        'xls'  => ['application/vnd.ms-excel'],
        'xlsx' => ['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'],
        'zip'  => ['application/zip', 'application/x-zip-compressed'],
        'rar'  => ['application/vnd.rar', 'application/x-rar-compressed'],
        'txt'  => ['text/plain'],
    ];

    /**
     * Проверить загружаемый файл по расширению и MIME.
     * Возвращает текст ошибки или null, если файл допустим.
     */
    protected function validateUploadedFile(UploadedFile $file, array $allowedExtensions): ?string
    {
        if (!$file->isValid()) {
            return 'Файл повреждён или не был загружен';
        }

        $extension = strtolower($file->getExtension());
        if (!in_array($extension, $allowedExtensions, true)) {
            return 'Тип файла не поддерживается';
        }

        $mimeType = strtolower((string) $file->getMimeType());
        $allowedMimes = $this->mimeMap[$extension] ?? [];

        if ($allowedMimes !== [] && !in_array($mimeType, $allowedMimes, true)) {
            return 'Содержимое файла не соответствует заявленному типу';
        }

        if ($this->isImage($extension)) {
            $imageInfo = @getimagesize($file->getTempName());
            if ($imageInfo === false) {
                return 'Файл не является корректным изображением';
            }
        }

        return null;
    }

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

    /**
     * Уменьшить изображение по длинной стороне (без апскейла).
     *
     * @return array{width: int, height: int, file_size: int}|null
     */
    protected function resizeImageIfNeeded(
        string $filePath,
        string $extension,
        int $maxSide = 1280,
        int $jpegQuality = 82
    ): ?array {
        $extension = strtolower($extension);
        if (! $this->isImage($extension) || ! is_file($filePath) || ! extension_loaded('gd')) {
            return null;
        }

        $imageInfo = @getimagesize($filePath);
        if ($imageInfo === false) {
            return null;
        }

        $width  = (int) $imageInfo[0];
        $height = (int) $imageInfo[1];
        if ($width < 1 || $height < 1) {
            return null;
        }

        $longSide = max($width, $height);
        if ($longSide <= $maxSide) {
            return [
                'width'     => $width,
                'height'    => $height,
                'file_size' => (int) filesize($filePath),
            ];
        }

        $ratio     = $maxSide / $longSide;
        $newWidth  = (int) max(1, round($width * $ratio));
        $newHeight = (int) max(1, round($height * $ratio));

        $source = match ($extension) {
            'jpg', 'jpeg' => @imagecreatefromjpeg($filePath),
            'png'         => @imagecreatefrompng($filePath),
            'gif'         => @imagecreatefromgif($filePath),
            'webp'        => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($filePath) : false,
            default       => false,
        };

        if ($source === false) {
            return [
                'width'     => $width,
                'height'    => $height,
                'file_size' => (int) filesize($filePath),
            ];
        }

        $dest = imagecreatetruecolor($newWidth, $newHeight);
        if ($dest === false) {
            imagedestroy($source);

            return [
                'width'     => $width,
                'height'    => $height,
                'file_size' => (int) filesize($filePath),
            ];
        }

        if (in_array($extension, ['png', 'gif', 'webp'], true)) {
            imagealphablending($dest, false);
            imagesavealpha($dest, true);
            $transparent = imagecolorallocatealpha($dest, 0, 0, 0, 127);
            imagefilledrectangle($dest, 0, 0, $newWidth, $newHeight, $transparent);
        }

        imagecopyresampled($dest, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

        $saved = match ($extension) {
            'jpg', 'jpeg' => imagejpeg($dest, $filePath, $jpegQuality),
            'png'         => imagepng($dest, $filePath, 6),
            'gif'         => imagegif($dest, $filePath),
            'webp'        => function_exists('imagewebp') ? imagewebp($dest, $filePath, $jpegQuality) : false,
            default       => false,
        };

        imagedestroy($source);
        imagedestroy($dest);

        clearstatcache(true, $filePath);

        if (! $saved) {
            return [
                'width'     => $width,
                'height'    => $height,
                'file_size' => (int) filesize($filePath),
            ];
        }

        return [
            'width'     => $newWidth,
            'height'    => $newHeight,
            'file_size' => (int) filesize($filePath),
        ];
    }
}