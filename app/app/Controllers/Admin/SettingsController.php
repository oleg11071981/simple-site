<?php

/**
 * Контроллер управления настройками сайта
 *
 * @package App\Controllers\Admin
 * @category Controllers
 * @author  Your Name
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
 */
class SettingsController extends BaseController
{
    /**
     * Конструктор контроллера
     */
    public function __construct()
    {
        // Модель настроек уже доступна через $this->settingsModel из BaseController
    }

    /**
     * Отображение страницы настроек
     *
     * @route GET /admin-panel/settings
     *
     * @return string HTML страница настроек
     */
    public function index(): string
    {
        $settings = $this->settingsModel->getAll();

        $data = [
            'title'        => 'Настройки сайта',
            'activeMenu'   => 'settings',
            'settings'     => $settings,
        ];

        return view('admin/settings/index', $data);
    }

    /**
     * Сохранение настроек
     *
     * @route POST /admin-panel/settings/save
     *
     * @return RedirectResponse
     * @throws ReflectionException
     */
    public function save(): RedirectResponse
    {
        $postData = $this->request->getPost();

        foreach ($postData as $key => $value) {
            if ($key === 'csrf_test_name') {
                continue;
            }

            $this->settingsModel->saveValue($key, $value);
        }

        $this->settingsModel->clearCache();

        log_message('info', '[SETTINGS] Пользователь "{login}" (ID: {id}) обновил настройки сайта', [
            'login' => session()->get('user_login'),
            'id'    => session()->get('user_id')
        ]);

        return redirect()->to('/admin-panel/settings')
            ->with('success', 'Настройки сохранены');
    }
}