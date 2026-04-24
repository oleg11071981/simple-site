<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;
use CodeIgniter\Filters\CSRF;
use CodeIgniter\Filters\DebugToolbar;
use CodeIgniter\Filters\Honeypot;
use CodeIgniter\Filters\InvalidChars;
use CodeIgniter\Filters\SecureHeaders;
use App\Filters\AuthFilter; // Добавляем наш фильтр авторизации

class Filters extends BaseConfig
{
    /**
     * Configures aliases for Filter classes to
     * make reading things nicer and simpler.
     *
     * @var array<string, class-string|list<class-string>> [filter_name => classname]
     *                                                     or [filter_name => [classname1, classname2, ...]]
     */
    public array $aliases = [
        'csrf'          => CSRF::class,
        'toolbar'       => DebugToolbar::class,
        'honeypot'      => Honeypot::class,
        'invalidchars'  => InvalidChars::class,
        'secureheaders' => SecureHeaders::class,
        'auth'          => AuthFilter::class, // Регистрируем фильтр авторизации
    ];

    /**
     * List of filter aliases that are always
     * applied before and after every request.
     *
     * @var array<string, array<string, array<string, string>>>|array<string, list<string>>
     */
    public array $globals = [
        'before' => [
            // 'honeypot',
            'csrf' => [
                'except' => [
                    'admin-panel/login',              // страница входа
                    'admin-panel/auth/authenticate',  // авторизация
                    'admin-panel/settings/save',      // сохранение настроек
                    'admin-panel/pages/store',        // создание страницы
                    'admin-panel/pages/update/*',     // обновление страницы
                    'admin-panel/pages/bulk-action',  // массовые действия
                    'admin-panel/files/store',        // загрузка файлов
                    'admin-panel/files/update/*',     // обновление файла
                    'admin-panel/files/bulk-action',  // массовые действия с файлами
                    'admin-panel/categories/store',   // создание категории
                    'admin-panel/categories/update/*', // обновление категории
                    'admin-panel/editor/upload',      // загрузка через редактор
                    'admin-panel/editor/upload-image', // загрузка изображений
                    'admin-panel/files/crop-image/*'
                ]
            ],
            // 'invalidchars',
        ],
        'after' => [
            'toolbar',
            // 'honeypot',
            // 'secureheaders',
        ],
    ];

    /**
     * List of filter aliases that works on a
     * particular HTTP method (GET, POST, etc.).
     *
     * Example:
     * 'post' => ['foo', 'bar']
     *
     * If you use this, you should disable auto-routing because auto-routing
     * permits any HTTP method to access a controller. Accessing the controller
     * with a method you don't expect could bypass the filter.
     *
     * @var array<string, list<string>>
     */
    public array $methods = [];

    /**
     * List of filter aliases that should run on any
     * before or after URI patterns.
     *
     * Example:
     * 'isLoggedIn' => ['before' => ['account/*', 'profiles/*']]
     *
     * @var array<string, array<string, list<string>>>
     */
    public array $filters = [];
}