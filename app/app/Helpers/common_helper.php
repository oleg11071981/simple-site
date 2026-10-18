<?php

if (!function_exists('declension')) {
    /**
     * Склонение существительных после числительных
     * @param int $number Число
     * @param string $one Единственное число (1)
     * @param string $two Два (2,3,4)
     * @param string $many Много (5+)
     * @return string
     */
    function declension(int $number, string $one, string $two, string $many): string
    {
        $mod10 = $number % 10;
        $mod100 = $number % 100;

        if ($mod100 >= 11 && $mod100 <= 19) {
            return $many;
        }

        if ($mod10 == 1) {
            return $one;
        }

        if ($mod10 >= 2 && $mod10 <= 4) {
            return $two;
        }

        return $many;
    }
}

if (!function_exists('has_date')) {
    /**
     * Дата задана и не является нулевым значением MySQL (0000-00-00).
     */
    function has_date(?string $date): bool
    {
        if ($date === null || trim($date) === '') {
            return false;
        }

        $year = (int) substr($date, 0, 4);

        return $year > 0;
    }
}