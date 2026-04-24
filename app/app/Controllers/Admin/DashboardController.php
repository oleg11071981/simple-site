<?php

/**
 * Контроллер панели управления
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

/**
 * Контроллер дашборда
 */
class DashboardController extends BaseController
{
    /**
     * Отображение главной страницы админ-панели
     *
     * @route GET /admin-panel/dashboard
     *
     * @return string HTML страница дашборда
     */
    public function index(): string
    {
        $data = [
            'title'        => 'Панель управления',
            'activeMenu'   => 'dashboard',
            //'additionalCss' => '/admin/css/dashboard.css',
            'user'         => [
                'id'    => session()->get('user_id'),
                'login' => session()->get('user_login'),
                'name'  => session()->get('user_name'),
                'email' => session()->get('user_email'),
                'type'  => session()->get('user_type'),
            ],
            'logged_in_at' => session()->get('logged_in_at'),
        ];

        return view('admin/dashboard/index', $data);
    }
}