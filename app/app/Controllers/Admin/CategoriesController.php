<?php

/**
 * Контроллер управления категориями файлов
 *
 * Предоставляет методы для CRUD операций с категориями файлов:
 * - Список категорий с деревом
 * - Создание/редактирование/удаление категорий
 * - Управление вложенностью
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
use CodeIgniter\HTTP\RedirectResponse;
use ReflectionException;

/**
 * Контроллер управления категориями файлов
 *
 * @package App\Controllers\Admin
 */
class CategoriesController extends BaseController
{
    /**
     * Модель категорий файлов
     *
     * @var NFileManagerCategoriesModel
     */
    protected NFileManagerCategoriesModel $categoriesModel;

    /**
     * Модель файлов
     *
     * @var NFileManagerModel
     */
    protected NFileManagerModel $filesModel;

    /**
     * Конструктор контроллера
     *
     * Инициализирует модели для работы с категориями и файлами.
     */
    public function __construct()
    {
        $this->categoriesModel = new NFileManagerCategoriesModel();
        $this->filesModel = new NFileManagerModel();
    }

    /**
     * Отображение списка категорий (деревом)
     *
     * Показывает категории текущего уровня с возможностью навигации по вложенности.
     * Для каждой категории отображается количество файлов и наличие дочерних категорий.
     *
     * @route GET /admin-panel/categories
     *
     * @return string HTML страница со списком категорий
     */
    public function index(): string
    {
        // Получаем ID родительской категории из GET параметра
        $parent = (int)($this->request->getGet('parent') ?? 0);

        // Получаем категории для текущего уровня
        $categories = $this->categoriesModel
            ->where('parent', $parent)
            ->orderBy('priority', 'ASC')
            ->orderBy('name', 'ASC')
            ->findAll();

        // Добавляем количество файлов и флаг наличия дочерних категорий
        foreach ($categories as &$cat) {
            $cat['files_count'] = $this->categoriesModel->getFilesCount($cat['id']);
            $cat['has_children'] = $this->categoriesModel->hasChildren($cat['id']);
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
            'title'                  => 'Категории файлов',
            'activeMenu'             => 'categories',
            'categories'             => $categories,
            'parent_id'              => $parent,
            'breadcrumbs'            => $breadcrumbs,
            'current_category_name'  => $currentCategoryName,
        ];

        return view('admin/categories/index', $data);
    }

    /**
     * Отображение формы создания категории
     *
     * @route GET /admin-panel/categories/create
     *
     * @return string HTML форма создания категории
     */
    public function create(): string
    {
        $parent = (int)($this->request->getGet('parent') ?? 0);

        $data = [
            'title'      => 'Создание категории',
            'activeMenu' => 'categories',
            'parent_id'  => $parent,
            'categories' => $this->categoriesModel->getForSelect(),
        ];

        return view('admin/categories/form', $data);
    }

    /**
     * Сохранение новой категории
     *
     * @route POST /admin-panel/categories/store
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
        $postData['parent'] = $postData['parent'] ?? 0;
        $postData['priority'] = $postData['priority'] ?? 0;

        if ($this->categoriesModel->save($postData)) {
            $redirectUrl = '/admin-panel/categories';
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
     * @route GET /admin-panel/categories/edit/{id}
     *
     * @param int $id ID категории
     * @return RedirectResponse|string HTML форма или редирект при ошибке
     */
    public function edit(int $id)
    {
        $category = $this->categoriesModel->find($id);
        if (!$category) {
            return redirect()->to('/admin-panel/categories')
                ->with('error', 'Категория не найдена');
        }

        $data = [
            'title'      => 'Редактирование категории',
            'activeMenu' => 'categories',
            'category'   => $category,
            'categories' => $this->categoriesModel->getForSelect($id),
        ];

        return view('admin/categories/form', $data);
    }

    /**
     * Обновление категории
     *
     * @route POST /admin-panel/categories/update/{id}
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
            $redirectUrl = '/admin-panel/categories';
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
     * Перед удалением проверяет наличие дочерних категорий и файлов.
     * Если они есть - удаление запрещено.
     *
     * @route GET /admin-panel/categories/delete/{id}
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

        // Проверяем наличие файлов в категории
        $filesCount = $this->categoriesModel->getFilesCount($id);

        if ($filesCount > 0) {
            return redirect()->back()
                ->with('error', "Невозможно удалить категорию. В ней $filesCount файлов. Сначала переназначьте или удалите файлы.");
        }

        if ($this->categoriesModel->delete($id)) {
            return redirect()->to('/admin-panel/categories')
                ->with('success', 'Категория удалена');
        }

        return redirect()->back()
            ->with('error', 'Ошибка при удалении');
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
}