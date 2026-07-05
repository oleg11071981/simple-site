<?php

/**
 * Контроллер управления файлами (Файловый менеджер)
 *
 * Предоставляет полный набор методов для работы с файлами:
 * - Список файлов с фильтрацией и сортировкой
 * - Загрузка новых файлов
 * - Редактирование информации о файлах
 * - Удаление файлов (физическое и из БД)
 * - Массовые операции
 * - Обрезка/редактирование изображений
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

/**
 * Контроллер управления файлами
 *
 * @package App\Controllers\Admin
 */
class FilesController extends BaseController
{
    use FileHelperTrait;

    /**
     * Модель файлов
     *
     * @var NFileManagerModel
     */
    protected NFileManagerModel $filesModel;

    /**
     * Максимальный размер загружаемого файла (50 МБ)
     */
    private const MAX_FILE_SIZE = 50 * 1024 * 1024;

    /**
     * Типы файлов, считающиеся изображениями
     */
    private const IMAGE_TYPES = ['jpg', 'jpeg', 'png', 'gif'];

    /**
     * Типы файлов, считающиеся документами
     */
    private const DOCUMENT_TYPES = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'txt'];

    /**
     * Конструктор контроллера
     *
     * Инициализирует модель файлов.
     */
    public function __construct()
    {
        $this->filesModel = new NFileManagerModel();
    }

    /**
     * Отображение списка файлов
     *
     * Поддерживает фильтрацию по категории, типу файлов,
     * сортировку и пагинацию.
     *
     * @route GET /admin-panel/files
     *
     * @return string HTML страница со списком файлов
     */
    public function index(): string
    {
        $show      = (int)($this->request->getGet('show') ?? 1);
        $sort      = (int)($this->request->getGet('sort') ?? 2);
        $perPage   = (int)($this->request->getGet('per_page') ?? 50);
        $category  = (int)($this->request->getGet('category') ?? 0);
        $fileType  = $this->request->getGet('file_type') ?? '';

        $builder = $this->filesModel;

        // Фильтр по категории
        if ($category > 0) {
            $builder = $builder->where('category', $category);
        }

        // Фильтр по типу файла
        if (!empty($fileType)) {
            $builder = $builder->where('file_type', $fileType);
        }

        // Фильтр "Показывать" (все/изображения/документы)
        if ($show == 2) {
            $builder = $builder->whereIn('file_type', self::IMAGE_TYPES);
        } elseif ($show == 3) {
            $builder = $builder->whereIn('file_type', self::DOCUMENT_TYPES);
        }

        // Сортировка
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

        $currentPage = (int)($this->request->getGet('page') ?? 1);
        $files = $builder->paginate($perPage, 'default', $currentPage);
        $pager = $this->filesModel->pager;

        // Получаем категории для отображения названий
        $categoriesModel = new NFileManagerCategoriesModel();
        $categories = $categoriesModel->findAll();
        $categoriesMap = [];
        foreach ($categories as $cat) {
            $categoriesMap[$cat['id']] = $cat['name'];
        }

        // Обогащаем данные файлов дополнительной информацией
        foreach ($files as &$file) {
            $file['icon']            = $this->getFileIcon($file['file_type']);
            $file['size_formatted']  = $this->formatFileSize($file['file_size']);
            $file['category_name']   = $file['category'] > 0 ? ($categoriesMap[$file['category']] ?? '—') : '—';
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
     * Отображение формы загрузки файла
     *
     * @route GET /admin-panel/files/upload
     *
     * @return string HTML форма загрузки файла
     */
    public function upload(): string
    {
        $categoriesModel = new NFileManagerCategoriesModel();

        // Получаем ID категории из GET параметра (если перешли из категории)
        $selectedCategory = (int)($this->request->getGet('category') ?? 0);

        $data = [
            'title'            => 'Загрузка файла',
            'activeMenu'       => 'files',
            'categories'       => $categoriesModel->orderBy('name', 'ASC')->findAll(),
            'selectedCategory' => $selectedCategory,
        ];

        return view('admin/files/form', $data);
    }

    /**
     * Сохранение загруженного файла
     *
     * Обрабатывает загрузку файла, сохраняет его на диск
     * и добавляет запись в базу данных.
     *
     * @route POST /admin-panel/files/store
     *
     * @return RedirectResponse Редирект на список файлов или назад с ошибкой
     * @throws ReflectionException
     */
    public function store(): RedirectResponse
    {
        $file = $this->request->getFile('userfile');
        $postData = $this->request->getPost();

        // Валидация наличия файла
        if (!$file || !$file->isValid()) {
            return redirect()->back()->with('error', 'Выберите файл для загрузки');
        }

        $allowedTypes = array_merge(self::IMAGE_TYPES, self::DOCUMENT_TYPES);
        $validationError = $this->validateUploadedFile($file, $allowedTypes);
        if ($validationError !== null) {
            return redirect()->back()->with('error', $validationError);
        }

        // Проверка размера файла
        if ($file->getSize() > self::MAX_FILE_SIZE) {
            return redirect()->back()->with('error', 'Размер файла не должен превышать 50 МБ');
        }

        // Создаём директорию для загрузок, если её нет
        $uploadPath = FCPATH . 'uploads/';
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        $originalName = $file->getClientName();
        $fileType = strtolower($file->getExtension());
        $mimeType = $file->getMimeType();
        $fileSize = $file->getSize();

        // Сохраняем файл с уникальным именем
        $newName = $file->getRandomName();
        if (!$file->move($uploadPath, $newName)) {
            return redirect()->back()->with('error', 'Ошибка при загрузке файла');
        }

        // Для изображений определяем размеры
        $width = $height = 0;
        if (in_array($fileType, self::IMAGE_TYPES)) {
            $imageInfo = getimagesize($uploadPath . $newName);
            if ($imageInfo) {
                $width = $imageInfo[0];
                $height = $imageInfo[1];
            }
        }

        // Подготовка данных для сохранения в БД
        $saveData = [
            'file_name' => $newName,
            'file_type' => $fileType,
            'mime_type' => $mimeType,
            'file_size' => $fileSize,
            'name'      => $postData['name'] ?? pathinfo($originalName, PATHINFO_FILENAME),
            'category'  => (int)($postData['category'] ?? 0),
            'title'     => $postData['title'] ?? '',
            'priority'  => (int)($postData['priority'] ?? 0),
            'width'     => $width,
            'height'    => $height,
        ];

        if ($this->filesModel->save($saveData)) {
            return redirect()->to('/admin-panel/files')->with('success', 'Файл успешно загружен');
        }

        return redirect()->back()
            ->with('errors', $this->filesModel->errors())
            ->withInput();
    }

    /**
     * Отображение формы редактирования файла
     *
     * @route GET /admin-panel/files/edit/{id}
     *
     * @param int $id ID файла
     * @return RedirectResponse|string HTML форма или редирект при ошибке
     */
    public function edit(int $id)
    {
        $file = $this->filesModel->find($id);
        if (!$file) {
            return redirect()->to('/admin-panel/files')->with('error', 'Файл не найден');
        }

        $categoriesModel = new NFileManagerCategoriesModel();

        // Добавляем дополнительные данные для отображения
        $file['icon']            = $this->getFileIcon($file['file_type']);
        $file['size_formatted']  = $this->formatFileSize($file['file_size']);

        $data = [
            'title'      => 'Редактирование файла',
            'activeMenu' => 'files',
            'file'       => $file,
            'categories' => $categoriesModel->orderBy('name', 'ASC')->findAll(),
        ];

        return view('admin/files/form', $data);
    }

    /**
     * Обновление информации о файле
     *
     * @route POST /admin-panel/files/update/{id}
     *
     * @param int $id ID файла
     * @return RedirectResponse Редирект на список файлов или назад с ошибкой
     * @throws ReflectionException
     */
    public function update(int $id): RedirectResponse
    {
        $postData = $this->request->getPost();

        if ($this->filesModel->update($id, $postData)) {
            return redirect()->to('/admin-panel/files')->with('success', 'Файл успешно обновлён');
        }

        return redirect()->back()
            ->with('errors', $this->filesModel->errors())
            ->withInput();
    }

    /**
     * Удаление файла
     *
     * Удаляет файл физически с диска и запись из базы данных.
     *
     * @route GET /admin-panel/files/delete/{id}
     *
     * @param int $id ID файла
     * @return RedirectResponse Редирект на список файлов с сообщением об успехе/ошибке
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
     * Массовые операции с файлами
     *
     * Поддерживает массовое удаление выбранных файлов.
     *
     * @route POST /admin-panel/files/bulk-action
     *
     * @return RedirectResponse Редирект назад с сообщением об успехе/ошибке
     */
    public function bulkAction(): RedirectResponse
    {
        $action = $this->request->getPost('bulk_action');
        $ids = $this->request->getPost('selected_ids');

        if (empty($ids) || empty($action)) {
            return redirect()->back()->with('error', 'Выберите действие и файлы');
        }

        if ($action === 'delete') {
            // Удаляем файлы физически
            foreach ($ids as $id) {
                $file = $this->filesModel->find($id);
                if ($file) {
                    $filePath = FCPATH . 'uploads/' . $file['file_name'];
                    if (file_exists($filePath)) {
                        unlink($filePath);
                    }
                }
            }
            // Удаляем записи из БД
            $this->filesModel->whereIn('id', $ids)->delete();
            return redirect()->back()->with('success', 'Файлы удалены');
        }

        return redirect()->back()->with('error', 'Неизвестное действие');
    }

    /**
     * Обрезка изображения
     *
     * Обрабатывает запрос на обрезку изображения, полученный из Cropper.js.
     * Принимает base64-данные, сохраняет обрезанное изображение поверх оригинального
     * и обновляет информацию в базе данных.
     *
     * @route POST /admin-panel/files/crop-image/{id}
     *
     * @param int $id ID файла
     * @return ResponseInterface JSON ответ с результатом операции
     */
    public function cropImage(int $id): ResponseInterface
    {
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
                    'width'          => $newWidth,
                    'height'         => $newHeight,
                    'file_size'      => $newSize,
                    'modify'         => date('Y-m-d H:i:s'),
                    'modify_by_user' => session()->get('user_id') ?? 0
                ]);

                @unlink($tempPath);

                return $this->response->setJSON([
                    'success' => true,
                    'width'   => $newWidth,
                    'height'  => $newHeight,
                    'size'    => $newSize
                ]);
            }

            @unlink($tempPath);
            return $this->response->setJSON(['success' => false, 'error' => 'Ошибка сохранения файла']);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'error'   => $e->getMessage()
            ]);
        }
    }
}