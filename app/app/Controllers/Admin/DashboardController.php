<?php

/**
 * Контроллер панели управления (Дашборд)
 *
 * Отображает главную страницу административной панели с
 * информацией о текущем пользователе и общими показателями.
 *
 * @package App\Controllers\Admin
 * @category Controllers
 * @license MIT
 * @link    http://localhost
 * @noinspection PhpUnused
 */

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

/**
 * Контроллер дашборда
 *
 * Обеспечивает отображение главной страницы админ-панели
 * после успешной авторизации пользователя.
 *
 * @package App\Controllers\Admin
 */
class DashboardController extends BaseController
{
    /**
     * Отображение главной страницы админ-панели
     *
     * Собирает информацию о текущем авторизованном пользователе
     * из сессии и передаёт её в представление.
     *
     * @route GET /admin-panel/dashboard
     *
     * @return string HTML страница дашборда с информацией о пользователе
     */
    public function index(): string
    {
        // Получаем данные текущего пользователя из сессии
        $userId = session()->get('user_id');
        $userLogin = session()->get('user_login');
        $userName = session()->get('user_name');
        $userEmail = session()->get('user_email');
        $userType = session()->get('user_type');
        $loggedInAt = session()->get('logged_in_at');

        $data = [
            'title'        => 'Панель управления',
            'activeMenu'   => 'dashboard',
            'user'         => [
                'id'    => $userId,
                'login' => $userLogin,
                'name'  => $userName,
                'email' => $userEmail,
                'type'  => $userType,
            ],
            'logged_in_at' => $loggedInAt,
        ];

        return view('admin/dashboard/index', $data);
    }
}