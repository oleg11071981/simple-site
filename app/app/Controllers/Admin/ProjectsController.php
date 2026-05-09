<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\NProjectsModel;
use App\Models\NProjectEventsModel;
use App\Models\NFileManagerCategoriesModel;
use App\Models\NFileManagerModel;
use CodeIgniter\HTTP\RedirectResponse;
use ReflectionException;

class ProjectsController extends BaseController
{
    protected NProjectsModel $projectsModel;
    protected NProjectEventsModel $eventsModel;

    public function __construct()
    {
        $this->projectsModel = new NProjectsModel();
        $this->eventsModel = new NProjectEventsModel();
    }

    /**
     * Список проектов
     */
    public function index(): string
    {
        $perPage = $this->request->getGet('per_page') ?? 20;
        $search = $this->request->getGet('search') ?? '';
        $publish = $this->request->getGet('publish') ?? '';

        $builder = $this->projectsModel;

        if (!empty($search)) {
            $builder = $builder->like('name', $search);
        }

        if ($publish !== '') {
            $builder = $builder->where('publish', $publish);
        }

        $projects = $builder->orderBy('priority', 'ASC')
            ->orderBy('id', 'DESC')
            ->paginate($perPage);

        $pager = $this->projectsModel->pager;

        // Добавляем количество мероприятий для каждого проекта
        foreach ($projects as &$project) {
            $project['events_count'] = $this->eventsModel->getEventsCount($project['id']);
        }

        $data = [
            'title'       => 'Управление проектами',
            'activeMenu'  => 'projects',
            'projects'    => $projects,
            'pager'       => $pager,
            'search'      => $search,
            'publish'     => $publish,
            'per_page'    => $perPage,
        ];

        return view('admin/projects/index', $data);
    }

    /**
     * Форма создания проекта
     */
    public function create(): string
    {
        $categoriesModel = new NFileManagerCategoriesModel();

        $data = [
            'title'           => 'Создание проекта',
            'activeMenu'      => 'projects',
            'mediaCategories' => $categoriesModel->getForSelect(),
        ];

        return view('admin/projects/form', $data);
    }

    /**
     * Сохранение проекта
     * @throws ReflectionException
     */
    public function store(): RedirectResponse
    {
        $postData = $this->request->getPost();

        $rules = [
            'name' => 'required|min_length[3]|max_length[255]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->with('errors', $this->validator->getErrors())
                ->withInput();
        }

        $postData['publish'] = $postData['publish'] ?? 0;
        $postData['priority'] = $postData['priority'] ?? 0;
        $postData['foto'] = $postData['foto'] ?? 0;
        $postData['media'] = $postData['media'] ?? 0;

        if ($this->projectsModel->save($postData)) {
            return redirect()->to('/admin-panel/projects')
                ->with('success', 'Проект успешно создан');
        }

        return redirect()->back()
            ->with('errors', $this->projectsModel->errors())
            ->withInput();
    }

    /**
     * Форма редактирования проекта (с вкладкой мероприятий)
     */
    public function edit(int $id)
    {
        $project = $this->projectsModel->find($id);

        if (!$project) {
            return redirect()->to('/admin-panel/projects')
                ->with('error', 'Проект не найден');
        }

        // Получаем мероприятия проекта
        $events = $this->eventsModel->where('project_id', $id)
            ->orderBy('priority', 'ASC')
            ->orderBy('date_start', 'ASC')
            ->findAll();

        // Получаем главное изображение
        if ($project['foto'] > 0) {
            $fileModel = new NFileManagerModel();
            $file = $fileModel->find($project['foto']);
            if ($file) {
                $project['foto_file'] = $file['file_name'];
            }
        }

        $categoriesModel = new NFileManagerCategoriesModel();

        $data = [
            'title'           => 'Редактирование проекта',
            'activeMenu'      => 'projects',
            'project'         => $project,
            'events'          => $events,
            'mediaCategories' => $categoriesModel->getForSelect(),
            'eventModel'      => $this->eventsModel,
        ];

        return view('admin/projects/form', $data);
    }

    /**
     * Обновление проекта
     * @throws ReflectionException
     */
    public function update(int $id): RedirectResponse
    {
        $postData = $this->request->getPost();

        $rules = [
            'name' => 'required|min_length[3]|max_length[255]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->with('errors', $this->validator->getErrors())
                ->withInput();
        }

        if ($this->projectsModel->update($id, $postData)) {
            return redirect()->to('/admin-panel/projects')
                ->with('success', 'Проект успешно обновлён');
        }

        return redirect()->back()
            ->with('errors', $this->projectsModel->errors())
            ->withInput();
    }

    /**
     * Удаление проекта (вместе с мероприятиями)
     */
    public function delete(int $id): RedirectResponse
    {
        $project = $this->projectsModel->find($id);

        if (!$project) {
            return redirect()->back()
                ->with('error', 'Проект не найден');
        }

        // Проверяем, есть ли мероприятия
        $eventsCount = $this->eventsModel->getEventsCount($id);

        if ($eventsCount > 0) {
            return redirect()->back()
                ->with('error', "Сначала удалите {$eventsCount} мероприятий, связанных с этим проектом");
        }

        if ($this->projectsModel->delete($id)) {
            return redirect()->to('/admin-panel/projects')
                ->with('success', 'Проект удалён');
        }

        return redirect()->back()
            ->with('error', 'Ошибка при удалении');
    }

    /**
     * Переключение статуса публикации
     * @throws ReflectionException
     */
    public function toggle(int $id): RedirectResponse
    {
        $project = $this->projectsModel->find($id);

        if (!$project) {
            return redirect()->back()
                ->with('error', 'Проект не найден');
        }

        $newStatus = $project['publish'] == 1 ? 0 : 1;
        $this->projectsModel->update($id, ['publish' => $newStatus]);

        $message = $newStatus == 1 ? 'Проект опубликован' : 'Проект снят с публикации';
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
            return redirect()->back()
                ->with('error', 'Выберите действие и проекты');
        }

        switch ($action) {
            case 'publish':
                $this->projectsModel->whereIn('id', $ids)
                    ->set(['publish' => 1])
                    ->update();
                $message = 'Проекты опубликованы';
                break;

            case 'unpublish':
                $this->projectsModel->whereIn('id', $ids)
                    ->set(['publish' => 0])
                    ->update();
                $message = 'Проекты сняты с публикации';
                break;

            case 'delete':
                // Проверяем, есть ли у проектов мероприятия
                foreach ($ids as $id) {
                    $eventsCount = $this->eventsModel->getEventsCount($id);
                    if ($eventsCount > 0) {
                        return redirect()->back()
                            ->with('error', 'Некоторые проекты имеют мероприятия. Удалите их сначала.');
                    }
                }
                $this->projectsModel->whereIn('id', $ids)->delete();
                $message = 'Проекты удалены';
                break;

            default:
                return redirect()->back()
                    ->with('error', 'Неизвестное действие');
        }

        return redirect()->back()->with('success', $message);
    }
}