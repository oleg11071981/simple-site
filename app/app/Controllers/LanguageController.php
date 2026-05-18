<?php

namespace App\Controllers;

use CodeIgniter\HTTP\RedirectResponse;

/**
 * Контроллер для переключения языка сайта
 *
 * Обрабатывает запросы на смену языка (RU/EN),
 * сохраняет выбранный язык в сессии и перенаправляет
 * пользователя обратно на предыдущую страницу.
 *
 * @package App\Controllers
 * @category Controllers
 * @license MIT
 * @link    http://localhost
 * @noinspection PhpUnused
 */
class LanguageController extends BaseController
{
    /**
     * Переключение языка сайта
     *
     * Проверяет допустимость запрошенного языка (ru или en),
     * сохраняет выбор в сессии через хилер set_lang(),
     * затем перенаправляет пользователя обратно на предыдущую страницу
     * или на главную, если реферер отсутствует.
     *
     * @route GET /lang/{lang}
     *
     * @param string $lang Код языка ('ru' или 'en')
     *
     * @return RedirectResponse Редирект на предыдущую страницу или главную
     */
    public function switch(string $lang): RedirectResponse
    {
        // Допустимые коды языков
        $allowed = ['ru', 'en'];

        // Проверяем, что запрошенный язык допустим
        if (!in_array($lang, $allowed)) {
            $lang = 'ru';
        }

        // Сохраняем выбранный язык в сессии
        set_lang($lang);

        // Возвращаемся на предыдущую страницу
        $referer = $this->request->getServer('HTTP_REFERER');
        if ($referer) {
            return redirect()->to($referer);
        }

        // Если реферер отсутствует, перенаправляем на главную
        return redirect()->to('/');
    }
}