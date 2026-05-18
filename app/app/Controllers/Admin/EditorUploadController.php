<?php

/**
 * Контроллер для загрузки и выбора файлов через CKEditor
 *
 * Обеспечивает функциональность для встроенного в CKEditor менеджера файлов:
 * - Загрузка изображений и документов
 * - Браузер файлов для выбора существующих
 * - Получение списка файлов с пагинацией
 *
 * @package App\Controllers\Admin
 * @category Controllers
 * @license MIT
 * @link    http://localhost
 * @noinspection PhpUnused
 */

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Controllers\Admin\Traits\FileHelperTrait;
use App\Models\NFileManagerModel;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\HTTP\Files\UploadedFile;

/**
 * Контроллер для загрузки и выбора файлов через CKEditor
 *
 * @package App\Controllers\Admin
 */
class EditorUploadController extends BaseController
{
    use FileHelperTrait;

    /**
     * Максимальный размер загружаемого изображения (5 МБ)
     */
    private const MAX_IMAGE_SIZE = 5 * 1024 * 1024;

    /**
     * Разрешенные типы файлов для общего метода загрузки
     */
    private const ALLOWED_FILE_TYPES = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'zip', 'rar', 'txt'];

    /**
     * Разрешенные типы изображений
     */
    private const ALLOWED_IMAGE_TYPES = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

    /**
     * Загрузка файлов через CKEditor (общий метод)
     *
     * Поддерживает загрузку документов и изображений.
     * Файлы сохраняются в директорию /uploads/ckeditor/
     *
     * @route POST /admin-panel/editor/upload
     *
     * @return ResponseInterface JSON ответ в формате CKEditor
     */
    public function upload(): ResponseInterface
    {
        /** @var UploadedFile|null $file Загруженный файл */
        $file = $this->request->getFile('upload');

        // Валидация наличия файла
        if (!$file || !$file->isValid()) {
            return $this->response->setJSON([
                'uploaded' => false,
                'error'    => ['message' => 'Файл не выбран или повреждён']
            ]);
        }

        // Создаём папку для загрузок, если её нет
        $uploadPath = FCPATH . 'uploads/ckeditor/';
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        // Проверяем тип файла
        $fileType = strtolower($file->getExtension());
        if (!in_array($fileType, self::ALLOWED_FILE_TYPES)) {
            return $this->response->setJSON([
                'uploaded' => false,
                'error'    => ['message' => 'Тип файла не поддерживается']
            ]);
        }

        // Сохраняем файл с уникальным именем
        $newName = $file->getRandomName();
        if (!$file->move($uploadPath, $newName)) {
            return $this->response->setJSON([
                'uploaded' => false,
                'error'    => ['message' => 'Ошибка при сохранении файла']
            ]);
        }

        // Формируем URL для доступа к файлу
        $url = base_url('uploads/ckeditor/' . $newName);

        return $this->response->setJSON([
            'uploaded' => true,
            'url'      => $url
        ]);
    }

    /**
     * Загрузка изображений через CKEditor
     *
     * Специализированный метод для загрузки только изображений.
     * Файлы сохраняются в директорию /uploads/ckeditor/images/
     * Дополнительно проверяется размер (не более 5 МБ).
     *
     * @route POST /admin-panel/editor/upload-image
     *
     * @return ResponseInterface JSON ответ в формате CKEditor
     */
    public function uploadImage(): ResponseInterface
    {
        /** @var UploadedFile|null $file Загруженный файл */
        $file = $this->request->getFile('upload');

        // Валидация наличия файла
        if (!$file || !$file->isValid()) {
            return $this->response->setJSON([
                'uploaded' => false,
                'error'    => ['message' => 'Изображение не выбрано или повреждено']
            ]);
        }

        // Создаём папку для загрузок, если её нет
        $uploadPath = FCPATH . 'uploads/ckeditor/images/';
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        // Проверяем, что это изображение
        $fileType = strtolower($file->getExtension());
        if (!in_array($fileType, self::ALLOWED_IMAGE_TYPES)) {
            return $this->response->setJSON([
                'uploaded' => false,
                'error'    => ['message' => 'Можно загружать только изображения (JPG, PNG, GIF, WEBP)']
            ]);
        }

        // Проверяем размер (max 5MB)
        if ($file->getSize() > self::MAX_IMAGE_SIZE) {
            return $this->response->setJSON([
                'uploaded' => false,
                'error'    => ['message' => 'Размер изображения не должен превышать 5 МБ']
            ]);
        }

        // Сохраняем файл с уникальным именем
        $newName = $file->getRandomName();
        if (!$file->move($uploadPath, $newName)) {
            return $this->response->setJSON([
                'uploaded' => false,
                'error'    => ['message' => 'Ошибка при сохранении изображения']
            ]);
        }

        // Формируем URL для доступа к файлу
        $url = base_url('uploads/ckeditor/images/' . $newName);

        return $this->response->setJSON([
            'uploaded' => true,
            'url'      => $url
        ]);
    }

    /**
     * Страница выбора файлов для CKEditor
     *
     * Отображает интерфейс для выбора существующих файлов
     * из файлового менеджера.
     *
     * @route GET /admin-panel/editor/ckeditor-browse
     *
     * @return string HTML страница браузера файлов
     */
    public function ckeditorBrowse(): string
    {
        $type = $this->request->getGet('type') ?? 'all';

        return view('admin/editor/ckeditor_browse', ['defaultType' => $type]);
    }

    /**
     * Получение списка файлов из файлового менеджера
     *
     * Возвращает JSON с файлами для AJAX-запросов CKEditor.
     * Поддерживает фильтрацию по типу (изображения/документы),
     * поиск по названию и пагинацию.
     *
     * @route GET /admin-panel/editor/get-files
     *
     * @return ResponseInterface JSON ответ со списком файлов
     */
    public function getFiles(): ResponseInterface
    {
        $page     = (int)($this->request->getGet('page') ?? 1);
        $type     = $this->request->getGet('type') ?? 'all';
        $search   = $this->request->getGet('search') ?? '';
        $perPage  = 20;

        $filesModel = new NFileManagerModel();
        $builder = $filesModel;

        // Фильтрация по типу файлов
        if ($type === 'image') {
            $builder = $builder->whereIn('file_type', self::ALLOWED_IMAGE_TYPES);
        } elseif ($type === 'document') {
            $builder = $builder->whereNotIn('file_type', self::ALLOWED_IMAGE_TYPES);
        }

        // Поиск по названию или оригинальному имени файла
        if (!empty($search)) {
            $builder = $builder->groupStart()
                ->like('name', $search)
                ->orLike('file_name', $search)
                ->groupEnd();
        }

        // Подсчёт общего количества (без учёта лимитов)
        $total = $builder->countAllResults(false);

        // Получение файлов с пагинацией
        $files = $builder->orderBy('id', 'DESC')
            ->limit($perPage, ($page - 1) * $perPage)
            ->find();

        // Формирование результата с дополнительными данными
        $result = [];
        foreach ($files as $file) {
            $result[] = [
                'id'              => $file['id'],
                'name'            => $file['name'],
                'file_name'       => $file['file_name'],
                'file_type'       => $this->isImage($file['file_type']) ? 'image' : 'file',
                'file_ext'        => $file['file_type'],
                'size'            => $file['file_size'],
                'size_formatted'  => $this->formatFileSize($file['file_size']),
                'url'             => base_url('uploads/' . $file['file_name'])
            ];
        }

        return $this->response->setJSON([
            'files'     => $result,
            'total'     => $total,
            'page'      => $page,
            'has_more'  => ($page * $perPage) < $total
        ]);
    }
}