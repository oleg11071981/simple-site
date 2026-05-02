<?php

/**
 * Контроллер публичной части сайта
 *
 * Отвечает за отображение главной страницы и произвольных страниц
 * на основе данных из базы данных.
 *
 * @package App\Controllers
 * @category Controllers
 * @author  Your Name
 * @license MIT
 * @link    http://localhost
 * @noinspection PhpUnused
 */

namespace App\Controllers;

use App\Models\NFileManagerModel;
use App\Models\NNewsArticlesModel;
use App\Models\NSiteModel;
use App\Models\NSiteconfigModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\RedirectResponse;

/**
 * Контроллер публичной части сайта
 */
class SiteController extends BaseController
{
    /**
     * Модель для работы со страницами
     *
     * @var NSiteModel
     */
    protected NSiteModel $pagesModel;

    /**
     * Модель для работы с настройками сайта
     *
     * @var NSiteconfigModel
     */
    protected NSiteconfigModel $settingsModel;

    /**
     * Конструктор контроллера
     */
    public function __construct()
    {
        $this->pagesModel = new NSiteModel();
        $this->settingsModel = new NSiteconfigModel();
    }

    /**
     * Отображение главной страницы
     *
     * @route GET /
     * @return string HTML страница
     */
    public function index(): string
    {
        $settings = $this->settingsModel->getAll();

        // Получаем последние новости для главной страницы
        $newsModel = new NNewsArticlesModel();
        $latestNews = $newsModel->getLatestNews(3);

        // Добавляем информацию о фото для каждой новости
        $fileModel = new NFileManagerModel();
        foreach ($latestNews as &$item) {
            if ($item['foto'] > 0) {
                $file = $fileModel->find($item['foto']);
                if ($file) {
                    $item['foto_file'] = $file['file_name'];
                }
            }
        }

        $data = [
            'title'       => $settings['SiteName'] ?? 'Демо',
            'description' => $settings['Description'] ?? '',
            'keywords'    => $settings['Keywords'] ?? '',
            'siteName'    => $settings['SiteName'] ?? 'Демо',
            'slogan'      => $settings['Slogan'] ?? '',
            'mainText'    => $settings['MainText'] ?? '',
            'menuPages'   => $this->pagesModel->getMenuPages(),
            'latestNews'  => $latestNews,
            'activePage'  => 'home',
            'currentPage' => ''
        ];

        return view('site/index', $data);
    }

    /**
     * Отображение произвольной страницы с поддержкой вложенных URL
     *
     * @route GET /{slug}
     * @param string ...$segments Сегменты URL
     * @return string|RedirectResponse HTML страница или редирект
     * @throws PageNotFoundException Если страница не найдена
     */
    public function page(...$segments)
    {
        $fullPath = implode('/', $segments);

        // Ищем страницу по последнему сегменту
        $lastSegment = end($segments);
        $page = $this->pagesModel->where('path', $lastSegment)
            ->where('publish', 1)
            ->first();

        if (!$page) {
            throw PageNotFoundException::forPageNotFound();
        }

        // Проверяем, правильный ли URL
        $correctPath = $this->pagesModel->getFullPath($page['id']);
        if ($fullPath !== $correctPath) {
            return redirect()->to('/' . $correctPath);
        }

        $settings = $this->settingsModel->getAll();

        // Получаем дерево вложенных страниц
        $childrenTree = $this->pagesModel->getTreeForDisplay($page['id']);

        // Получаем хлебные крошки
        $breadcrumbs = $this->getBreadcrumbs($page['id']);

        // Получаем файлы галереи, если привязана
        $galleryFiles = [];
        if ($page['media'] > 0) {
            $fileModel = new NFileManagerModel();
            $files = $fileModel->getFilesByCategory($page['media']);

            // Форматируем размер для каждого файла
            foreach ($files as &$file) {
                $file['size_formatted'] = $this->formatFileSize($file['file_size']);
            }
            $galleryFiles = $files;
        }

        $data = [
            'title'         => $page['name'] . ' | ' . ($settings['SiteName'] ?? 'n-cms'),
            'description'   => $page['description'] ?: ($settings['Description'] ?? ''),
            'keywords'      => $page['keywords'] ?: ($settings['Keywords'] ?? ''),
            'page'          => $page,
            'childrenTree'  => $childrenTree,
            'breadcrumbs'   => $breadcrumbs,
            'galleryFiles'  => $galleryFiles,
            'menuPages'     => $this->pagesModel->getMenuPages(),
            'activePage'    => 'page_' . $page['id'],
            'currentPage'   => $page['name'],
        ];

        return view('site/page', $data);
    }

    /**
     * Получение хлебных крошек для навигации
     *
     * @param int $id ID текущей страницы
     * @return array
     */
    private function getBreadcrumbs(int $id): array
    {
        $breadcrumbs = [];
        $parents = [];
        $current = $this->pagesModel->find($id);

        // Собираем всех родителей (исключая текущую страницу)
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
                'name' => $parent['name'],
                'url'  => '/' . $this->pagesModel->getFullPath($parent['id'])
            ];
        }

        return $breadcrumbs;
    }

    /**
     * Список новостей
     *
     * @route GET /news
     * @return string
     */
    public function news(): string
    {
        $perPage = 9;
        $page = $this->request->getGet('page') ?? 1;

        $newsModel = new NNewsArticlesModel();
        $settings = $this->settingsModel->getAll();
        $fileModel = new NFileManagerModel();

        $news = $newsModel->where('publish', 1)
            ->orderBy('date', 'DESC')
            ->orderBy('id', 'DESC')
            ->paginate($perPage, 'default', $page);

        foreach ($news as &$item) {
            if ($item['foto'] > 0) {
                $file = $fileModel->find($item['foto']);
                if ($file) {
                    $item['foto_file'] = $file['file_name'];
                }
            }
        }

        $pager = $newsModel->pager;

        $data = [
            'title'       => 'Новости | ' . ($settings['SiteName'] ?? 'n-cms'),
            'description' => 'Новости и события компании. Актуальные новости, проекты и достижения.',
            'keywords'    => 'новости, события, проекты, достижения',
            'news'        => $news,
            'pager'       => $pager,
            'currentPage' => 'Новости',
            'menuPages'   => $this->pagesModel->getMenuPages(),
            'activePage'  => 'news',
        ];

        return view('site/news', $data);
    }

    /**
     * Детальная страница новости
     *
     * @route GET /news/{slug}
     * @param string $slug
     * @return string
     */
    public function newsDetail(string $slug): string
    {
        $newsModel = new NNewsArticlesModel();
        $settings = $this->settingsModel->getAll();
        $fileModel = new NFileManagerModel();

        $news = $newsModel->where('path', $slug)
            ->where('publish', 1)
            ->first();

        if (!$news) {
            throw PageNotFoundException::forPageNotFound();
        }

        // Добавляем информацию о фото
        if ($news['foto'] > 0) {
            $file = $fileModel->find($news['foto']);
            if ($file) {
                $news['foto_file'] = $file['file_name'];
            }
        }

        // Получаем файлы галереи, если привязана
        $galleryFiles = [];
        if ($news['media'] > 0) {
            $files = $fileModel->getFilesByCategory($news['media']);

            // Форматируем размер для каждого файла
            foreach ($files as &$file) {
                $file['size_formatted'] = $this->formatFileSize($file['file_size']);
            }
            $galleryFiles = $files;
        }

        // Получаем другие новости
        $otherNews = $newsModel->where('publish', 1)
            ->where('id !=', $news['id'])
            ->orderBy('date', 'DESC')
            ->limit(3)
            ->findAll();

        foreach ($otherNews as &$item) {
            if ($item['foto'] > 0) {
                $file = $fileModel->find($item['foto']);
                if ($file) {
                    $item['foto_file'] = $file['file_name'];
                }
            }
        }

        $data = [
            'title'       => $news['name'] . ' | ' . ($settings['SiteName'] ?? 'n-cms'),
            'description' => $news['description'] ?: $news['anons_text'],
            'keywords'    => $news['keywords'],
            'news'        => $news,
            'galleryFiles'=> $galleryFiles,
            'otherNews'   => $otherNews,
            'menuPages'   => $this->pagesModel->getMenuPages(),
            'activePage'  => 'news',
            'breadcrumbs' => [
                ['name' => 'Новости', 'url' => '/news']
            ],
            'currentPage' => $news['name'],
        ];

        return view('site/news_detail', $data);
    }

    /**
     * Страница контактов
     *
     * @route GET /contacts
     * @return string
     */
    public function contacts(): string
    {
        $settings = $this->settingsModel->getAll();

        $data = [
            'title'       => 'Контакты | ' . ($settings['SiteName'] ?? 'n-cms'),
            'description' => 'Контактная информация, адрес, телефон, email, схема проезда',
            'keywords'    => 'контакты, адрес, телефон, email, схема проезда',
            'siteName'    => $settings['SiteName'] ?? 'n-cms',
            'email'       => $settings['Email'] ?? '',
            'adminEmail'  => $settings['AdminEmail'] ?? '',
            'phone'       => $settings['Phone'] ?? '',
            'address'     => $settings['Adress'] ?? '',
            'workSchedule' => $settings['WorkSchedule'] ?? '',
            'additional_field1' => $settings['additional_field1'] ?? '',
            'additional_field2' => $settings['additional_field2'] ?? '',
            'menuPages'   => $this->pagesModel->getMenuPages(),
            'activePage'  => 'contacts',
            'currentPage' => 'Контакты',
        ];

        return view('site/contacts', $data);
    }

    /**
     * Форматировать размер файла
     *
     * @param int $bytes
     * @return string
     */
    private function formatFileSize(int $bytes): string
    {
        if ($bytes < 1024) return $bytes . ' Б';
        if ($bytes < 1048576) return round($bytes / 1024, 1) . ' КБ';
        if ($bytes < 1073741824) return round($bytes / 1048576, 1) . ' МБ';
        return round($bytes / 1073741824, 1) . ' ГБ';
    }

}