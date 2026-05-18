<?php

/**
 * Контроллер управления категориями новостей
 *
 * Предоставляет методы для CRUD операций с категориями новостей:
 * - Список категорий с деревом
 * - Создание/редактирование/удаление категорий
 * - Управление вложенностью
 * - Массовые операции с категориями
 *
 * @package App\Controllers\Admin
 * @category Controllers
 * @license MIT
 * @link    http://localhost
 * @noinspection PhpUnused
 */

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\NNewsCategoriesModel;
use App\Models\NNewsArticlesModel;
use CodeIgniter\HTTP\RedirectResponse;
use ReflectionException;

/**
 * Контроллер управления категориями новостей
 *
 * @package App\Controllers\Admin
 */
class NewsCategoriesController extends BaseController
{
    /**
     * Модель категорий новостей
     *
     * @var NNewsCategoriesModel
     */
    protected NNewsCategoriesModel $categoriesModel;

    /**
     * Модель новостей
     *
     * @var NNewsArticlesModel
     */
    protected NNewsArticlesModel $newsModel;

    /**
     * Конструктор контроллера
     *
     * Инициализирует модели для работы с категориями и новостями.
     */
    public function __construct()
    {
        $this->categoriesModel = new NNewsCategoriesModel();
        $this->newsModel = new NNewsArticlesModel();
    }

    /**
     * Отображение списка категорий (деревом)
     *
     * Показывает категории текущего уровня с возможностью навигации по вложенности.
     * Для каждой категории отображается количество новостей и наличие дочерних категорий.
     *
     * @route GET /admin-panel/news-categories
     *
     * @return string HTML страница со списком категорий
     */
    public function index(): string
    {
        $parent = (int)($this->request->getGet('parent') ?? 0);

        // Получаем категории для текущего уровня
        $categories = $this->categoriesModel
            ->where('parent', $parent)
            ->orderBy('priority', 'ASC')
            ->orderBy('name', 'ASC')
            ->findAll();

        // Добавляем количество новостей и флаг наличия дочерних категорий
        foreach ($categories as &$cat) {
            $cat['news_count'] = $this->getNewsCount($cat['id']);
            $cat['has_children'] = $this->categoriesModel->where('parent', $cat['id'])->countAllResults() > 0;
        }

        // Формируем хлебные крошки для навигации
        $breadcrumbs = [];
        $currentCategoryName = '';
        if ($parent > 0) {
            $currentCategory = $this->categoriesModel->find($parent);
            if ($currentCategory) {
                $currentCategoryName = $currentCategory['name'];
                $breadcrumbs = $this->getBreadcrumbs($parent);
            }
        }

        $data = [
            'title'                  => 'Категории новостей',
            'activeMenu'             => 'news_categories',
            'categories'             => $categories,
            'parent_id'              => $parent,
            'breadcrumbs'            => $breadcrumbs,
            'current_category_name'  => $currentCategoryName,
        ];

        return view('admin/news_categories/index', $data);
    }

    /**
     * Отображение формы создания категории
     *
     * @route GET /admin-panel/news-categories/create
     *
     * @return string HTML форма создания категории
     */
    public function create(): string
    {
        $parent = (int)($this->request->getGet('parent') ?? 0);

        $data = [
            'title'      => 'Создание категории новостей',
            'activeMenu' => 'news_categories',
            'parent_id'  => $parent,
            'categories' => $this->categoriesModel->getForSelect(),
        ];

        return view('admin/news_categories/form', $data);
    }

    /**
     * Сохранение новой категории
     *
     * @route POST /admin-panel/news-categories/store
     *
     * @return RedirectResponse Редирект на список категорий или назад с ошибкой
     * @throws ReflectionException
     */
    public function store(): RedirectResponse
    {
        $postData = $this->request->getPost();

        // Правила валидации
        $rules = [
            'name' => 'required|min_length[2]|max_length[255]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->with('errors', $this->validator->getErrors())
                ->withInput();
        }

        // Устанавливаем значения по умолчанию
        $postData['parent']   = $postData['parent'] ?? 0;
        $postData['priority'] = $postData['priority'] ?? 0;

        if ($this->categoriesModel->save($postData)) {
            $redirectUrl = '/admin-panel/news-categories';
            if ($postData['parent'] > 0) {
                $redirectUrl .= '?parent=' . $postData['parent'];
            }
            return redirect()->to($redirectUrl)
                ->with('success', 'Категория успешно создана');
        }

        return redirect()->back()
            ->with('errors', $this->categoriesModel->errors())
            ->withInput();
    }

    /**
     * Отображение формы редактирования категории
     *
     * @route GET /admin-panel/news-categories/edit/{id}
     *
     * @param int $id ID категории
     * @return RedirectResponse|string HTML форма или редирект при ошибке
     */
    public function edit(int $id)
    {
        $category = $this->categoriesModel->find($id);
        if (!$category) {
            return redirect()->to('/admin-panel/news-categories')
                ->with('error', 'Категория не найдена');
        }

        $data = [
            'title'      => 'Редактирование категории новостей',
            'activeMenu' => 'news_categories',
            'category'   => $category,
            'categories' => $this->categoriesModel->getForSelect($id),
        ];

        return view('admin/news_categories/form', $data);
    }

    /**
     * Обновление категории
     *
     * @route POST /admin-panel/news-categories/update/{id}
     *
     * @param int $id ID категории
     * @return RedirectResponse Редирект на список категорий или назад с ошибкой
     * @throws ReflectionException
     */
    public function update(int $id): RedirectResponse
    {
        $postData = $this->request->getPost();

        $rules = [
            'name' => "required|min_length[2]|max_length[255]",
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->with('errors', $this->validator->getErrors())
                ->withInput();
        }

        if ($this->categoriesModel->update($id, $postData)) {
            $redirectUrl = '/admin-panel/news-categories';
            if (isset($postData['parent']) && $postData['parent'] > 0) {
                $redirectUrl .= '?parent=' . $postData['parent'];
            }
            return redirect()->to($redirectUrl)
                ->with('success', 'Категория успешно обновлена');
        }

        return redirect()->back()
            ->with('errors', $this->categoriesModel->errors())
            ->withInput();
    }

    /**
     * Удаление категории
     *
     * Перед удалением проверяет наличие дочерних категорий и новостей.
     * Если они есть - удаление запрещено.
     *
     * @route GET /admin-panel/news-categories/delete/{id}
     *
     * @param int $id ID категории
     * @return RedirectResponse Редирект на список категорий с сообщением об успехе/ошибке
     */
    public function delete(int $id): RedirectResponse
    {
        // Проверяем наличие дочерних категорий
        $children = $this->categoriesModel->where('parent', $id)->countAllResults();

        if ($children > 0) {
            return redirect()->back()
                ->with('error', 'Невозможно удалить категорию. Сначала удалите или переместите дочерние категории.');
        }

        // Проверяем наличие новостей в категории
        $newsCount = $this->getNewsCount($id);

        if ($newsCount > 0) {
            return redirect()->back()
                ->with('error', "Невозможно удалить категорию. В ней $newsCount новостей. Сначала переназначьте или удалите новости.");
        }

        if ($this->categoriesModel->delete($id)) {
            return redirect()->to('/admin-panel/news-categories')
                ->with('success', 'Категория удалена');
        }

        return redirect()->back()
            ->with('error', 'Ошибка при удалении');
    }

    /**
     * Получение количества новостей в категории
     *
     * @param int $categoryId ID категории
     * @return int Количество новостей
     */
    private function getNewsCount(int $categoryId): int
    {
        return $this->newsModel->where('category_news', $categoryId)->countAllResults();
    }

    /**
     * Получение хлебных крошек для навигации
     *
     * Формирует цепочку родителей для указанной категории,
     * исключая саму категорию.
     *
     * @param int $id ID категории
     * @return array Массив родительских категорий
     */
    private function getBreadcrumbs(int $id): array
    {
        $breadcrumbs = [];
        $current = $this->categoriesModel->find($id);

        // Поднимаемся по дереву родителей
        while ($current && $current['parent'] > 0) {
            $parent = $this->categoriesModel->find($current['parent']);
            if ($parent) {
                array_unshift($breadcrumbs, $parent);
                $current = $parent;
            } else {
                break;
            }
        }

        return $breadcrumbs;
    }

    /**
     * Массовые действия с категориями
     *
     * Поддерживает массовое удаление категорий.
     * Перед удалением проверяет каждую категорию на наличие дочерних элементов и новостей.
     *
     * @route POST /admin-panel/news-categories/bulk-action
     *
     * @return RedirectResponse Редирект назад с сообщением об успехе/ошибке
     */
    public function bulkAction(): RedirectResponse
    {
        $action = $this->request->getPost('bulk_action');
        $ids = $this->request->getPost('selected_ids');
        $parent = (int)($this->request->getPost('parent') ?? 0);

        if (empty($ids) || empty($action)) {
            return redirect()->back()->with('error', 'Выберите действие и категории');
        }

        if ($action === 'delete') {
            // Проверяем каждую категорию на наличие дочерних элементов или новостей
            $hasError = false;
            foreach ($ids as $id) {
                $children = $this->categoriesModel->where('parent', $id)->countAllResults();
                if ($children > 0) {
                    $hasError = true;
                    break;
                }
                $newsCount = $this->getNewsCount($id);
                if ($newsCount > 0) {
                    $hasError = true;
                    break;
                }
            }

            if ($hasError) {
                return redirect()->back()->with('error', 'Некоторые категории имеют дочерние элементы или новости');
            }

            $this->categoriesModel->whereIn('id', $ids)->delete();
            return redirect()->to('/admin-panel/news-categories?parent=' . $parent)
                ->with('success', 'Категории удалены');
        }

        return redirect()->back()->with('error', 'Неизвестное действие');
    }
}