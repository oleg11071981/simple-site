<?php

/**
 * Контроллер управления категориями файлов
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
use App\Models\NFileManagerCategoriesModel;
use App\Models\NFileManagerModel;
use CodeIgniter\HTTP\RedirectResponse;
use ReflectionException;

class CategoriesController extends BaseController
{
    protected NFileManagerCategoriesModel $categoriesModel;
    protected NFileManagerModel $filesModel;

    public function __construct()
    {
        $this->categoriesModel = new NFileManagerCategoriesModel();
        $this->filesModel = new NFileManagerModel();
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

        // Добавляем количество файлов
        foreach ($categories as &$cat) {
            $cat['files_count'] = $this->categoriesModel->getFilesCount($cat['id']);
            $cat['has_children'] = $this->categoriesModel->hasChildren($cat['id']);
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
            'title'                => 'Категории файлов',
            'activeMenu'           => 'categories',
            'categories'           => $categories,
            'parent_id'            => $parent,
            'breadcrumbs'          => $breadcrumbs,
            'current_category_name' => $current_category_name,
        ];

        return view('admin/categories/index', $data);
    }

    /**
     * Форма создания категории
     */
    public function create(): string
    {
        $parent = $this->request->getGet('parent') ?? 0;

        $data = [
            'title'      => 'Создание категории',
            'activeMenu' => 'categories',
            'parent_id'  => $parent,
            'categories' => $this->categoriesModel->getForSelect(),
        ];
        return view('admin/categories/form', $data);
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
     * Форма редактирования категории
     */
    public function edit(int $id)
    {
        $category = $this->categoriesModel->find($id);
        if (!$category) {
            return redirect()->to('/admin-panel/categories')->with('error', 'Категория не найдена');
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
            if ($postData['parent'] > 0) {
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

        // Проверяем, есть ли файлы в категории
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

}