<?php

/**
 * Контроллер управления страницами сайта
 *
 * Предоставляет методы для CRUD операций со страницами:
 * - Список страниц с фильтрацией, сортировкой и иерархической навигацией
 * - Создание/редактирование/удаление страниц
 * - Управление вложенностью страниц (родитель-потомок)
 * - Управление статусом публикации
 * - Массовые операции со страницами
 * - Генерация иерархических URL-путей
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
use CodeIgniter\HTTP\RedirectResponse;
use ReflectionException;

/**
 * Контроллер управления страницами сайта
 *
 * @package App\Controllers\Admin
 */
class PagesController extends BaseController
{
    /**
     * Конструктор
     *
     * Инициализирует модель страниц (доступна из BaseController).
     */
    public function __construct()
    {
        // Модель страниц уже доступна через $this->pagesModel из BaseController
    }

    /**
     * Отображение списка страниц
     *
     * Поддерживает фильтрацию по родительской странице, статусу публикации,
     * сортировку по различным полям и пагинацию.
     *
     * @route GET /admin-panel/pages
     *
     * @return string HTML страница со списком страниц
     */
    public function index(): string
    {
        // Получаем параметры фильтрации из GET
        $show    = (int)($this->request->getGet('show') ?? 1);
        $sort    = (int)($this->request->getGet('sort') ?? 2);
        $perPage = (int)($this->request->getGet('per_page') ?? 50);
        $parent  = (int)($this->request->getGet('parent') ?? 0);

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
            case 1:  $builder = $builder->orderBy('id', 'ASC'); break;      // по ID возрастание
            case 2:  $builder = $builder->orderBy('id', 'DESC'); break;     // по ID убывание
            case 3:  $builder = $builder->orderBy('name', 'ASC'); break;    // по названию А-Я
            case 4:  $builder = $builder->orderBy('name', 'DESC'); break;   // по названию Я-А
            case 5:  $builder = $builder->orderBy('create', 'ASC'); break;  // по дате создания (старые)
            case 6:  $builder = $builder->orderBy('create', 'DESC'); break; // по дате создания (новые)
            case 7:  $builder = $builder->orderBy('modify', 'ASC'); break;  // по дате изменения (старые)
            case 8:  $builder = $builder->orderBy('modify', 'DESC'); break; // по дате изменения (новые)
            case 9:  $builder = $builder->orderBy('publish', 'DESC'); break; // сначала опубликованные
            case 10: $builder = $builder->orderBy('publish', 'ASC'); break;  // сначала черновики
            default: $builder = $builder->orderBy('id', 'DESC');
        }

        // Пагинация
        $currentPage = (int)($this->request->getGet('page') ?? 1);
        $pages = $builder->paginate($perPage, 'default', $currentPage);
        $pager = $this->pagesModel->pager;

        // Формируем хлебные крошки для навигации
        $breadcrumbs = [];
        $currentPageName = '';
        if ($parent > 0) {
            $currentPageData = $this->pagesModel->find($parent);
            if ($currentPageData) {
                $currentPageName = $currentPageData['name'];
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
            'current_page_name' => $currentPageName,
            'pager'             => $pager,
            'additionalJs'      => '/admin/js/pages.js',
        ];

        return view('admin/pages/index', $data);
    }

    /**
     * Получение хлебных крошек только для родителей (без текущей страницы)
     *
     * @param int $id ID страницы
     * @return array Массив родительских страниц
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
     * Отображение формы создания страницы
     *
     * @route GET /admin-panel/pages/create
     *
     * @return string HTML форма создания страницы
     */
    public function create(): string
    {
        $parent = (int)($this->request->getGet('parent') ?? 0);

        // Получаем список категорий для галереи
        $categoriesModel = new NFileManagerCategoriesModel();
        $mediaCategories = $categoriesModel->getForSelect();

        $data = [
            'title'          => 'Создание страницы',
            'activeMenu'     => 'pages',
            'parent_id'      => $parent,
            'parents'        => $this->pagesModel->where('publish', 1)->findAll(),
            'mediaCategories'=> $mediaCategories,
            'additionalCss'  => '/admin/css/pages.css',
            'additionalJs'   => '/admin/js/pages.js',
        ];

        return view('admin/pages/form', $data);
    }

    /**
     * Очистка SEO полей от лишних пробелов
     *
     * @param array $data Данные для очистки
     * @return array Очищенные данные
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
     * Установка значений по умолчанию для полей страницы
     *
     * @param array $data Данные для установки
     * @return array Данные с заполненными значениями по умолчанию
     */
    private function setDefaultValues(array $data): array
    {
        $defaults = [
            'publish'      => 0,
            'parent'       => 0,
            'priority'     => 0,
            'show_in_menu' => 1,
            'new_on_site'  => 0,
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
     * @return RedirectResponse Редирект на список страниц или назад с ошибкой
     * @throws ReflectionException
     */
    public function store(): RedirectResponse
    {
        $postData = $this->request->getPost();

        // Генерация path из name, если не указан
        if (empty($postData['path']) && !empty($postData['name'])) {
            $postData['path'] = $this->generatePath($postData['name'], (int)($postData['parent'] ?? 0));
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
     * Отображение формы редактирования страницы
     *
     * @route GET /admin-panel/pages/edit/{id}
     *
     * @param int $id ID страницы
     * @return RedirectResponse|string HTML форма или редирект при ошибке
     */
    public function edit(int $id)
    {
        $page = $this->pagesModel->find($id);

        if (!$page) {
            return redirect()->to('/admin-panel/pages')
                ->with('error', 'Страница не найдена');
        }

        // Получаем список категорий для галереи
        $categoriesModel = new NFileManagerCategoriesModel();
        $mediaCategories = $categoriesModel->getForSelect();

        $data = [
            'title'          => 'Редактирование страницы',
            'activeMenu'     => 'pages',
            'page'           => $page,
            'parents'        => $this->pagesModel->where('publish', 1)
                ->where('id !=', $id)
                ->findAll(),
            'mediaCategories'=> $mediaCategories,
            'additionalCss'  => '/admin/css/pages.css',
            'additionalJs'   => '/admin/js/pages.js',
        ];

        return view('admin/pages/form', $data);
    }

    /**
     * Обновление страницы
     *
     * @route POST /admin-panel/pages/update/{id}
     *
     * @param int $id ID страницы
     * @return RedirectResponse Редирект на список страниц или назад с ошибкой
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
     * Перед удалением проверяет наличие дочерних страниц.
     *
     * @route GET /admin-panel/pages/delete/{id}
     *
     * @param int $id ID страницы
     * @return RedirectResponse Редирект на список страниц с сообщением об успехе/ошибке
     */
    public function delete(int $id): RedirectResponse
    {
        // Проверяем наличие дочерних страниц
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
     * Переключение статуса публикации страницы
     *
     * @route GET /admin-panel/pages/toggle/{id}
     *
     * @param int $id ID страницы
     * @return RedirectResponse Редирект назад с сообщением об успехе/ошибке
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
     * Массовые действия со страницами
     *
     * Поддерживает массовую публикацию, снятие с публикации и удаление.
     * При удалении проверяет отсутствие дочерних страниц.
     *
     * @route POST /admin-panel/pages/bulk-action
     *
     * @return RedirectResponse Редирект назад с сообщением об успехе/ошибке
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
                // Проверяем наличие дочерних страниц у каждой
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
     * Генерация иерархического пути (slug) из названия страницы
     *
     * Транслитерирует русские символы, заменяет пробелы на дефисы,
     * удаляет недопустимые символы. Учитывает родительскую страницу
     * для формирования полного пути и проверяет уникальность.
     *
     * @param string $name Название страницы
     * @param int $parent Родительская страница (ID)
     * @return string Сгенерированный уникальный путь
     */
    private function generatePath(string $name, int $parent = 0): string
    {
        // Генерируем slug из названия
        $slug = mb_strtolower($name, 'UTF-8');
        $slug = str_replace([' ', '_', '.'], '-', $slug);
        $slug = preg_replace('/[^a-zа-я0-9-]/ui', '', $slug);
        $slug = preg_replace('/-+/', '-', $slug);
        $slug = trim($slug, '-');

        // Если есть родитель, формируем полный иерархический путь
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

        // Проверяем уникальность пути
        $count = $this->pagesModel->where('path', $path)->countAllResults();
        if ($count > 0) {
            $path .= '-' . ($count + 1);
        }

        return $path;
    }
}