<?php

/**
 * Контроллер управления категориями новостей
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
use App\Models\NNewsCategoriesModel;
use App\Models\NNewsArticlesModel;
use CodeIgniter\HTTP\RedirectResponse;
use ReflectionException;

class NewsCategoriesController extends BaseController
{
    protected NNewsCategoriesModel $categoriesModel;
    protected NNewsArticlesModel $newsModel;

    public function __construct()
    {
        $this->categoriesModel = new NNewsCategoriesModel();
        $this->newsModel = new NNewsArticlesModel();
    }

    /**
     * Список категорий (деревом)
     */
    public function index(): string
    {
        $parent = $this->request->getGet('parent') ?? 0;

        // Получаем категории для текущего уровня
        $categories = $this->categoriesModel->where('parent', $parent)
            ->orderBy('priority', 'ASC')
            ->orderBy('name', 'ASC')
            ->findAll();

        // Добавляем количество новостей
        foreach ($categories as &$cat) {
            $cat['news_count'] = $this->getNewsCount($cat['id']);
            $cat['has_children'] = $this->categoriesModel->where('parent', $cat['id'])->countAllResults() > 0;
        }

        // Получаем хлебные крошки
        $breadcrumbs = [];
        $current_category_name = '';
        if ($parent > 0) {
            $currentCategory = $this->categoriesModel->find($parent);
            if ($currentCategory) {
                $current_category_name = $currentCategory['name'];
                $breadcrumbs = $this->getBreadcrumbs($parent);
            }
        }

        $data = [
            'title'                 => 'Категории новостей',
            'activeMenu'            => 'news_categories',
            'categories'            => $categories,
            'parent_id'             => $parent,
            'breadcrumbs'           => $breadcrumbs,
            'current_category_name' => $current_category_name,
        ];

        return view('admin/news_categories/index', $data);
    }

    /**
     * Форма создания категории
     */
    public function create(): string
    {
        $parent = $this->request->getGet('parent') ?? 0;

        $data = [
            'title'      => 'Создание категории новостей',
            'activeMenu' => 'news_categories',
            'parent_id'  => $parent,
            'categories' => $this->categoriesModel->getForSelect(),
        ];
        return view('admin/news_categories/form', $data);
    }

    /**
     * Сохранение категории
     * @throws ReflectionException
     */
    public function store(): RedirectResponse
    {
        $postData = $this->request->getPost();

        $rules = [
            'name' => 'required|min_length[2]|max_length[255]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->with('errors', $this->validator->getErrors())
                ->withInput();
        }

        // Устанавливаем parent
        $postData['parent'] = $postData['parent'] ?? 0;
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
     * Форма редактирования категории
     */
    public function edit(int $id)
    {
        $category = $this->categoriesModel->find($id);
        if (!$category) {
            return redirect()->to('/admin-panel/news-categories')->with('error', 'Категория не найдена');
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
     */
    public function delete(int $id): RedirectResponse
    {
        // Проверяем, есть ли дочерние категории
        $children = $this->categoriesModel->where('parent', $id)->countAllResults();

        if ($children > 0) {
            return redirect()->back()
                ->with('error', 'Невозможно удалить категорию. Сначала удалите или переместите дочерние категории.');
        }

        // Проверяем, есть ли новости в категории
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
     * Получить количество новостей в категории
     *
     * @param int $categoryId
     * @return int
     */
    private function getNewsCount(int $categoryId): int
    {
        return $this->newsModel->where('category_news', $categoryId)->countAllResults();
    }

    /**
     * Получить хлебные крошки для навигации (без текущего раздела)
     *
     * @param int $id ID категории
     * @return array
     */
    private function getBreadcrumbs(int $id): array
    {
        $breadcrumbs = [];
        $current = $this->categoriesModel->find($id);

        // Собираем цепочку родителей (без самой текущей категории)
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
     * @return RedirectResponse
     */
    public function bulkAction(): RedirectResponse
    {
        $action = $this->request->getPost('bulk_action');
        $ids = $this->request->getPost('selected_ids');
        $parent = $this->request->getPost('parent') ?? 0;

        if (empty($ids) || empty($action)) {
            return redirect()->back()->with('error', 'Выберите действие и категории');
        }

        if ($action === 'delete') {
            // Проверяем, нет ли в категориях дочерних элементов или новостей
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