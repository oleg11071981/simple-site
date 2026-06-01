<?php

/**
 * Контроллер публичной части сайта
 *
 * Отвечает за отображение главной страницы, произвольных страниц,
 * новостей, контактов и других публичных разделов сайта.
 *
 * Поддерживает многоязычность (RU/EN) с переключением через сессию.
 *
 * @package App\Controllers
 * @category Controllers
 * @license MIT
 * @link    http://localhost
 * @noinspection PhpUnused
 */

namespace App\Controllers;

use App\Models\NFileManagerModel;
use App\Models\NNewsArticlesModel;
use App\Models\NNewsCategoriesModel;
use App\Models\NProjectEventsModel;
use App\Models\NProjectsModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\RedirectResponse;

/**
 * Контроллер публичной части сайта
 *
 * @package App\Controllers
 */
class SiteController extends BaseController
{
    /**
     * Конструктор контроллера
     *
     * Модели pagesModel и settingsModel наследуются от BaseController.
     * Дополнительная инициализация не требуется.
     */
    public function __construct()
    {
        // Модели уже доступны через parent:
        // $this->pagesModel   - модель страниц
        // $this->settingsModel - модель настроек
        // $this->contacts     - контактные данные для футера
        // $this->currentLang  - текущий язык (ru/en)
    }

    /**
     * Отображение главной страницы
     *
     * @route GET /
     *
     * @return string HTML страница главной
     */
    public function index(): string
    {
        $settings = $this->settingsModel->getAll();
        $lang = $this->currentLang;

        // ============================================================
        // Получаем последние 3 новости категории "Новости комитета"
        // ============================================================
        $newsModel = new NNewsArticlesModel();

        $committeeNews = $newsModel->where('publish', 1)
            ->where('show_all', 1)
            ->where('category_news', 1)
            ->orderBy('date', 'DESC')
            ->orderBy('id', 'DESC')
            ->findAll(3);  // ← Используем параметр лимита в findAll()

        // ============================================================
        // Получаем последние 3 новости категории "Новости в РФ и мире"
        // ============================================================
        $worldNews = $newsModel->where('publish', 1)
            ->where('show_all', 1)
            ->where('category_news', 2)
            ->orderBy('date', 'DESC')
            ->orderBy('id', 'DESC')
            ->findAll(3);  // ← Используем параметр лимита в findAll()

        // Объединяем новости
        $latestNews = array_merge($committeeNews, $worldNews);

        // Локализация новостей
        if ($lang === 'en') {
            foreach ($latestNews as &$item) {
                $item['name'] = $item['name_en'] ?? $item['name'];
                $item['anons_text'] = $item['anons_text_en'] ?? $item['anons_text'];
            }
            unset($item);
        }

        $fileModel = new NFileManagerModel();
        $categoriesModel = new NNewsCategoriesModel();

        // Добавляем фото и названия категорий
        foreach ($latestNews as &$item) {
            if ($item['foto'] > 0) {
                $file = $fileModel->find($item['foto']);
                if ($file) {
                    $item['foto_file'] = $file['file_name'];
                }
            }
            // Добавляем название категории с учетом языка
            if ($item['category_news'] > 0) {
                $category = $categoriesModel->find($item['category_news']);
                if ($category) {
                    $item['category_name'] = ($lang === 'en' && !empty($category['name_en']))
                        ? $category['name_en']
                        : $category['name'];
                } else {
                    $item['category_name'] = '';
                }
            } else {
                $item['category_name'] = '';
            }
        }
        unset($item);

        // ============================================================
        // Получаем проекты с учетом языка (3 последних активных)
        // ============================================================
        $projectsModel = new NProjectsModel();
        $projects = $projectsModel->getActiveProjectsWithLang(3, $lang);

        $eventsModel = new NProjectEventsModel();

        foreach ($projects as &$project) {
            if ($project['foto'] > 0) {
                $file = $fileModel->find($project['foto']);
                if ($file) {
                    $project['foto_file'] = $file['file_name'];
                }
            }
            $project['events_count'] = $eventsModel->getEventsCount($project['id']);
        }
        unset($project);

        // Настройки сайта с учетом языка
        $siteName = ($lang === 'en' && !empty($settings['SiteName_en']))
            ? $settings['SiteName_en']
            : ($settings['SiteName'] ?? 'n-cms');

        $mainText = ($lang === 'en' && !empty($settings['MainText_en']))
            ? $settings['MainText_en']
            : ($settings['MainText'] ?? '');

        $slogan = ($lang === 'en' && !empty($settings['Slogan_en']))
            ? $settings['Slogan_en']
            : ($settings['Slogan'] ?? '');

        $description = ($lang === 'en' && !empty($settings['Description_en']))
            ? $settings['Description_en']
            : ($settings['Description'] ?? '');

        $keywords = ($lang === 'en' && !empty($settings['Keywords_en']))
            ? $settings['Keywords_en']
            : ($settings['Keywords'] ?? '');

        $data = [
            'title'       => $siteName,
            'description' => $description,
            'keywords'    => $keywords,
            'siteName'    => $siteName,
            'slogan'      => $slogan,
            'mainText'    => $mainText,
            'menuPages'   => $this->pagesModel->getMenuPages(),
            'latestNews'  => $latestNews,
            'projects'    => $projects,
            'activePage'  => 'home',
            'currentPage' => '',
            'currentLang' => $lang,
            'email'       => $this->contacts['email'],
            'phone'       => $this->contacts['phone'],
            'address'     => $this->contacts['address'],
        ];

        return view('site/index', $data);
    }

    /**
     * Отображение произвольной страницы с поддержкой языка
     *
     * Обрабатывает как обычные страницы, так и страницы с префиксом 'en/'
     * для английской версии. Выполняет редирект на правильный URL при необходимости.
     *
     * @route GET /{slug}
     * @route GET /en/{slug}
     *
     * @param string ...$segments Сегменты URL
     *
     * @return string|RedirectResponse HTML страница или редирект
     * @throws PageNotFoundException Если страница не найдена
     */
    public function page(...$segments)
    {
        $fullPath = implode('/', $segments);

        // Проверяем, не первый ли сегмент — язык
        $lang = get_lang();
        $firstSegment = $segments[0] ?? '';

        if ($firstSegment === 'en') {
            $lang = 'en';
            array_shift($segments);
            $fullPath = implode('/', $segments);
        }

        $lastSegment = end($segments);
        $page = $this->pagesModel->getByPathWithLang($lastSegment, $lang);

        if (!$page) {
            throw PageNotFoundException::forPageNotFound();
        }

        // Проверяем правильность URL (редирект если путь не соответствует)
        $correctPath = $this->pagesModel->getFullPath($page['id']);
        if ($fullPath !== $correctPath) {
            return redirect()->to('/' . ($lang === 'en' ? 'en/' : '') . $correctPath);
        }

        $settings = $this->settingsModel->getAll();

        // SEO с учетом языка
        $description = t_seo($page, 'description', $settings);
        $keywords = t_seo($page, 'keywords', $settings);

        $data = [
            'title'         => ($lang === 'en' && !empty($page['name_en']))
                ? $page['name_en'] . ' | ' . ($settings['SiteName_en'] ?? $settings['SiteName'])
                : $page['name'] . ' | ' . ($settings['SiteName'] ?? 'n-cms'),
            'description'   => $description,
            'keywords'      => $keywords,
            'page'          => $page,
            'childrenTree'  => $this->pagesModel->getTreeForDisplay($page['id']),
            'breadcrumbs'   => $this->getBreadcrumbs($page['id']),
            'galleryFiles'  => [],
            'menuPages'     => $this->pagesModel->getMenuPages(),
            'activePage'    => 'page_' . $page['id'],
            'currentPage'   => ($lang === 'en' && !empty($page['name_en'])) ? $page['name_en'] : $page['name'],
            'currentLang'   => $lang,
            'email'         => $this->contacts['email'],
            'phone'         => $this->contacts['phone'],
            'address'       => $this->contacts['address'],
        ];

        return view('site/page', $data);
    }

    /**
     * Получение хлебных крошек для навигации с учетом языка
     *
     * Формирует цепочку родительских страниц, исключая текущую.
     * Названия страниц выводятся на текущем языке.
     *
     * @param int $id ID текущей страницы
     *
     * @return array Массив хлебных крошек с полями 'name' и 'url'
     */
    private function getBreadcrumbs(int $id): array
    {
        $breadcrumbs = [];
        $parents = [];
        $current = $this->pagesModel->find($id);
        $lang = $this->currentLang;

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
            // Выбираем название с учетом языка
            $name = ($lang === 'en' && !empty($parent['name_en']))
                ? $parent['name_en']
                : $parent['name'];

            $breadcrumbs[] = [
                'name' => $name,
                'url'  => '/' . $this->pagesModel->getFullPath($parent['id'])
            ];
        }

        return $breadcrumbs;
    }

    /**
     * Список новостей с фильтрацией по категории и дате
     *
     * Поддерживает фильтрацию по категории, диапазону дат,
     * пагинацию и локализацию контента.
     *
     * @route GET /news
     *
     * @return string HTML страница со списком новостей
     */
    public function news(): string
    {
        $perPage = 9;
        $page = (int)($this->request->getGet('page') ?? 1);
        $category = (int)($this->request->getGet('category') ?? 0);
        $date_from = $this->request->getGet('date_from') ?? '';
        $date_to = $this->request->getGet('date_to') ?? '';

        $settings = $this->settingsModel->getAll();
        $lang = $this->currentLang;

        $newsModel = new NNewsArticlesModel();
        $fileModel = new NFileManagerModel();
        $categoriesModel = new NNewsCategoriesModel();

        $builder = $newsModel->where('publish', 1);

        // Фильтр по категории
        if ($category > 0) {
            $builder = $builder->where('category_news', $category);
        }

        // Фильтр по дате (диапазон)
        if (!empty($date_from)) {
            $builder = $builder->where('date >=', $date_from);
        }
        if (!empty($date_to)) {
            $builder = $builder->where('date <=', $date_to);
        }

        $news = $builder->orderBy('date', 'DESC')
            ->orderBy('id', 'DESC')
            ->paginate($perPage, 'default', $page);

        // Локализация новостей
        if ($lang === 'en') {
            foreach ($news as &$item) {
                $item['name'] = $item['name_en'] ?? $item['name'];
                $item['anons_text'] = $item['anons_text_en'] ?? $item['anons_text'];
            }
            unset($item);
        }

        $allCategories = $categoriesModel->orderBy('priority', 'ASC')->findAll();

        // Добавляем изображения и названия категорий
        foreach ($news as &$item) {
            if ($item['foto'] > 0) {
                $file = $fileModel->find($item['foto']);
                if ($file) {
                    $item['foto_file'] = $file['file_name'];
                }
            }
            if ($item['category_news'] > 0) {
                $cat = $categoriesModel->find($item['category_news']);
                $item['category_name'] = $cat ? $cat['name'] : '';
            } else {
                $item['category_name'] = '';
            }
        }
        unset($item);

        $pager = $newsModel->pager;

        $data = [
            'title'          => ($lang === 'en' && !empty($settings['SiteName_en']))
                ? 'News | ' . $settings['SiteName_en']
                : 'Новости | ' . ($settings['SiteName'] ?? 'n-cms'),
            'description'    => ($lang === 'en' && !empty($settings['Description_en']))
                ? $settings['Description_en']
                : 'Новости и события компании',
            'keywords'       => ($lang === 'en' && !empty($settings['Keywords_en']))
                ? $settings['Keywords_en']
                : 'новости, события',
            'news'           => $news,
            'pager'          => $pager,
            'currentPage'    => ($lang === 'en') ? 'News' : 'Новости',
            'activeCategory' => $category,
            'allCategories'  => $allCategories,
            'date_from'      => $date_from,
            'date_to'        => $date_to,
            'menuPages'      => $this->pagesModel->getMenuPages(),
            'activePage'     => 'news',
            'currentLang'    => $lang,
            'email'          => $this->contacts['email'],
            'phone'          => $this->contacts['phone'],
            'address'        => $this->contacts['address'],
        ];

        return view('site/news', $data);
    }

    /**
     * Детальная страница новости
     *
     * @route GET /news/{slug}
     *
     * @param string $slug URL-путь новости
     *
     * @return string HTML страница новости
     * @throws PageNotFoundException Если новость не найдена
     */
    public function newsDetail(string $slug): string
    {
        $settings = $this->settingsModel->getAll();
        $lang = $this->currentLang;

        $newsModel = new NNewsArticlesModel();
        $fileModel = new NFileManagerModel();
        $categoriesModel = new NNewsCategoriesModel();

        $news = $newsModel->getByPathWithLang($slug, $lang);

        if (!$news) {
            throw PageNotFoundException::forPageNotFound();
        }

        // Получаем главное изображение
        if ($news['foto'] > 0) {
            $file = $fileModel->find($news['foto']);
            if ($file) {
                $news['foto_file'] = $file['file_name'];
            }
        }

        // Добавляем название категории
        if ($news['category_news'] > 0) {
            $category = $categoriesModel->find($news['category_news']);
            $news['category_name'] = $category ? $category['name'] : '';
        } else {
            $news['category_name'] = '';
        }

        // Получаем галерею новости
        $galleryFiles = [];
        if ($news['media'] > 0) {
            $files = $fileModel->getFilesByCategory($news['media']);
            foreach ($files as &$file) {
                $file['size_formatted'] = $this->formatFileSize($file['file_size']);
            }
            unset($file);
            $galleryFiles = $files;
        }

        // Получаем другие новости (3 штуки)
        $otherNews = $newsModel->where('publish', 1)
            ->where('id !=', $news['id'])
            ->orderBy('date', 'DESC')
            ->findAll(4);

        foreach ($otherNews as &$item) {
            if ($item['foto'] > 0) {
                $file = $fileModel->find($item['foto']);
                if ($file) {
                    $item['foto_file'] = $file['file_name'];
                }
            }
        }
        unset($item);

        $data = [
            'title'       => ($lang === 'en' && !empty($news['name_en']))
                ? $news['name_en'] . ' | ' . ($settings['SiteName_en'] ?? $settings['SiteName'])
                : $news['name'] . ' | ' . ($settings['SiteName'] ?? 'n-cms'),
            'description' => $news['description'] ?: $news['anons_text'],
            'keywords'    => $news['keywords'],
            'news'        => $news,
            'galleryFiles'=> $galleryFiles,
            'otherNews'   => $otherNews,
            'menuPages'   => $this->pagesModel->getMenuPages(),
            'activePage'  => 'news',
            'breadcrumbs' => [
                ['name' => ($lang === 'en') ? 'News' : 'Новости', 'url' => '/news']
            ],
            'currentPage' => ($lang === 'en' && !empty($news['name_en'])) ? $news['name_en'] : $news['name'],
            'currentLang' => $lang,
            'email'       => $this->contacts['email'],
            'phone'       => $this->contacts['phone'],
            'address'     => $this->contacts['address'],
        ];

        return view('site/news_detail', $data);
    }

    /**
     * Страница контактов
     *
     * Отображает контактную информацию из настроек сайта.
     *
     * @route GET /contacts
     *
     * @return string HTML страница контактов
     */
    public function contacts(): string
    {
        $settings = $this->settingsModel->getAll();
        $lang = $this->currentLang;

        // Получаем название сайта с учетом языка
        $siteName = ($lang === 'en' && !empty($settings['SiteName_en']))
            ? $settings['SiteName_en']
            : ($settings['SiteName'] ?? 'n-cms');

        $description = ($lang === 'en' && !empty($settings['Description_en']))
            ? $settings['Description_en']
            : 'Контактная информация, адрес, телефон, email, схема проезда';

        $keywords = ($lang === 'en' && !empty($settings['Keywords_en']))
            ? $settings['Keywords_en']
            : 'контакты, адрес, телефон, email, схема проезда';

        $pageTitle = ($lang === 'en') ? 'Contacts' : 'Контакты';

        $data = [
            'title'       => $pageTitle . ' | ' . $siteName,
            'description' => $description,
            'keywords'    => $keywords,
            'siteName'    => $siteName,
            'email'       => $this->contacts['email'],
            'adminEmail'  => $settings['AdminEmail'] ?? '',
            'phone'       => $this->contacts['phone'],
            'address'     => $this->contacts['address'],
            'workSchedule'=> $settings['WorkSchedule'] ?? '',
            'additional_field1' => $settings['additional_field1'] ?? '',
            'additional_field2' => $settings['additional_field2'] ?? '',
            'menuPages'   => $this->pagesModel->getMenuPages(),
            'activePage'  => 'contacts',
            'currentPage' => $pageTitle,
            'currentLang' => $lang,
        ];

        return view('site/contacts', $data);
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