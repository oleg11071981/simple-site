<?php

/**
 * Контроллер управления настройками сайта
 *
 * Предоставляет методы для управления конфигурацией сайта:
 * - Отображение формы настроек
 * - Сохранение значений параметров в базу данных
 * - Очистка кэша настроек после сохранения
 * - Логирование изменений настроек
 *
 * @package App\Controllers\Admin
 * @category Controllers
 * @license MIT
 * @link    http://localhost
 * @noinspection PhpUnused
 */

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\NSiteconfigModel;
use CodeIgniter\HTTP\RedirectResponse;
use ReflectionException;

/**
 * Контроллер настроек сайта
 *
 * Обеспечивает управление параметрами конфигурации сайта
 * через административный интерфейс.
 *
 * @package App\Controllers\Admin
 */
class SettingsController extends BaseController
{
    /**
     * Модель настроек сайта
     *
     * @var NSiteconfigModel
     */
    protected NSiteconfigModel $settingsModel;

    /**
     * Конструктор контроллера
     */
    public function __construct()
    {
        $this->settingsModel = new NSiteconfigModel();
    }

    /**
     * Отображение страницы управления настройками
     *
     * Загружает все текущие настройки сайта из базы данных
     * и передаёт их в представление для редактирования.
     *
     * @route GET /admin-panel/settings
     *
     * @return string HTML страница с формой настроек
     */
    public function index(): string
    {
        // Получаем все настройки из базы данных
        $settings = $this->settingsModel->getAll();

        $data = [
            'title'      => 'Настройки сайта',
            'activeMenu' => 'settings',
            'settings'   => $settings,
        ];

        return view('admin/settings/index', $data);
    }

    /**
     * Сохранение настроек сайта
     *
     * Обрабатывает POST-запрос с формы настроек,
     * сохраняет каждый параметр в базу данных,
     * очищает кэш настроек и логирует действие.
     *
     * @route POST /admin-panel/settings/save
     *
     * @return RedirectResponse Редирект на страницу настроек с сообщением об успехе
     * @throws ReflectionException
     */
    public function save(): RedirectResponse
    {
        // Отладка: посмотрим, что приходит в POST
        log_message('error', 'POST data: ' . print_r($this->request->getPost(), true));

        $postData = $this->request->getPost();

        foreach ($postData as $key => $value) {
            if ($key === 'csrf_test_name') {
                continue;
            }

            log_message('error', 'Saving: ' . $key . ' = ' . $value);

            $this->settingsModel->saveValue($key, $value);
        }

        $this->settingsModel->clearCache();

        return redirect()->to('/admin-panel/settings')
            ->with('success', 'Настройки сохранены');
    }
}