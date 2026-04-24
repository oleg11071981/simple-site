<?php

/**
 * Контроллер для загрузки и выбора файлов через CKEditor
 *
 * @package App\Controllers\Admin
 * @category Controllers
 * @author  Your Name
 * @license MIT
 * @link    http://localhost
 * @noinspection PhpUnused
 */

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Controllers\Admin\Traits\FileHelperTrait;
use App\Models\NFileManagerModel;
use CodeIgniter\HTTP\ResponseInterface;

class EditorUploadController extends BaseController
{
    use FileHelperTrait;

    /**
     * Загрузка файлов через CKEditor (общий метод)
     *
     * @route POST /admin-panel/editor/upload
     */
    public function upload(): ResponseInterface
    {
        $file = $this->request->getFile('upload');

        if (!$file || !$file->isValid()) {
            return $this->response->setJSON([
                'uploaded' => false,
                'error' => ['message' => 'Файл не выбран или повреждён']
            ]);
        }

        // Создаём папку для загрузок, если её нет
        $uploadPath = FCPATH . 'uploads/ckeditor/';
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        // Проверяем тип файла
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'zip', 'rar', 'txt'];
        $fileType = strtolower($file->getExtension());

        if (!in_array($fileType, $allowedTypes)) {
            return $this->response->setJSON([
                'uploaded' => false,
                'error' => ['message' => 'Тип файла не поддерживается']
            ]);
        }

        // Генерируем уникальное имя
        $newName = $file->getRandomName();

        // Сохраняем файл
        if (!$file->move($uploadPath, $newName)) {
            return $this->response->setJSON([
                'uploaded' => false,
                'error' => ['message' => 'Ошибка при сохранении файла']
            ]);
        }

        // Формируем URL для доступа к файлу
        $url = base_url('uploads/ckeditor/' . $newName);

        return $this->response->setJSON([
            'uploaded' => true,
            'url' => $url
        ]);
    }

    /**
     * Загрузка изображений через CKEditor
     *
     * @route POST /admin-panel/editor/upload-image
     */
    public function uploadImage(): ResponseInterface
    {
        $file = $this->request->getFile('upload');

        if (!$file || !$file->isValid()) {
            return $this->response->setJSON([
                'uploaded' => false,
                'error' => ['message' => 'Изображение не выбрано или повреждено']
            ]);
        }

        // Создаём папку для загрузок, если её нет
        $uploadPath = FCPATH . 'uploads/ckeditor/images/';
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        // Проверяем, что это изображение
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $fileType = strtolower($file->getExtension());

        if (!in_array($fileType, $allowedTypes)) {
            return $this->response->setJSON([
                'uploaded' => false,
                'error' => ['message' => 'Можно загружать только изображения (JPG, PNG, GIF)']
            ]);
        }

        // Проверяем размер (max 5MB)
        if ($file->getSize() > 5 * 1024 * 1024) {
            return $this->response->setJSON([
                'uploaded' => false,
                'error' => ['message' => 'Размер изображения не должен превышать 5 МБ']
            ]);
        }

        // Генерируем уникальное имя
        $newName = $file->getRandomName();

        // Сохраняем файл
        if (!$file->move($uploadPath, $newName)) {
            return $this->response->setJSON([
                'uploaded' => false,
                'error' => ['message' => 'Ошибка при сохранении изображения']
            ]);
        }

        // Формируем URL для доступа к файлу
        $url = base_url('uploads/ckeditor/images/' . $newName);

        return $this->response->setJSON([
            'uploaded' => true,
            'url' => $url
        ]);
    }

    /**
     * Страница выбора файлов для CKEditor
     *
     * @route GET /admin-panel/editor/ckeditor-browse
     */
    public function ckeditorBrowse(): string
    {
        // Получаем тип из GET параметра
        $type = $this->request->getGet('type') ?? 'all';

        return view('admin/editor/ckeditor_browse', ['defaultType' => $type]);
    }

    /**
     * Получение списка файлов из файлового менеджера
     *
     * @route GET /admin-panel/editor/get-files
     */
    public function getFiles(): ResponseInterface
    {
        $page = $this->request->getGet('page') ?? 1;
        $type = $this->request->getGet('type') ?? 'all';
        $search = $this->request->getGet('search') ?? '';
        $perPage = 20;

        $filesModel = new NFileManagerModel();
        $builder = $filesModel;

        // Фильтр по типу
        if ($type === 'image') {
            $builder = $builder->whereIn('file_type', ['jpg', 'jpeg', 'png', 'gif', 'webp']);
        } elseif ($type === 'document') {
            $builder = $builder->whereNotIn('file_type', ['jpg', 'jpeg', 'png', 'gif', 'webp']);
        }

        // Поиск по названию
        if (!empty($search)) {
            $builder = $builder->groupStart()
                ->like('name', $search)
                ->orLike('file_name', $search)
                ->groupEnd();
        }

        $total = $builder->countAllResults(false);
        $files = $builder->orderBy('id', 'DESC')
            ->limit($perPage, ($page - 1) * $perPage)
            ->find();

        $result = [];
        foreach ($files as $file) {
            $result[] = [
                'id' => $file['id'],
                'name' => $file['name'],
                'file_name' => $file['file_name'],
                'file_type' => $this->isImage($file['file_type']) ? 'image' : 'file',
                'file_ext' => $file['file_type'],
                'size' => $file['file_size'],
                'size_formatted' => $this->formatFileSize($file['file_size']),
                'url' => base_url('uploads/' . $file['file_name'])
            ];
        }

        return $this->response->setJSON([
            'files' => $result,
            'total' => $total,
            'page' => $page,
            'has_more' => ($page * $perPage) < $total
        ]);
    }
}