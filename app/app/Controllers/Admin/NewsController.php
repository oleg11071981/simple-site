<?php

/**
 * Контроллер управления новостями
 *
 * Предоставляет методы для CRUD операций с новостями:
 * - Список новостей с фильтрацией, поиском и сортировкой
 * - Создание/редактирование/удаление новостей
 * - Управление статусом публикации
 * - Массовые операции с новостями
 *
 * @package App\Controllers\Admin
 * @category Controllers
 * @license MIT
 * @link    http://localhost
 * @noinspection PhpUnused
 */

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\NFileManagerCategoriesModel;
use App\Models\NFileManagerModel;
use App\Models\NNewsArticlesModel;
use App\Models\NNewsCategoriesModel;
use CodeIgniter\HTTP\RedirectResponse;
use ReflectionException;

/**
 * Контроллер управления новостями
 *
 * @package App\Controllers\Admin
 */
class NewsController extends BaseController
{
    /**
     * Модель новостей
     *
     * @var NNewsArticlesModel
     */
    protected NNewsArticlesModel $newsModel;

    /**
     * Конструктор контроллера
     *
     * Инициализирует модель новостей.
     */
    public function __construct()
    {
        $this->newsModel = new NNewsArticlesModel();
    }

    /**
     * Отображение списка новостей
     *
     * Поддерживает фильтрацию по статусу, категории,
     * поиск по названию, сортировку и пагинацию.
     *
     * @route GET /admin-panel/news
     *
     * @return string HTML страница со списком новостей
     */
    public function index(): string
    {
        $show          = (int)($this->request->getGet('show') ?? 1);
        $categoryNews  = (int)($this->request->getGet('category_news') ?? 0);
        $sort          = (int)($this->request->getGet('sort') ?? 2);
        $perPage       = (int)($this->request->getGet('per_page') ?? 50);
        $search        = $this->request->getGet('search') ?? '';

        $builder = $this->newsModel;

        // Поиск по названию
        if (!empty($search)) {
            $builder = $builder->like('name', $search);
        }

        // Фильтр по статусу публикации
        if ($show == 2) {
            $builder = $builder->where('publish', 1);
        } elseif ($show == 3) {
            $builder = $builder->where('publish', 0);
        }

        // Фильтр по категории новостей
        if ($categoryNews > 0) {
            $builder = $builder->where('category_news', $categoryNews);
        }

        // Сортировка
        switch ($sort) {
            case 1:  $builder = $builder->orderBy('id', 'ASC'); break;
            case 2:  $builder = $builder->orderBy('id', 'DESC'); break;
            case 3:  $builder = $builder->orderBy('name', 'ASC'); break;
            case 4:  $builder = $builder->orderBy('name', 'DESC'); break;
            case 5:  $builder = $builder->orderBy('date', 'ASC'); break;
            case 6:  $builder = $builder->orderBy('date', 'DESC'); break;
            case 7:  $builder = $builder->orderBy('create', 'ASC'); break;
            case 8:  $builder = $builder->orderBy('create', 'DESC'); break;
            default: $builder = $builder->orderBy('id', 'DESC');
        }

        $currentPage = (int)($this->request->getGet('page') ?? 1);
        $news = $builder->paginate($perPage, 'default', $currentPage);
        $pager = $this->newsModel->pager;

        // Получаем названия категорий для каждой новости
        $categoriesModel = new NNewsCategoriesModel();
        foreach ($news as &$item) {
            if ($item['category_news'] > 0) {
                $category = $categoriesModel->find($item['category_news']);
                $item['category_name'] = $category ? $category['name'] : '';
            } else {
                $item['category_name'] = '';
            }
        }

        $data = [
            'title'         => 'Управление новостями',
            'activeMenu'    => 'news',
            'news'          => $news,
            'show'          => $show,
            'category_news' => $categoryNews,
            'sort'          => $sort,
            'per_page'      => $perPage,
            'search'        => $search,
            'pager'         => $pager,
        ];

        return view('admin/news/index', $data);
    }

    /**
     * Отображение формы создания новости
     *
     * @route GET /admin-panel/news/create
     *
     * @return string HTML форма создания новости
     */
    public function create(): string
    {
        // Получаем список категорий для галереи
        $categoriesModel = new NFileManagerCategoriesModel();
        $mediaCategories = $categoriesModel->getForSelect();

        // Получаем список категорий новостей
        $newsCategoriesModel = new NNewsCategoriesModel();
        $newsCategories = $newsCategoriesModel->getForSelect();

        $data = [
            'title'           => 'Создание новости',
            'activeMenu'      => 'news',
            'mediaCategories' => $mediaCategories,
            'newsCategories'  => $newsCategories,
        ];

        return view('admin/news/form', $data);
    }

    /**
     * Сохранение новой новости
     *
     * @route POST /admin-panel/news/store
     *
     * @return RedirectResponse Редирект на список новостей или назад с ошибкой
     * @throws ReflectionException
     */
    public function store(): RedirectResponse
    {
        $postData = $this->request->getPost();

        // Генерация path из названия, если не указан
        if (empty($postData['path']) && !empty($postData['name'])) {
            $postData['path'] = $this->generatePath($postData['name']);
        }

        // Установка даты, если не указана
        if (empty($postData['date'])) {
            $postData['date'] = date('Y-m-d');
        }

        // Установка времени публикации, если не указано
        if (empty($postData['publish_time'])) {
            $postData['publish_time'] = date('Y-m-d H:i:s');
        }

        // Значения по умолчанию
        $postData['publish']       = $postData['publish'] ?? 0;
        $postData['type']          = $postData['type'] ?? 0;
        $postData['morder']        = $postData['morder'] ?? 0;
        $postData['category_news'] = $postData['category_news'] ?? 0;

        if ($this->newsModel->save($postData)) {
            return redirect()->to('/admin-panel/news')
                ->with('success', 'Новость успешно создана');
        }

        return redirect()->back()
            ->with('errors', $this->newsModel->errors())
            ->withInput();
    }

    /**
     * Отображение формы редактирования новости
     *
     * @route GET /admin-panel/news/edit/{id}
     *
     * @param int $id ID новости
     * @return RedirectResponse|string HTML форма или редирект при ошибке
     */
    public function edit(int $id)
    {
        $news = $this->newsModel->find($id);
        if (!$news) {
            return redirect()->to('/admin-panel/news')
                ->with('error', 'Новость не найдена');
        }

        // Получаем информацию о главном изображении
        if ($news['foto'] > 0) {
            $fileModel = new NFileManagerModel();
            $file = $fileModel->find($news['foto']);
            if ($file) {
                $news['foto_file'] = $file['file_name'];
            }
        }

        // Получаем список категорий для галереи
        $categoriesModel = new NFileManagerCategoriesModel();
        $mediaCategories = $categoriesModel->getForSelect();

        // Добавляем количество файлов для каждой категории галереи
        foreach ($mediaCategories as &$cat) {
            $cat['files_count'] = $categoriesModel->getFilesCount($cat['id']);
        }

        // Получаем список категорий новостей
        $newsCategoriesModel = new NNewsCategoriesModel();
        $newsCategories = $newsCategoriesModel->getForSelect();

        $data = [
            'title'           => 'Редактирование новости',
            'activeMenu'      => 'news',
            'news'            => $news,
            'mediaCategories' => $mediaCategories,
            'newsCategories'  => $newsCategories,
        ];

        return view('admin/news/form', $data);
    }

    /**
     * Обновление новости
     *
     * @route POST /admin-panel/news/update/{id}
     *
     * @param int $id ID новости
     * @return RedirectResponse Редирект на список новостей или назад с ошибкой
     * @throws ReflectionException
     */
    public function update(int $id): RedirectResponse
    {
        $postData = $this->request->getPost();

        if ($this->newsModel->update($id, $postData)) {
            return redirect()->to('/admin-panel/news')
                ->with('success', 'Новость успешно обновлена');
        }

        return redirect()->back()
            ->with('errors', $this->newsModel->errors())
            ->withInput();
    }

    /**
     * Удаление новости
     *
     * @route GET /admin-panel/news/delete/{id}
     *
     * @param int $id ID новости
     * @return RedirectResponse Редирект на список новостей с сообщением об успехе/ошибке
     */
    public function delete(int $id): RedirectResponse
    {
        if ($this->newsModel->delete($id)) {
            return redirect()->to('/admin-panel/news')
                ->with('success', 'Новость удалена');
        }

        return redirect()->back()->with('error', 'Ошибка при удалении');
    }

    /**
     * Переключение статуса публикации новости
     *
     * @route GET /admin-panel/news/toggle/{id}
     *
     * @param int $id ID новости
     * @return RedirectResponse Редирект назад с сообщением об успехе/ошибке
     * @throws ReflectionException
     */
    public function toggle(int $id): RedirectResponse
    {
        $news = $this->newsModel->find($id);
        if (!$news) {
            return redirect()->back()->with('error', 'Новость не найдена');
        }

        $newStatus = $news['publish'] == 1 ? 0 : 1;
        $this->newsModel->update($id, ['publish' => $newStatus]);

        $message = $newStatus == 1 ? 'Новость опубликована' : 'Новость снята с публикации';
        return redirect()->back()->with('success', $message);
    }

    /**
     * Массовые действия с новостями
     *
     * Поддерживает массовую публикацию, снятие с публикации и удаление.
     *
     * @route POST /admin-panel/news/bulk-action
     *
     * @return RedirectResponse Редирект назад с сообщением об успехе/ошибке
     * @throws ReflectionException
     */
    public function bulkAction(): RedirectResponse
    {
        $action = $this->request->getPost('bulk_action');
        $ids = $this->request->getPost('selected_ids');

        if (empty($ids) || empty($action)) {
            return redirect()->back()->with('error', 'Выберите действие и новости');
        }

        switch ($action) {
            case 'publish':
                $this->newsModel->whereIn('id', $ids)->set(['publish' => 1])->update();
                $message = 'Новости опубликованы';
                break;
            case 'unpublish':
                $this->newsModel->whereIn('id', $ids)->set(['publish' => 0])->update();
                $message = 'Новости сняты с публикации';
                break;
            case 'delete':
                $this->newsModel->whereIn('id', $ids)->delete();
                $message = 'Новости удалены';
                break;
            default:
                return redirect()->back()->with('error', 'Неизвестное действие');
        }

        return redirect()->back()->with('success', $message);
    }

    /**
     * Генерация уникального пути (slug) из названия новости
     *
     * Транслитерирует русские символы, заменяет пробелы на дефисы,
     * удаляет недопустимые символы и проверяет уникальность.
     *
     * @param string $name Название новости
     * @return string Сгенерированный уникальный путь
     */
    private function generatePath(string $name): string
    {
        // Транслитерация
        $path = mb_strtolower($name, 'UTF-8');
        $path = str_replace([' ', '_', '.'], '-', $path);
        $path = preg_replace('/[^a-zа-я0-9-]/ui', '', $path);
        $path = preg_replace('/-+/', '-', $path);
        $path = trim($path, '-');

        // Проверка уникальности
        $count = $this->newsModel->where('path', $path)->countAllResults();
        if ($count > 0) {
            $path .= '-' . ($count + 1);
        }

        return $path;
    }
}