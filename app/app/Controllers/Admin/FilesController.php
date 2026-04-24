<?php

/**
 * Контроллер управления файлами
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
use App\Models\NFileManagerCategoriesModel;
use App\Models\NFileManagerModel;
use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\HTTP\ResponseInterface;
use ReflectionException;

class FilesController extends BaseController
{
    use FileHelperTrait;

    protected NFileManagerModel $filesModel;

    public function __construct()
    {
        $this->filesModel = new NFileManagerModel();
    }

    /**
     * Список файлов
     */
    public function index(): string
    {
        $show = $this->request->getGet('show') ?? 1;
        $sort = $this->request->getGet('sort') ?? 2;
        $perPage = $this->request->getGet('per_page') ?? 50;
        $category = $this->request->getGet('category') ?? 0;
        $fileType = $this->request->getGet('file_type') ?? '';

        $builder = $this->filesModel;

        if ($category > 0) {
            $builder = $builder->where('category', $category);
        }

        if (!empty($fileType)) {
            $builder = $builder->where('file_type', $fileType);
        }

        if ($show == 2) {
            $builder = $builder->whereIn('file_type', ['jpg', 'jpeg', 'png', 'gif']);
        } elseif ($show == 3) {
            $builder = $builder->whereIn('file_type', ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'txt']);
        }

        switch ($sort) {
            case 1: $builder = $builder->orderBy('id', 'ASC'); break;
            case 2: $builder = $builder->orderBy('id', 'DESC'); break;
            case 3: $builder = $builder->orderBy('name', 'ASC'); break;
            case 4: $builder = $builder->orderBy('name', 'DESC'); break;
            case 5: $builder = $builder->orderBy('create', 'ASC'); break;
            case 6: $builder = $builder->orderBy('create', 'DESC'); break;
            case 7: $builder = $builder->orderBy('modify', 'ASC'); break;
            case 8: $builder = $builder->orderBy('modify', 'DESC'); break;
            default: $builder = $builder->orderBy('id', 'DESC');
        }

        $currentPage = $this->request->getGet('page') ?? 1;
        $files = $builder->paginate($perPage, 'default', $currentPage);
        $pager = $this->filesModel->pager;

        // Получаем категории для отображения
        $categoriesModel = new NFileManagerCategoriesModel();
        $categories = $categoriesModel->findAll();
        $categoriesMap = [];
        foreach ($categories as $cat) {
            $categoriesMap[$cat['id']] = $cat['name'];
        }

        // Добавляем иконку, форматированный размер и название категории для каждого файла
        foreach ($files as &$file) {
            $file['icon'] = $this->getFileIcon($file['file_type']);
            $file['size_formatted'] = $this->formatFileSize($file['file_size']);
            $file['category_name'] = $file['category'] > 0 ? ($categoriesMap[$file['category']] ?? '—') : '—';
        }

        $data = [
            'title'         => 'Файловый менеджер',
            'activeMenu'    => 'files',
            'files'         => $files,
            'show'          => $show,
            'sort'          => $sort,
            'per_page'      => $perPage,
            'category'      => $category,
            'file_type'     => $fileType,
            'categories'    => $categories,
            'pager'         => $pager,
        ];

        return view('admin/files/index', $data);
    }

    /**
     * Загрузка файла (форма)
     */
    public function upload(): string
    {
        $categoriesModel = new NFileManagerCategoriesModel();

        $data = [
            'title'         => 'Загрузка файла',
            'activeMenu'    => 'files',
            'categories'    => $categoriesModel->orderBy('name', 'ASC')->findAll(),
        ];
        return view('admin/files/form', $data);
    }

    /**
     * Сохранение загруженного файла
     * @throws ReflectionException
     */
    public function store(): RedirectResponse
    {
        $file = $this->request->getFile('userfile');
        $postData = $this->request->getPost();

        if (!$file || !$file->isValid()) {
            return redirect()->back()->with('error', 'Выберите файл для загрузки');
        }

        $uploadPath = FCPATH . 'uploads/';
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        $originalName = $file->getClientName();
        $fileType = $file->getExtension();
        $mimeType = $file->getMimeType();
        $fileSize = $file->getSize();

        $newName = $file->getRandomName();

        if (!$file->move($uploadPath, $newName)) {
            return redirect()->back()->with('error', 'Ошибка при загрузке файла');
        }

        $width = $height = 0;
        if (in_array(strtolower($fileType), ['jpg', 'jpeg', 'png', 'gif'])) {
            $imageInfo = getimagesize($uploadPath . $newName);
            if ($imageInfo) {
                $width = $imageInfo[0];
                $height = $imageInfo[1];
            }
        }

        $saveData = [
            'file_name' => $newName,
            'file_type' => $fileType,
            'mime_type' => $mimeType,
            'file_size' => $fileSize,
            'name'      => $postData['name'] ?? pathinfo($originalName, PATHINFO_FILENAME),
            'category'  => $postData['category'] ?? 0,
            'title'     => $postData['title'] ?? '',
            'priority'  => $postData['priority'] ?? 0,
            'width'     => $width,
            'height'    => $height,
        ];

        if ($this->filesModel->save($saveData)) {
            return redirect()->to('/admin-panel/files')->with('success', 'Файл успешно загружен');
        }

        return redirect()->back()->with('errors', $this->filesModel->errors())->withInput();
    }

    /**
     * Редактирование файла
     */
    public function edit(int $id)
    {
        $file = $this->filesModel->find($id);
        if (!$file) {
            return redirect()->to('/admin-panel/files')->with('error', 'Файл не найден');
        }

        $categoriesModel = new NFileManagerCategoriesModel();

        $file['icon'] = $this->getFileIcon($file['file_type']);
        $file['size_formatted'] = $this->formatFileSize($file['file_size']);

        $data = [
            'title'         => 'Редактирование файла',
            'activeMenu'    => 'files',
            'file'          => $file,
            'categories'    => $categoriesModel->orderBy('name', 'ASC')->findAll(),
        ];
        return view('admin/files/form', $data);
    }

    /**
     * Обновление файла
     * @throws ReflectionException
     */
    public function update(int $id): RedirectResponse
    {
        $postData = $this->request->getPost();

        if ($this->filesModel->update($id, $postData)) {
            return redirect()->to('/admin-panel/files')->with('success', 'Файл успешно обновлён');
        }

        return redirect()->back()->with('errors', $this->filesModel->errors())->withInput();
    }

    /**
     * Удаление файла
     */
    public function delete(int $id): RedirectResponse
    {
        $file = $this->filesModel->find($id);
        if ($file && $this->filesModel->delete($id)) {
            $filePath = FCPATH . 'uploads/' . $file['file_name'];
            if (file_exists($filePath)) {
                unlink($filePath);
            }
            return redirect()->to('/admin-panel/files')->with('success', 'Файл удалён');
        }
        return redirect()->back()->with('error', 'Ошибка при удалении');
    }

    /**
     * Массовые действия
     */
    public function bulkAction(): RedirectResponse
    {
        $action = $this->request->getPost('bulk_action');
        $ids = $this->request->getPost('selected_ids');

        if (empty($ids) || empty($action)) {
            return redirect()->back()->with('error', 'Выберите действие и файлы');
        }

        if ($action === 'delete') {
            foreach ($ids as $id) {
                $file = $this->filesModel->find($id);
                if ($file) {
                    $filePath = FCPATH . 'uploads/' . $file['file_name'];
                    if (file_exists($filePath)) {
                        unlink($filePath);
                    }
                }
            }
            $this->filesModel->whereIn('id', $ids)->delete();
            return redirect()->back()->with('success', 'Файлы удалены');
        }

        return redirect()->back()->with('error', 'Неизвестное действие');
    }

    /**
     * Обрезка изображения
     *
     * @param int $id ID файла
     * @return ResponseInterface
     */
    public function cropImage(int $id): ResponseInterface
    {
        // Устанавливаем заголовок JSON
        $this->response->setHeader('Content-Type', 'application/json');

        try {
            $file = $this->filesModel->find($id);
            if (!$file) {
                return $this->response->setJSON(['success' => false, 'error' => 'Файл не найден']);
            }

            $imageData = $this->request->getJSON();

            if (!$imageData || empty($imageData->image_data)) {
                return $this->response->setJSON(['success' => false, 'error' => 'Нет данных изображения']);
            }

            // Декодируем base64 изображение
            $imageDataBase64 = $imageData->image_data;
            $dataParts = explode(';', $imageDataBase64);

            if (count($dataParts) < 2) {
                return $this->response->setJSON(['success' => false, 'error' => 'Неверный формат данных']);
            }

            $base64Data = explode(',', $dataParts[1]);
            if (count($base64Data) < 2) {
                return $this->response->setJSON(['success' => false, 'error' => 'Неверный формат base64']);
            }

            $imageBinary = base64_decode($base64Data[1]);
            if (!$imageBinary) {
                return $this->response->setJSON(['success' => false, 'error' => 'Ошибка декодирования изображения']);
            }

            // Создаём папку для временных файлов
            $tempDir = WRITEPATH . 'uploads/';
            if (!is_dir($tempDir)) {
                mkdir($tempDir, 0755, true);
            }

            // Сохраняем во временный файл
            $tempPath = $tempDir . 'temp_' . uniqid() . '_' . $file['file_name'];
            $bytesWritten = file_put_contents($tempPath, $imageBinary);

            if (!$bytesWritten) {
                return $this->response->setJSON(['success' => false, 'error' => 'Ошибка записи временного файла']);
            }

            // Проверяем, что файл корректен
            $imageInfo = @getimagesize($tempPath);
            if (!$imageInfo) {
                @unlink($tempPath);
                return $this->response->setJSON(['success' => false, 'error' => 'Некорректный формат изображения']);
            }

            $newWidth = $imageInfo[0];
            $newHeight = $imageInfo[1];
            $newSize = filesize($tempPath);

            // Сохраняем поверх старого файла
            $uploadPath = FCPATH . 'uploads/' . $file['file_name'];
            if (copy($tempPath, $uploadPath)) {
                // Обновляем информацию в БД
                $this->filesModel->update($id, [
                    'width' => $newWidth,
                    'height' => $newHeight,
                    'file_size' => $newSize,
                    'modify' => date('Y-m-d H:i:s'),
                    'modify_by_user' => session()->get('user_id') ?? 0
                ]);

                // Удаляем временный файл
                @unlink($tempPath);

                return $this->response->setJSON([
                    'success' => true,
                    'width' => $newWidth,
                    'height' => $newHeight,
                    'size' => $newSize
                ]);
            }

            @unlink($tempPath);
            return $this->response->setJSON(['success' => false, 'error' => 'Ошибка сохранения файла']);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }
}