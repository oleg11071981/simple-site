<?php

/**
 * Контроллер управления страницами сайта
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
use App\Models\NSiteModel;
use CodeIgniter\HTTP\RedirectResponse;
use ReflectionException;

/**
 * Контроллер страниц
 */
class PagesController extends BaseController
{
    /**
     * Модель страниц
     *
     * @var NSiteModel
     */
    protected NSiteModel $pagesModel;

    /**
     * Конструктор
     */
    public function __construct()
    {
        $this->pagesModel = new NSiteModel();
    }

    /**
     * Список страниц
     *
     * @route GET /admin-panel/pages
     *
     * @return string
     */
    public function index(): string
    {
        // Получаем параметры фильтрации из GET
        $show = $this->request->getGet('show') ?? 1;
        $sort = $this->request->getGet('sort') ?? 2;
        $perPage = $this->request->getGet('per_page') ?? 50;
        $parent = $this->request->getGet('parent') ?? 0;

        // Строим запрос
        $builder = $this->pagesModel;

        // Фильтр по родительской странице
        $builder = $builder->where('parent', $parent);

        // Фильтр по статусу публикации
        if ($show == 2) {
            $builder = $builder->where('publish', 1);
        } elseif ($show == 3) {
            $builder = $builder->where('publish', 0);
        }

        // Сортировка
        switch ($sort) {
            case 1: // по ID возрастание
                $builder = $builder->orderBy('id', 'ASC');
                break;
            case 2: // по ID убывание
                $builder = $builder->orderBy('id', 'DESC');
                break;
            case 3: // по названию А-Я
                $builder = $builder->orderBy('name', 'ASC');
                break;
            case 4: // по названию Я-А
                $builder = $builder->orderBy('name', 'DESC');
                break;
            case 5: // по дате создания (старые)
                $builder = $builder->orderBy('create', 'ASC');
                break;
            case 6: // по дате создания (новые)
                $builder = $builder->orderBy('create', 'DESC');
                break;
            case 7: // по дате изменения (старые)
                $builder = $builder->orderBy('modify', 'ASC');
                break;
            case 8: // по дате изменения (новые)
                $builder = $builder->orderBy('modify', 'DESC');
                break;
            case 9: // по статусу (сначала опубликованные)
                $builder = $builder->orderBy('publish', 'DESC');
                break;
            case 10: // по статусу (сначала черновики)
                $builder = $builder->orderBy('publish', 'ASC');
                break;
            default:
                $builder = $builder->orderBy('id', 'DESC');
        }

        // Пагинация
        $pages = $builder->paginate($perPage, 'default', null, (int)['page' => $this->request->getGet('page') ?? 1]);
        $pager = $this->pagesModel->pager;

        // Получаем хлебные крошки для навигации (только родители, без текущего раздела)
        $breadcrumbs = [];
        $current_page_name = '';
        if ($parent > 0) {
            // Получаем название текущего раздела
            $currentPage = $this->pagesModel->find($parent);
            if ($currentPage) {
                $current_page_name = $currentPage['name'];
                // Получаем только родителей (без текущего раздела)
                $breadcrumbs = $this->getParentBreadcrumbs($parent);
            }
        }

        $data = [
            'title'             => 'Управление страницами',
            'activeMenu'        => 'pages',
            'pages'             => $pages,
            'show'              => $show,
            'sort'              => $sort,
            'per_page'          => $perPage,
            'parent_id'         => $parent,
            'breadcrumbs'       => $breadcrumbs,
            'current_page_name' => $current_page_name,
            'pager'             => $pager,
            'additionalJs'      => '/admin/js/pages.js',
        ];

        return view('admin/pages/index', $data);
    }

    /**
     * Получить хлебные крошки только для родителей (без текущей страницы)
     *
     * @param int $id ID страницы
     * @return array
     */
    private function getParentBreadcrumbs(int $id): array
    {
        $breadcrumbs = [];
        $current = $this->pagesModel->find($id);

        // Собираем всех родителей (рекурсивно, до корня)
        $parents = [];
        while ($current && $current['parent'] > 0) {
            $parent = $this->pagesModel->find($current['parent']);
            if ($parent) {
                array_unshift($parents, $parent);
                $current = $parent;
            } else {
                break;
            }
        }

        foreach ($parents as $parent) {
            $breadcrumbs[] = [
                'id'   => $parent['id'],
                'name' => $parent['name'],
            ];
        }

        return $breadcrumbs;
    }

    /**
     * Получить хлебные крошки для навигации (без текущего раздела)
     *
     * @param int $id ID страницы
     * @return array
     * @deprecated Use getParentBreadcrumbs instead
     */
    private function getBreadcrumbs(int $id): array
    {
        return $this->getParentBreadcrumbs($id);
    }

    /**
     * Форма создания страницы
     *
     * @route GET /admin-panel/pages/create
     *
     * @return string
     */
    public function create(): string
    {
        // Получаем parent из GET параметра
        $parent = $this->request->getGet('parent') ?? 0;

        $data = [
            'title'         => 'Создание страницы',
            'activeMenu'    => 'pages',
            'parent_id'     => $parent,
            'parents'       => $this->pagesModel->where('publish', 1)->findAll(),
            'additionalCss' => '/admin/css/pages.css',
            'additionalJs'  => '/admin/js/pages.js',
        ];

        return view('admin/pages/form', $data);
    }

    /**
     * Очистка SEO полей от лишних пробелов
     *
     * @param array $data Данные для очистки
     * @return array
     */
    private function cleanSeoFields(array $data): array
    {
        $seoFields = ['keywords', 'description', 'anons_text'];

        foreach ($seoFields as $field) {
            if (isset($data[$field])) {
                $data[$field] = trim($data[$field]);
            }
        }

        return $data;
    }

    /**
     * Установка значений по умолчанию
     *
     * @param array $data Данные для установки
     * @return array
     */
    private function setDefaultValues(array $data): array
    {
        $defaults = [
            'publish'       => 0,
            'parent'        => 0,
            'priority'      => 0,
            'show_in_menu'  => 1,
            'new_on_site'   => 0,
        ];

        foreach ($defaults as $key => $value) {
            if (!isset($data[$key])) {
                $data[$key] = $value;
            }
        }

        return $data;
    }

    /**
     * Сохранение новой страницы
     *
     * @route POST /admin-panel/pages/store
     *
     * @return RedirectResponse
     * @throws ReflectionException
     */
    public function store(): RedirectResponse
    {
        $postData = $this->request->getPost();

        // Генерация path из name, если не указан
        if (empty($postData['path']) && !empty($postData['name'])) {
            $postData['path'] = $this->generatePath($postData['name'], $postData['parent'] ?? 0);
        }

        // Очищаем SEO поля
        $postData = $this->cleanSeoFields($postData);

        // Устанавливаем значения по умолчанию
        $postData = $this->setDefaultValues($postData);

        // Убеждаемся, что parent передан
        if (!isset($postData['parent'])) {
            $postData['parent'] = 0;
        }

        if ($this->pagesModel->save($postData)) {
            // После создания возвращаемся в тот же раздел
            $redirectUrl = '/admin-panel/pages';
            if ($postData['parent'] > 0) {
                $redirectUrl .= '?parent=' . $postData['parent'];
            }
            return redirect()->to($redirectUrl)
                ->with('success', 'Страница успешно создана');
        }

        return redirect()->back()
            ->with('errors', $this->pagesModel->errors())
            ->withInput();
    }

    /**
     * Форма редактирования страницы
     *
     * @param int $id ID страницы
     *
     * @return RedirectResponse|string
     */
    public function edit(int $id): string
    {
        $page = $this->pagesModel->find($id);

        if (!$page) {
            return redirect()->to('/admin-panel/pages')
                ->with('error', 'Страница не найдена');
        }

        $data = [
            'title'         => 'Редактирование страницы',
            'activeMenu'    => 'pages',
            'page'          => $page,
            'parents'       => $this->pagesModel->where('publish', 1)
                ->where('id !=', $id)
                ->findAll(),
            'additionalCss' => '/admin/css/pages.css',
            'additionalJs'  => '/admin/js/pages.js',
        ];

        return view('admin/pages/form', $data);
    }

    /**
     * Обновление страницы
     *
     * @param int $id ID страницы
     *
     * @return RedirectResponse
     * @throws ReflectionException
     */
    public function update(int $id): RedirectResponse
    {
        $postData = $this->request->getPost();

        // Очищаем SEO поля
        $postData = $this->cleanSeoFields($postData);

        // Убеждаемся, что parent передан
        if (!isset($postData['parent'])) {
            $postData['parent'] = 0;
        }

        if ($this->pagesModel->update($id, $postData)) {
            // После обновления возвращаемся в тот же раздел
            $redirectUrl = '/admin-panel/pages';
            if ($postData['parent'] > 0) {
                $redirectUrl .= '?parent=' . $postData['parent'];
            }
            return redirect()->to($redirectUrl)
                ->with('success', 'Страница успешно обновлена');
        }

        return redirect()->back()
            ->with('errors', $this->pagesModel->errors())
            ->withInput();
    }

    /**
     * Удаление страницы
     *
     * @param int $id ID страницы
     *
     * @return RedirectResponse
     */
    public function delete(int $id): RedirectResponse
    {
        // Проверяем, есть ли дочерние страницы
        $children = $this->pagesModel->where('parent', $id)->countAllResults();

        if ($children > 0) {
            return redirect()->back()
                ->with('error', 'Удалите сначала дочерние страницы');
        }

        if ($this->pagesModel->delete($id)) {
            return redirect()->to('/admin-panel/pages')
                ->with('success', 'Страница удалена');
        }

        return redirect()->back()
            ->with('error', 'Ошибка при удалении');
    }

    /**
     * Переключение статуса публикации
     *
     * @param int $id ID страницы
     * @return RedirectResponse
     * @throws ReflectionException
     */
    public function toggle(int $id): RedirectResponse
    {
        $page = $this->pagesModel->find($id);
        if (!$page) {
            return redirect()->back()->with('error', 'Страница не найдена');
        }

        $newStatus = $page['publish'] == 1 ? 0 : 1;
        $this->pagesModel->update($id, ['publish' => $newStatus]);

        $message = $newStatus == 1 ? 'Страница опубликована' : 'Страница снята с публикации';
        return redirect()->back()->with('success', $message);
    }

    /**
     * Массовые действия
     *
     * @return RedirectResponse
     * @throws ReflectionException
     */
    public function bulkAction(): RedirectResponse
    {
        $action = $this->request->getPost('bulk_action');
        $ids = $this->request->getPost('selected_ids');

        if (empty($ids) || empty($action)) {
            return redirect()->back()->with('error', 'Выберите действие и страницы');
        }

        switch ($action) {
            case 'publish':
                $this->pagesModel->whereIn('id', $ids)->set(['publish' => 1])->update();
                $message = 'Страницы опубликованы';
                break;
            case 'unpublish':
                $this->pagesModel->whereIn('id', $ids)->set(['publish' => 0])->update();
                $message = 'Страницы сняты с публикации';
                break;
            case 'delete':
                // Проверяем дочерние страницы
                foreach ($ids as $id) {
                    $children = $this->pagesModel->where('parent', $id)->countAllResults();
                    if ($children > 0) {
                        return redirect()->back()->with('error', 'Некоторые страницы имеют дочерние элементы');
                    }
                }
                $this->pagesModel->whereIn('id', $ids)->delete();
                $message = 'Страницы удалены';
                break;
            default:
                return redirect()->back()->with('error', 'Неизвестное действие');
        }

        return redirect()->back()->with('success', $message);
    }

    /**
     * Генерация пути из названия с учётом родителя (иерархический)
     *
     * @param string $name Название страницы
     * @param int $parent Родительская страница
     * @return string
     */
    private function generatePath(string $name, int $parent = 0): string
    {
        // Генерируем slug из названия
        $slug = mb_strtolower($name, 'UTF-8');
        $slug = str_replace([' ', '_', '.'], '-', $slug);
        $slug = preg_replace('/[^a-zа-я0-9-]/ui', '', $slug);
        $slug = preg_replace('/-+/', '-', $slug);
        $slug = trim($slug, '-');

        // Если есть родитель, получаем его полный путь
        if ($parent > 0) {
            $parentPage = $this->pagesModel->find($parent);
            if ($parentPage && !empty($parentPage['path'])) {
                $path = $parentPage['path'] . '/' . $slug;
            } else {
                $path = $slug;
            }
        } else {
            $path = $slug;
        }

        // Проверяем уникальность
        $count = $this->pagesModel->where('path', $path)->countAllResults();
        if ($count > 0) {
            $path .= '-' . ($count + 1);
        }

        return $path;
    }
}