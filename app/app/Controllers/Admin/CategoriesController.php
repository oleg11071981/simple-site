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
     * Список категорий
     *
     * @route GET /admin-panel/categories
     * @return string
     */
    public function index(): string
    {
        $perPage = $this->request->getGet('per_page') ?? 50;
        $search = $this->request->getGet('search') ?? '';

        $builder = $this->categoriesModel;

        if (!empty($search)) {
            $builder = $builder->like('name', $search);
        }

        $categories = $builder->orderBy('name', 'ASC')->paginate($perPage);
        $pager = $this->categoriesModel->pager;

        // Подсчитываем количество файлов в каждой категории
        foreach ($categories as &$cat) {
            $cat['files_count'] = $this->filesModel->where('category', $cat['id'])->countAllResults();
        }

        $data = [
            'title'         => 'Категории файлов',
            'activeMenu'    => 'categories',
            'categories'    => $categories,
            'search'        => $search,
            'per_page'      => $perPage,
            'pager'         => $pager,
        ];

        return view('admin/categories/index', $data);
    }

    /**
     * Форма создания категории
     *
     * @route GET /admin-panel/categories/create
     * @return string
     */
    public function create(): string
    {
        $data = [
            'title'      => 'Создание категории',
            'activeMenu' => 'categories',
        ];
        return view('admin/categories/form', $data);
    }

    /**
     * Сохранение категории
     *
     * @route POST /admin-panel/categories/store
     * @return RedirectResponse
     * @throws ReflectionException
     */
    public function store(): RedirectResponse
    {
        $postData = $this->request->getPost();

        $rules = [
            'name' => 'required|min_length[2]|max_length[255]|is_unique[n_file_manager_categories.name]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->with('errors', $this->validator->getErrors())
                ->withInput();
        }

        if ($this->categoriesModel->save($postData)) {
            return redirect()->to('/admin-panel/categories')
                ->with('success', 'Категория успешно создана');
        }

        return redirect()->back()
            ->with('errors', $this->categoriesModel->errors())
            ->withInput();
    }

    /**
     * Форма редактирования категории
     *
     * @param int $id ID категории
     * @return string|RedirectResponse
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
        ];
        return view('admin/categories/form', $data);
    }

    /**
     * Обновление категории
     *
     * @param int $id ID категории
     * @return RedirectResponse
     * @throws ReflectionException
     */
    public function update(int $id): RedirectResponse
    {
        $postData = $this->request->getPost();

        $rules = [
            'name' => "required|min_length[2]|max_length[255]|is_unique[n_file_manager_categories.name,id,$id]",
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->with('errors', $this->validator->getErrors())
                ->withInput();
        }

        if ($this->categoriesModel->update($id, $postData)) {
            return redirect()->to('/admin-panel/categories')
                ->with('success', 'Категория успешно обновлена');
        }

        return redirect()->back()
            ->with('errors', $this->categoriesModel->errors())
            ->withInput();
    }

    /**
     * Удаление категории
     *
     * @param int $id ID категории
     * @return RedirectResponse
     */
    public function delete(int $id): RedirectResponse
    {
        // Проверяем, есть ли файлы в категории
        $filesCount = $this->filesModel->where('category', $id)->countAllResults();

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
}