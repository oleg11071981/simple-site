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

    public function __construct()
    {
        $this->projectsModel = new NProjectsModel();
        $this->eventsModel = new NProjectEventsModel();
    }

    /**
     * Список всех проектов
     * GET /projects
     */
    public function index(): string
    {
        $settings = $this->settingsModel->getAll();
        $lang = $this->currentLang;

        $projectsModel = new \App\Models\NProjectsModel();
        $projects = $projectsModel->getPublishedWithLang(0, $lang);

        // Добавляем информацию о главном изображении для каждого проекта
        $fileModel = new \App\Models\NFileManagerModel();
        $eventsModel = new \App\Models\NProjectEventsModel();

        foreach ($projects as &$project) {
            if ($project['foto'] > 0) {
                $file = $fileModel->find($project['foto']);
                if ($file) {
                    $project['foto_file'] = $file['file_name'];
                }
            }
            $project['events_count'] = $eventsModel->getEventsCount($project['id']);
        }

        $data = [
            'title'       => ($lang === 'en' && !empty($settings['SiteName_en'])) ? 'Projects | ' . $settings['SiteName_en'] : 'Проекты | ' . ($settings['SiteName'] ?? 'n-cms'),
            'description' => ($lang === 'en' && !empty($settings['Description_en'])) ? $settings['Description_en'] : 'Проекты и мероприятия организации',
            'keywords'    => ($lang === 'en' && !empty($settings['Keywords_en'])) ? $settings['Keywords_en'] : 'проекты, мероприятия, события',
            'projects'    => $projects,
            'menuPages'   => $this->pagesModel->getMenuPages(),
            'activePage'  => 'projects',
            'currentPage' => ($lang === 'en') ? 'Projects' : 'Проекты',
            'currentLang' => $lang,
            'email'       => $this->contacts['email'],
            'phone'       => $this->contacts['phone'],
            'address'     => $this->contacts['address'],
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
        $lang = $this->currentLang;

        $projectsModel = new \App\Models\NProjectsModel();
        $project = $projectsModel->getByPathWithLang($slug, $lang);

        if (!$project) {
            throw PageNotFoundException::forPageNotFound();
        }

        // Получаем главное изображение
        $fileModel = new \App\Models\NFileManagerModel();
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

        // Получаем мероприятия проекта с учетом языка
        $eventsModel = new \App\Models\NProjectEventsModel();
        $events = $eventsModel->getByProjectIdWithLang($project['id'], $lang);

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
            'title'       => ($lang === 'en' && !empty($project['name_en'])) ? $project['name_en'] . ' | ' . ($settings['SiteName_en'] ?? $settings['SiteName']) : $project['name'] . ' | ' . ($settings['SiteName'] ?? 'n-cms'),
            'description' => $project['description'] ?: $project['anons_text'],
            'keywords'    => $project['keywords'] ?: ($lang === 'en' ? ($settings['Keywords_en'] ?? '') : ($settings['Keywords'] ?? '')),
            'project'     => $project,
            'events'      => $events,
            'galleryFiles'=> $galleryFiles,
            'menuPages'   => $this->pagesModel->getMenuPages(),
            'activePage'  => 'projects',
            'breadcrumbs' => [
                ['name' => ($lang === 'en') ? 'Projects' : 'Проекты', 'url' => '/projects']
            ],
            'currentPage' => ($lang === 'en' && !empty($project['name_en'])) ? $project['name_en'] : $project['name'],
            'currentLang' => $lang,
            'email'       => $this->contacts['email'],
            'phone'       => $this->contacts['phone'],
            'address'     => $this->contacts['address'],
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
        $lang = $this->currentLang;

        $eventsModel = new \App\Models\NProjectEventsModel();
        $event = $eventsModel->getByProjectPathAndEventPathWithLang($projectSlug, $eventSlug, $lang);

        if (!$event) {
            throw PageNotFoundException::forPageNotFound();
        }

        // Получаем проект
        $projectsModel = new \App\Models\NProjectsModel();
        $project = $projectsModel->getByPathWithLang($projectSlug, $lang);

        // Получаем главное изображение мероприятия
        $fileModel = new \App\Models\NFileManagerModel();
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
        $otherEvents = $eventsModel->where('project_id', $project['id'])
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
            // Локализация названия других мероприятий
            if ($lang === 'en' && !empty($other['name_en'])) {
                $other['name'] = $other['name_en'];
            }
        }

        $data = [
            'title'       => ($lang === 'en' && !empty($event['name_en'])) ? $event['name_en'] . ' | ' . ($settings['SiteName_en'] ?? $settings['SiteName']) : $event['name'] . ' | ' . ($settings['SiteName'] ?? 'n-cms'),
            'description' => $event['more_info'] ? strip_tags(substr($event['more_info'], 0, 200)) : ($event['anons_text'] ?? ''),
            'keywords'    => '',
            'event'       => $event,
            'project'     => $project,
            'galleryFiles'=> $galleryFiles,
            'otherEvents' => $otherEvents,
            'menuPages'   => $this->pagesModel->getMenuPages(),
            'activePage'  => 'projects',
            'breadcrumbs' => [
                ['name' => ($lang === 'en') ? 'Projects' : 'Проекты', 'url' => '/projects'],
                ['name' => ($lang === 'en' && !empty($project['name_en'])) ? $project['name_en'] : $project['name'], 'url' => '/projects/' . $project['path']]
            ],
            'currentPage' => ($lang === 'en' && !empty($event['name_en'])) ? $event['name_en'] : $event['name'],
            'currentLang' => $lang,
            'email'       => $this->contacts['email'],
            'phone'       => $this->contacts['phone'],
            'address'     => $this->contacts['address'],
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