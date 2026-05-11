<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

/*
 * --------------------------------------------------------------------
 * Система управления
 * --------------------------------------------------------------------
 */

$routes->group('admin-panel', ['namespace' => 'App\Controllers\Admin'], function ($routes) {
    // Авторизация
    $routes->get('login', 'AuthController::login');
    $routes->post('auth/authenticate', 'AuthController::authenticate');
    $routes->get('logout', 'AuthController::logout');
    // Дашборд
    $routes->get('dashboard', 'DashboardController::index', ['filter' => 'auth']);
    // Настройки
    $routes->get('settings', 'SettingsController::index', ['filter' => 'auth']);
    $routes->post('settings/save', 'SettingsController::save', ['filter' => 'auth']);
    // Управление страницами
    $routes->get('pages', 'PagesController::index', ['filter' => 'auth']);
    $routes->get('pages/create', 'PagesController::create', ['filter' => 'auth']);
    $routes->post('pages/store', 'PagesController::store', ['filter' => 'auth']);
    $routes->get('pages/edit/(:num)', 'PagesController::edit/$1', ['filter' => 'auth']);
    $routes->post('pages/update/(:num)', 'PagesController::update/$1', ['filter' => 'auth']);
    $routes->get('pages/delete/(:num)', 'PagesController::delete/$1', ['filter' => 'auth']);
    $routes->get('pages/toggle/(:num)', 'PagesController::toggle/$1', ['filter' => 'auth']);
    $routes->post('pages/bulk-action', 'PagesController::bulkAction', ['filter' => 'auth']);
    // Файловый менеджер
    $routes->get('files', 'FilesController::index', ['filter' => 'auth']);
    $routes->get('files/upload', 'FilesController::upload', ['filter' => 'auth']);
    $routes->post('files/store', 'FilesController::store', ['filter' => 'auth']);
    $routes->get('files/edit/(:num)', 'FilesController::edit/$1', ['filter' => 'auth']);
    $routes->post('files/update/(:num)', 'FilesController::update/$1', ['filter' => 'auth']);
    $routes->get('files/delete/(:num)', 'FilesController::delete/$1', ['filter' => 'auth']);
    $routes->post('files/bulk-action', 'FilesController::bulkAction', ['filter' => 'auth']);
    $routes->post('files/crop-image/(:num)', 'FilesController::cropImage/$1', ['filter' => 'auth']);
    // Загрузка файлов для CKEditor
    $routes->post('editor/upload', 'EditorUploadController::upload', ['filter' => 'auth']);
    $routes->post('editor/upload-image', 'EditorUploadController::uploadImage', ['filter' => 'auth']);
    $routes->get('editor/ckeditor-browse', 'EditorUploadController::ckeditorBrowse', ['filter' => 'auth']);
    $routes->get('editor/get-files', 'EditorUploadController::getFiles', ['filter' => 'auth']);
    // Категории файлов
    $routes->get('categories', 'CategoriesController::index', ['filter' => 'auth']);
    $routes->get('categories/create', 'CategoriesController::create', ['filter' => 'auth']);
    $routes->post('categories/store', 'CategoriesController::store', ['filter' => 'auth']);
    $routes->get('categories/edit/(:num)', 'CategoriesController::edit/$1', ['filter' => 'auth']);
    $routes->post('categories/update/(:num)', 'CategoriesController::update/$1', ['filter' => 'auth']);
    $routes->get('categories/delete/(:num)', 'CategoriesController::delete/$1', ['filter' => 'auth']);
    // Проекты
    $routes->get('projects', 'ProjectsController::index', ['filter' => 'auth']);
    $routes->get('projects/create', 'ProjectsController::create', ['filter' => 'auth']);
    $routes->post('projects/store', 'ProjectsController::store', ['filter' => 'auth']);
    $routes->get('projects/edit/(:num)', 'ProjectsController::edit/$1', ['filter' => 'auth']);
    $routes->post('projects/update/(:num)', 'ProjectsController::update/$1', ['filter' => 'auth']);
    $routes->get('projects/delete/(:num)', 'ProjectsController::delete/$1', ['filter' => 'auth']);
    $routes->get('projects/toggle/(:num)', 'ProjectsController::toggle/$1', ['filter' => 'auth']);
    $routes->post('projects/bulk-action', 'ProjectsController::bulkAction', ['filter' => 'auth']);
    // Мероприятия
    $routes->get('events', 'EventsController::index', ['filter' => 'auth']);
    $routes->get('events/create', 'EventsController::create', ['filter' => 'auth']);
    $routes->post('events/store', 'EventsController::store', ['filter' => 'auth']);
    $routes->get('events/edit/(:num)', 'EventsController::edit/$1', ['filter' => 'auth']);
    $routes->post('events/update/(:num)', 'EventsController::update/$1', ['filter' => 'auth']);
    $routes->get('events/delete/(:num)', 'EventsController::delete/$1', ['filter' => 'auth']);
    $routes->get('events/toggle/(:num)', 'EventsController::toggle/$1', ['filter' => 'auth']);
    // Новости
    $routes->get('news', 'NewsController::index', ['filter' => 'auth']);
    $routes->get('news/create', 'NewsController::create', ['filter' => 'auth']);
    $routes->post('news/store', 'NewsController::store', ['filter' => 'auth']);
    $routes->get('news/edit/(:num)', 'NewsController::edit/$1', ['filter' => 'auth']);
    $routes->post('news/update/(:num)', 'NewsController::update/$1', ['filter' => 'auth']);
    $routes->get('news/delete/(:num)', 'NewsController::delete/$1', ['filter' => 'auth']);
    $routes->get('news/toggle/(:num)', 'NewsController::toggle/$1', ['filter' => 'auth']);
    $routes->post('news/bulk-action', 'NewsController::bulkAction', ['filter' => 'auth']);
    // Категории новостей
    $routes->get('news-categories', 'NewsCategoriesController::index', ['filter' => 'auth']);
    $routes->get('news-categories/create', 'NewsCategoriesController::create', ['filter' => 'auth']);
    $routes->post('news-categories/store', 'NewsCategoriesController::store', ['filter' => 'auth']);
    $routes->get('news-categories/edit/(:num)', 'NewsCategoriesController::edit/$1', ['filter' => 'auth']);
    $routes->post('news-categories/update/(:num)', 'NewsCategoriesController::update/$1', ['filter' => 'auth']);
    $routes->get('news-categories/delete/(:num)', 'NewsCategoriesController::delete/$1', ['filter' => 'auth']);
});

/*
 * --------------------------------------------------------------------
 * МАРШРУТЫ ДЛЯ ПУБЛИЧНОЙ ЧАСТИ САЙТА
 * --------------------------------------------------------------------
 */

// Переключение языка
$routes->get('lang/(:any)', 'LanguageController::switch/$1');

// Главная страница
$routes->get('/', 'SiteController::index');

// Контакты
$routes->get('contacts', 'SiteController::contacts');

// Новости
$routes->get('news', 'SiteController::news');
$routes->get('news/(:any)', 'SiteController::newsDetail/$1');

// ПРОЕКТЫ
$routes->get('projects', 'ProjectsController::index');
$routes->get('projects/(:any)/(:any)', 'ProjectsController::eventDetail/$1/$2');
$routes->get('projects/(:any)', 'ProjectsController::detail/$1');

// Произвольные страницы (должен быть последним, чтобы не перехватывать другие маршруты)
$routes->get('/(:any)', 'SiteController::page/$1');
$routes->get('/(:any)/(:any)', 'SiteController::page/$1/$2');
$routes->get('/(:any)/(:any)/(:any)', 'SiteController::page/$1/$2/$3');