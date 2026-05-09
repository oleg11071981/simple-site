<?php

namespace App\Controllers;

use App\Models\NProjectsModel;
use App\Models\NProjectEventsModel;
use App\Models\NFileManagerModel;
use App\Models\NSiteconfigModel;
use App\Models\NSiteModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class ProjectsController extends BaseController
{
    protected NProjectsModel $projectsModel;
    protected NProjectEventsModel $eventsModel;
    protected NSiteconfigModel $settingsModel;
    protected NSiteModel $pagesModel;

    public function __construct()
    {
        $this->projectsModel = new NProjectsModel();
        $this->eventsModel = new NProjectEventsModel();
        $this->settingsModel = new NSiteconfigModel();
        $this->pagesModel = new NSiteModel();
    }

    /**
     * Список всех проектов
     * GET /projects
     */
    public function index(): string
    {
        $settings = $this->settingsModel->getAll();

        $projects = $this->projectsModel->getPublished();

        // Добавляем информацию о главном изображении для каждого проекта
        $fileModel = new NFileManagerModel();
        foreach ($projects as &$project) {
            if ($project['foto'] > 0) {
                $file = $fileModel->find($project['foto']);
                if ($file) {
                    $project['foto_file'] = $file['file_name'];
                }
            }
            // Добавляем количество мероприятий
            $project['events_count'] = $this->eventsModel->getEventsCount($project['id']);
        }

        $data = [
            'title'       => 'Проекты | ' . ($settings['SiteName'] ?? 'n-cms'),
            'description' => 'Проекты и мероприятия организации',
            'keywords'    => 'проекты, мероприятия, события',
            'projects'    => $projects,
            'menuPages'   => $this->pagesModel->getMenuPages(),
            'activePage'  => 'projects',
            'currentPage' => 'Проекты',
            // Контакты для подвала
            'email'       => $settings['Email'] ?? '',
            'phone'       => $settings['Phone'] ?? '',
            'address'     => $settings['Adress'] ?? ''
        ];

        return view('site/projects/index', $data);
    }

    /**
     * Детальная страница проекта
     * GET /projects/{slug}
     */
    public function detail(string $slug): string
    {
        $settings = $this->settingsModel->getAll();

        $project = $this->projectsModel->getByPath($slug);

        if (!$project) {
            throw PageNotFoundException::forPageNotFound();
        }

        // Получаем главное изображение
        $fileModel = new NFileManagerModel();
        if ($project['foto'] > 0) {
            $file = $fileModel->find($project['foto']);
            if ($file) {
                $project['foto_file'] = $file['file_name'];
            }
        }

        // Получаем галерею проекта
        $galleryFiles = [];
        if ($project['media'] > 0) {
            $files = $fileModel->getFilesByCategory($project['media']);
            foreach ($files as &$file) {
                $file['size_formatted'] = $this->formatFileSize($file['file_size']);
            }
            $galleryFiles = $files;
        }

        // Получаем мероприятия проекта
        $events = $this->eventsModel->getByProjectId($project['id']);

        // Добавляем изображения к мероприятиям
        foreach ($events as &$event) {
            if ($event['foto'] > 0) {
                $file = $fileModel->find($event['foto']);
                if ($file) {
                    $event['foto_file'] = $file['file_name'];
                }
            }
        }

        $data = [
            'title'       => $project['name'] . ' | ' . ($settings['SiteName'] ?? 'n-cms'),
            'description' => $project['description'] ?: $project['anons_text'],
            'keywords'    => $project['keywords'] ?: $settings['Keywords'] ?? '',
            'project'     => $project,
            'events'      => $events,
            'galleryFiles'=> $galleryFiles,
            'menuPages'   => $this->pagesModel->getMenuPages(),
            'activePage'  => 'projects',
            'breadcrumbs' => [
                ['name' => 'Проекты', 'url' => '/projects']
            ],
            'currentPage' => $project['name'],
            // Контакты для подвала
            'email'       => $settings['Email'] ?? '',
            'phone'       => $settings['Phone'] ?? '',
            'address'     => $settings['Adress'] ?? ''
        ];

        return view('site/projects/detail', $data);
    }

    /**
     * Детальная страница мероприятия
     * GET /projects/{project_slug}/{event_slug}
     */
    public function eventDetail(string $projectSlug, string $eventSlug): string
    {
        $settings = $this->settingsModel->getAll();

        $event = $this->eventsModel->getByProjectPathAndEventPath($projectSlug, $eventSlug);

        if (!$event) {
            throw PageNotFoundException::forPageNotFound();
        }

        // Получаем проект
        $project = $this->projectsModel->find($event['project_id']);

        // Получаем главное изображение мероприятия
        $fileModel = new NFileManagerModel();
        if ($event['foto'] > 0) {
            $file = $fileModel->find($event['foto']);
            if ($file) {
                $event['foto_file'] = $file['file_name'];
            }
        }

        // Получаем галерею мероприятия
        $galleryFiles = [];
        if ($event['media'] > 0) {
            $files = $fileModel->getFilesByCategory($event['media']);
            foreach ($files as &$file) {
                $file['size_formatted'] = $this->formatFileSize($file['file_size']);
            }
            $galleryFiles = $files;
        }

        // Получаем другие мероприятия этого проекта
        $otherEvents = $this->eventsModel->where('project_id', $project['id'])
            ->where('id !=', $event['id'])
            ->where('publish', 1)
            ->orderBy('date_start', 'ASC')
            ->limit(3)
            ->findAll();

        foreach ($otherEvents as &$other) {
            if ($other['foto'] > 0) {
                $file = $fileModel->find($other['foto']);
                if ($file) {
                    $other['foto_file'] = $file['file_name'];
                }
            }
        }

        $data = [
            'title'       => $event['name'] . ' | ' . ($settings['SiteName'] ?? 'n-cms'),
            'description' => $event['description'] ?: $event['anons_text'],
            'keywords'    => '',
            'event'       => $event,
            'project'     => $project,
            'galleryFiles'=> $galleryFiles,
            'otherEvents' => $otherEvents,
            'menuPages'   => $this->pagesModel->getMenuPages(),
            'activePage'  => 'projects',
            'breadcrumbs' => [
                ['name' => 'Проекты', 'url' => '/projects'],
                ['name' => $project['name'], 'url' => '/projects/' . $project['path']]
            ],
            'currentPage' => $event['name'],
            // Контакты для подвала
            'email'       => $settings['Email'] ?? '',
            'phone'       => $settings['Phone'] ?? '',
            'address'     => $settings['Adress'] ?? ''
        ];

        return view('site/projects/event_detail', $data);
    }

    /**
     * Форматирование размера файла
     */
    private function formatFileSize(int $bytes): string
    {
        if ($bytes < 1024) return $bytes . ' Б';
        if ($bytes < 1048576) return round($bytes / 1024, 1) . ' КБ';
        if ($bytes < 1073741824) return round($bytes / 1048576, 1) . ' МБ';
        return round($bytes / 1073741824, 1) . ' ГБ';
    }
}