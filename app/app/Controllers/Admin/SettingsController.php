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
        $postData = $this->request->getPost();

        // Сохраняем каждый параметр в базу данных
        foreach ($postData as $key => $value) {
            // Пропускаем CSRF токен (не нужно сохранять)
            if ($key === 'csrf_test_name') {
                continue;
            }

            $this->settingsModel->saveValue($key, $value);
        }

        // Очищаем кэш настроек, чтобы изменения вступили в силу немедленно
        $this->settingsModel->clearCache();

        // Логируем действие для аудита
        log_message('info', '[SETTINGS] Пользователь "{login}" (ID: {id}) обновил настройки сайта', [
            'login' => session()->get('user_login'),
            'id'    => session()->get('user_id')
        ]);

        // Перенаправляем обратно на страницу настроек с сообщением об успехе
        return redirect()->to('/admin-panel/settings')
            ->with('success', 'Настройки сохранены');
    }
}