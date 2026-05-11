<?php

if (!function_exists('get_lang')) {
    /**
     * Получить текущий язык
     * @return string 'ru' или 'en'
     */
    function get_lang(): string
    {
        $session = session();
        $lang = $session->get('lang');

        if ($lang && in_array($lang, ['ru', 'en'])) {
            return $lang;
        }

        return 'ru'; // по умолчанию русский
    }
}

if (!function_exists('set_lang')) {
    /**
     * Установить язык
     * @param string $lang 'ru' или 'en'
     */
    function set_lang(string $lang): void
    {
        if (!in_array($lang, ['ru', 'en'])) {
            $lang = 'ru';
        }

        $session = session();
        $session->set('lang', $lang);
    }
}

if (!function_exists('t')) {
    /**
     * Получить локализованное значение поля
     * @param array $item Массив с данными
     * @param string $field Название поля (без суффикса)
     * @param string $default Значение по умолчанию
     * @return string
     */
    function t(array $item, string $field, string $default = ''): string
    {
        $lang = get_lang();

        if ($lang === 'en' && !empty($item[$field . '_en'])) {
            return $item[$field . '_en'];
        }

        return $item[$field] ?? $default;
    }
}

if (!function_exists('t_seo')) {
    /**
     * Получить локализованное SEO значение
     * @param array $item Массив с данными
     * @param string $field Название поля (keywords, description)
     * @param array $settings Настройки сайта
     * @return string
     */
    function t_seo(array $item, string $field, array $settings): string
    {
        $lang = get_lang();
        $value = $item[$field] ?? '';

        if ($lang === 'en') {
            if (!empty($item[$field . '_en'])) {
                $value = $item[$field . '_en'];
            } elseif (!empty($settings[$field . '_en'])) {
                $value = $settings[$field . '_en'];
            }
        } else {
            if (empty($value) && !empty($settings[$field])) {
                $value = $settings[$field];
            }
        }

        return $value;
    }
}