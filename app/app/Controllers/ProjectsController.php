<?php

namespace App\Controllers;

use App\Models\NProjectsModel;
use App\Models\NProjectEventsModel;
use App\Models\NFileManagerModel;
use CodeIgniter\Exceptions\PageNotFoundException;

/**
 * Контроллер публичной части проектов и мероприятий
 *
 * Обеспечивает отображение:
 * - Списка проектов (разделён на активные и завершённые)
 * - Детальной страницы проекта с мероприятиями
 * - Детальной страницы мероприятия
 * - Поддержку многоязычности (RU/EN)
 *
 * @package App\Controllers
 * @category Controllers
 * @license MIT
 * @link    http://localhost
 * @noinspection PhpUnused
 */
class ProjectsController extends BaseController
{
    /**
     * Модель проектов
     *
     * @var NProjectsModel
     */
    protected NProjectsModel $projectsModel;

    /**
     * Модель мероприятий
     *
     * @var NProjectEventsModel
     */
    protected NProjectEventsModel $eventsModel;

    /**
     * Конструктор контроллера
     *
     * Инициализирует модели для работы с проектами и мероприятиями.
     */
    public function __construct()
    {
        $this->projectsModel = new NProjectsModel();
        $this->eventsModel = new NProjectEventsModel();
    }

    /**
     * Отображение списка всех проектов
     *
     * Разделяет проекты на две группы:
     * - Активные проекты (status = 'active')
     * - Завершённые проекты (status = 'completed')
     *
     * GET /projects
     *
     * @return string HTML страница со списком проектов
     */
    public function index(): string
    {
        $settings = $this->settingsModel->getAll();
        $lang = $this->currentLang;

        // Получаем активные проекты с учетом языка
        $activeProjects = $this->projectsModel->getActiveProjectsWithLang(0, $lang);

        // Получаем завершённые проекты с учетом языка
        $completedProjects = $this->projectsModel->getCompletedProjectsWithLang(0, $lang);

        $fileModel = new NFileManagerModel();

        // Добавляем информацию о главном изображении для активных проектов
        foreach ($activeProjects as &$project) {
            if ($project['foto'] > 0) {
                $file = $fileModel->find($project['foto']);
                if ($file) {
                    $project['foto_file'] = $file['file_name'];
                }
            }
            $project['events_count'] = $this->eventsModel->getEventsCount($project['id']);
        }
        unset($project);

        // Добавляем информацию о главном изображении для завершённых проектов
        foreach ($completedProjects as &$project) {
            if ($project['foto'] > 0) {
                $file = $fileModel->find($project['foto']);
                if ($file) {
                    $project['foto_file'] = $file['file_name'];
                }
            }
            $project['events_count'] = $this->eventsModel->getEventsCount($project['id']);
        }
        unset($project);

        $data = [
            'title'            => ($lang === 'en' && !empty($settings['SiteName_en']))
                ? 'Projects | ' . $settings['SiteName_en']
                : 'Проекты | ' . ($settings['SiteName'] ?? 'n-cms'),
            'description'      => ($lang === 'en' && !empty($settings['Description_en']))
                ? $settings['Description_en']
                : 'Проекты и мероприятия организации',
            'keywords'         => ($lang === 'en' && !empty($settings['Keywords_en']))
                ? $settings['Keywords_en']
                : 'проекты, мероприятия, события',
            'activeProjects'   => $activeProjects,
            'completedProjects'=> $completedProjects,
            'menuPages'        => $this->pagesModel->getMenuPages(),
            'activePage'       => 'projects',
            'currentPage'      => ($lang === 'en') ? 'Projects' : 'Проекты',
            'currentLang'      => $lang,
            'email'            => $this->contacts['email'],
            'phone'            => $this->contacts['phone'],
            'address'          => $this->contacts['address'],
        ];

        return view('site/projects/index', $data);
    }

    /**
     * Детальная страница проекта
     *
     * GET /projects/{slug}
     *
     * @param string $slug URL-путь проекта
     *
     * @return string HTML страница проекта
     * @throws PageNotFoundException Если проект не найден
     */
    public function detail(string $slug): string
    {
        $settings = $this->settingsModel->getAll();
        $lang = $this->currentLang;

        // Получаем проект с учетом языка
        $project = $this->projectsModel->getByPathWithLang($slug, $lang);

        if (!$project) {
            throw PageNotFoundException::forPageNotFound();
        }

        $fileModel = new NFileManagerModel();

        // Получаем главное изображение
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
        $events = $this->eventsModel->getByProjectIdWithLang($project['id'], $lang);

        // Добавляем изображения к мероприятиям
        foreach ($events as &$event) {
            if ($event['foto'] > 0) {
                $file = $fileModel->find($event['foto']);
                if ($file) {
                    $event['foto_file'] = $file['file_name'];
                }
            }
        }
        unset($event);

        $data = [
            'title'       => ($lang === 'en' && !empty($project['name_en']))
                ? $project['name_en'] . ' | ' . ($settings['SiteName_en'] ?? $settings['SiteName'])
                : $project['name'] . ' | ' . ($settings['SiteName'] ?? 'n-cms'),
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
     *
     * GET /projects/{project_slug}/{event_slug}
     *
     * @param string $projectSlug URL-путь проекта
     * @param string $eventSlug   URL-путь мероприятия
     *
     * @return string HTML страница мероприятия
     * @throws PageNotFoundException Если мероприятие не найдено
     * @noinspection PhpUnused
     */
    public function eventDetail(string $projectSlug, string $eventSlug): string
    {
        $settings = $this->settingsModel->getAll();
        $lang = $this->currentLang;

        // Получаем мероприятие с учетом языка
        $event = $this->eventsModel->getByProjectPathAndEventPathWithLang($projectSlug, $eventSlug, $lang);

        if (!$event) {
            throw PageNotFoundException::forPageNotFound();
        }

        // Получаем проект
        $project = $this->projectsModel->getByPathWithLang($projectSlug, $lang);

        $fileModel = new NFileManagerModel();

        // Получаем главное изображение мероприятия
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
            ->findAll(3);

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
        unset($other);

        $data = [
            'title'       => ($lang === 'en' && !empty($event['name_en']))
                ? $event['name_en'] . ' | ' . ($settings['SiteName_en'] ?? $settings['SiteName'])
                : $event['name'] . ' | ' . ($settings['SiteName'] ?? 'n-cms'),
            'description' => $event['more_info']
                ? strip_tags(substr($event['more_info'], 0, 200))
                : ($event['anons_text'] ?? ''),
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
     * Форматирование размера файла в человекочитаемый вид
     *
     * @param int $bytes Размер в байтах
     *
     * @return string Отформатированный размер
     */
    private function formatFileSize(int $bytes): string
    {
        if ($bytes < 1024) {
            return $bytes . ' Б';
        }
        if ($bytes < 1048576) {
            return round($bytes / 1024, 1) . ' КБ';
        }
        if ($bytes < 1073741824) {
            return round($bytes / 1048576, 1) . ' МБ';
        }
        return round($bytes / 1073741824, 1) . ' ГБ';
    }
}