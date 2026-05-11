<?php

namespace App\Controllers;

use CodeIgniter\HTTP\RedirectResponse;

class LanguageController extends BaseController
{
    /**
     * Переключение языка
     * @param string $lang
     * @return RedirectResponse
     */
    public function switch(string $lang): RedirectResponse
    {
        $allowed = ['ru', 'en'];

        if (!in_array($lang, $allowed)) {
            $lang = 'ru';
        }

        set_lang($lang);

        // Возвращаемся на предыдущую страницу или на главную
        $referer = $this->request->getServer('HTTP_REFERER');
        if ($referer) {
            return redirect()->to($referer);
        }

        return redirect()->to('/');
    }
}