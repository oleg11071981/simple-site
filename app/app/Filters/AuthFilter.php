<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AuthFilter implements FilterInterface
{
    /**
     * Проверка авторизации перед запросом
     *
     * @param RequestInterface $request
     * @param array|null $arguments
     * @return RedirectResponse|RequestInterface
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        // Проверяем, авторизован ли пользователь
        if (!session()->get('is_logged_in')) {
            return redirect()->to('/admin-panel/login')
                ->with('error', 'Пожалуйста, авторизуйтесь для доступа к админ-панели');
        }

        // Проверяем права доступа (если указаны аргументы)
        if ($arguments && in_array('admin', $arguments)) {
            if (session()->get('user_type') != 1) {
                return redirect()->to('/admin-panel/dashboard')
                    ->with('error', 'У вас нет прав для доступа к этой странице');
            }
        }

        // Если всё в порядке, пропускаем запрос
        return $request;
    }

    /**
     * После запроса (ничего не делаем)
     *
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @param array|null $arguments
     * @return ResponseInterface
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null): ResponseInterface
    {
        // Возвращаем ответ без изменений
        return $response;
    }
}