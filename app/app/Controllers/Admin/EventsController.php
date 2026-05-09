<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\NProjectsModel;
use App\Models\NProjectEventsModel;
use App\Models\NFileManagerCategoriesModel;
use App\Models\NFileManagerModel;
use CodeIgniter\HTTP\RedirectResponse;
use ReflectionException;

class EventsController extends BaseController
{
    protected NProjectEventsModel $eventsModel;
    protected NProjectsModel $projectsModel;

    public function __construct()
    {
        $this->eventsModel = new NProjectEventsModel();
        $this->projectsModel = new NProjectsModel();
    }

    /**
     * Список мероприятий (опционально)
     */
    public function index(): string
    {
        $perPage = $this->request->getGet('per_page') ?? 20;
        $search = $this->request->getGet('search') ?? '';
        $projectId = $this->request->getGet('project_id') ?? 0;
        $publish = $this->request->getGet('publish') ?? '';

        $builder = $this->eventsModel->select('n_project_events.*, n_projects.name as project_name')
            ->join('n_projects', 'n_projects.id = n_project_events.project_id', 'left');

        if (!empty($search)) {
            $builder = $builder->like('n_project_events.name', $search);
        }

        if ($projectId > 0) {
            $builder = $builder->where('n_project_events.project_id', $projectId);
        }

        if ($publish !== '') {
            $builder = $builder->where('n_project_events.publish', $publish);
        }

        $events = $builder->orderBy('n_project_events.date_start', 'DESC')
            ->paginate($perPage);

        $pager = $this->eventsModel->pager;

        // Получаем список проектов для фильтра
        $projects = $this->projectsModel->where('publish', 1)
            ->orderBy('name', 'ASC')
            ->findAll();

        $data = [
            'title'      => 'Управление мероприятиями',
            'activeMenu' => 'events',
            'events'     => $events,
            'pager'      => $pager,
            'projects'   => $projects,
            'project_id' => $projectId,
            'search'     => $search,
            'publish'    => $publish,
            'per_page'   => $perPage,
        ];

        return view('admin/events/index', $data);
    }

    /**
     * Форма создания мероприятия (привязанного к проекту)
     */
    public function create()
    {
        $projectId = $this->request->getGet('project_id') ?? 0;

        if (!$projectId) {
            return redirect()->to('/admin-panel/projects')
                ->with('error', 'Выберите проект для добавления мероприятия');
        }

        $project = $this->projectsModel->find($projectId);

        if (!$project) {
            return redirect()->to('/admin-panel/projects')
                ->with('error', 'Проект не найден');
        }

        $categoriesModel = new \App\Models\NFileManagerCategoriesModel();

        $data = [
            'title'           => 'Создание мероприятия',
            'activeMenu'      => 'projects',
            'project'         => $project,
            'project_id'      => $projectId,
            'mediaCategories' => $categoriesModel->getForSelect(),
        ];

        return view('admin/events/form', $data);
    }

    /**
     * Сохранение мероприятия
     * @throws ReflectionException
     */
    public function store(): RedirectResponse
    {
        $postData = $this->request->getPost();

        $rules = [
            'name'       => 'required|min_length[3]|max_length[255]',
            'project_id' => 'required|numeric',
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

        if ($this->eventsModel->save($postData)) {
            $project = $this->projectsModel->find($postData['project_id']);
            return redirect()->to('/admin-panel/projects/edit/' . $postData['project_id'])
                ->with('success', 'Мероприятие успешно создано');
        }

        return redirect()->back()
            ->with('errors', $this->eventsModel->errors())
            ->withInput();
    }

    /**
     * Форма редактирования мероприятия
     */
    public function edit(int $id)
    {
        $event = $this->eventsModel->find($id);

        if (!$event) {
            return redirect()->to('/admin-panel/projects')
                ->with('error', 'Мероприятие не найдено');
        }

        $project = $this->projectsModel->find($event['project_id']);

        if (!$project) {
            return redirect()->to('/admin-panel/projects')
                ->with('error', 'Проект не найден');
        }

        // Получаем главное изображение
        if ($event['foto'] > 0) {
            $fileModel = new \App\Models\NFileManagerModel();
            $file = $fileModel->find($event['foto']);
            if ($file) {
                $event['foto_file'] = $file['file_name'];
            }
        }

        $categoriesModel = new \App\Models\NFileManagerCategoriesModel();

        $data = [
            'title'           => 'Редактирование мероприятия',
            'activeMenu'      => 'projects',
            'event'           => $event,
            'project'         => $project,
            'mediaCategories' => $categoriesModel->getForSelect(),
        ];

        return view('admin/events/form', $data);
    }

    /**
     * Обновление мероприятия
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

        $event = $this->eventsModel->find($id);

        if ($this->eventsModel->update($id, $postData)) {
            return redirect()->to('/admin-panel/projects/edit/' . $event['project_id'])
                ->with('success', 'Мероприятие успешно обновлено');
        }

        return redirect()->back()
            ->with('errors', $this->eventsModel->errors())
            ->withInput();
    }

    /**
     * Удаление мероприятия
     */
    public function delete(int $id): RedirectResponse
    {
        $event = $this->eventsModel->find($id);

        if (!$event) {
            return redirect()->back()
                ->with('error', 'Мероприятие не найдено');
        }

        $projectId = $event['project_id'];

        if ($this->eventsModel->delete($id)) {
            return redirect()->to('/admin-panel/projects/edit/' . $projectId)
                ->with('success', 'Мероприятие удалено');
        }

        return redirect()->back()
            ->with('error', 'Ошибка при удалении');
    }

    /**
     * Переключение статуса публикации мероприятия
     * @throws ReflectionException
     */
    public function toggle(int $id): RedirectResponse
    {
        $event = $this->eventsModel->find($id);

        if (!$event) {
            return redirect()->back()
                ->with('error', 'Мероприятие не найдено');
        }

        $newStatus = $event['publish'] == 1 ? 0 : 1;
        $this->eventsModel->update($id, ['publish' => $newStatus]);

        $message = $newStatus == 1 ? 'Мероприятие опубликовано' : 'Мероприятие снято с публикации';
        return redirect()->back()->with('success', $message);
    }
}