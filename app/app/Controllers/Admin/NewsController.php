<?php

/**
 * Контроллер управления новостями
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
use App\Models\NNewsArticlesModel;
use CodeIgniter\HTTP\RedirectResponse;
use ReflectionException;

class NewsController extends BaseController
{
    protected NNewsArticlesModel $newsModel;

    public function __construct()
    {
        $this->newsModel = new NNewsArticlesModel();
    }

    /**
     * Список новостей
     */
    public function index(): string
    {
        $show = $this->request->getGet('show') ?? 1;
        $sort = $this->request->getGet('sort') ?? 2;
        $perPage = $this->request->getGet('per_page') ?? 50;
        $search = $this->request->getGet('search') ?? '';

        $builder = $this->newsModel;

        // Поиск по названию
        if (!empty($search)) {
            $builder = $builder->like('name', $search);
        }

        // Фильтр по статусу
        if ($show == 2) {
            $builder = $builder->where('publish', 1);
        } elseif ($show == 3) {
            $builder = $builder->where('publish', 0);
        }

        // Сортировка
        switch ($sort) {
            case 1: $builder = $builder->orderBy('id', 'ASC'); break;
            case 2: $builder = $builder->orderBy('id', 'DESC'); break;
            case 3: $builder = $builder->orderBy('name', 'ASC'); break;
            case 4: $builder = $builder->orderBy('name', 'DESC'); break;
            case 5: $builder = $builder->orderBy('date', 'ASC'); break;
            case 6: $builder = $builder->orderBy('date', 'DESC'); break;
            case 7: $builder = $builder->orderBy('create', 'ASC'); break;
            case 8: $builder = $builder->orderBy('create', 'DESC'); break;
            default: $builder = $builder->orderBy('id', 'DESC');
        }

        $currentPage = $this->request->getGet('page') ?? 1;
        $news = $builder->paginate($perPage, 'default', $currentPage);
        $pager = $this->newsModel->pager;

        $data = [
            'title'      => 'Управление новостями',
            'activeMenu' => 'news',
            'news'       => $news,
            'show'       => $show,
            'sort'       => $sort,
            'per_page'   => $perPage,
            'search'     => $search,
            'pager'      => $pager,
        ];

        return view('admin/news/index', $data);
    }

    /**
     * Форма создания новости
     */
    public function create(): string
    {
        // Получаем список категорий для галереи
        $categoriesModel = new \App\Models\NFileManagerCategoriesModel();
        $mediaCategories = $categoriesModel->getForSelect();

        $data = [
            'title'          => 'Создание новости',
            'activeMenu'     => 'news',
            'mediaCategories'=> $mediaCategories,
        ];
        return view('admin/news/form', $data);
    }

    /**
     * Сохранение новости
     * @throws ReflectionException
     */
    public function store(): RedirectResponse
    {
        $postData = $this->request->getPost();

        // Генерация path из названия
        if (empty($postData['path']) && !empty($postData['name'])) {
            $postData['path'] = $this->generatePath($postData['name']);
        }

        // Установка даты
        if (empty($postData['date'])) {
            $postData['date'] = date('Y-m-d');
        }

        // Установка времени публикации
        if (empty($postData['publish_time'])) {
            $postData['publish_time'] = date('Y-m-d H:i:s');
        }

        // Значения по умолчанию
        $postData['publish'] = $postData['publish'] ?? 0;
        $postData['type'] = $postData['type'] ?? 0;
        $postData['morder'] = $postData['morder'] ?? 0;

        if ($this->newsModel->save($postData)) {
            return redirect()->to('/admin-panel/news')
                ->with('success', 'Новость успешно создана');
        }

        return redirect()->back()
            ->with('errors', $this->newsModel->errors())
            ->withInput();
    }

    /**
     * Форма редактирования новости
     */
    public function edit(int $id)
    {
        $news = $this->newsModel->find($id);
        if (!$news) {
            return redirect()->to('/admin-panel/news')->with('error', 'Новость не найдена');
        }

        // Получаем список категорий для галереи
        $categoriesModel = new \App\Models\NFileManagerCategoriesModel();
        $mediaCategories = $categoriesModel->getForSelect();

        $data = [
            'title'          => 'Редактирование новости',
            'activeMenu'     => 'news',
            'news'           => $news,
            'mediaCategories'=> $mediaCategories,
        ];
        return view('admin/news/form', $data);
    }

    /**
     * Обновление новости
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
     * Переключение статуса публикации
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
     * Массовые действия
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
     * Генерация пути из названия
     */
    private function generatePath(string $name): string
    {
        $path = mb_strtolower($name, 'UTF-8');
        $path = str_replace([' ', '_', '.'], '-', $path);
        $path = preg_replace('/[^a-zа-я0-9-]/ui', '', $path);
        $path = preg_replace('/-+/', '-', $path);
        $path = trim($path, '-');

        $count = $this->newsModel->where('path', $path)->countAllResults();
        if ($count > 0) {
            $path .= '-' . ($count + 1);
        }

        return $path;
    }
}