<?php

/**
 * Модель для работы с таблицей настроек сайта n_siteconfig
 *
 * Предоставляет методы для работы с параметрами конфигурации:
 * - Получение значения по ключу
 * - Установка значения
 * - Получение всех настроек
 *
 * @package App\Models
 * @category Models
 * @author  Your Name
 * @license MIT
 * @link    http://localhost
 * @noinspection PhpUnused
 */

namespace App\Models;

use CodeIgniter\Model;
use ReflectionException;

/**
 * Модель настроек сайта
 */
class NSiteconfigModel extends Model
{
    /**
     * Имя таблицы в базе данных
     *
     * @var string
     */
    protected $table = 'n_siteconfig';

    /**
     * Первичный ключ таблицы
     *
     * @var string
     */
    protected $primaryKey = 'parameter';

    /**
     * Тип первичного ключа (не автоинкремент)
     *
     * @var bool
     */
    protected $useAutoIncrement = false;

    /**
     * Тип возвращаемых данных
     *
     * @var string
     */
    protected $returnType = 'array';

    /**
     * Использовать мягкое удаление
     *
     * @var bool
     */
    protected $useSoftDeletes = false;

    /**
     * Разрешённые для массового заполнения поля
     *
     * @var string[]
     */
    protected $allowedFields = [
        'parameter', 'value'
    ];

    /**
     * Отключаем автоматические timestamps
     *
     * @var bool
     */
    protected $useTimestamps = false;

    /**
     * Кэш настроек (для уменьшения запросов к БД)
     *
     * @var array|null
     */
    private static ?array $settingsCache = null;

    /**
     * Получить значение настройки по ключу
     *
     * @param string $key       Ключ параметра
     * @param mixed  $default   Значение по умолчанию
     *
     * @return mixed Значение настройки или default
     */
    public function get(string $key, $default = null)
    {
        // Загружаем все настройки в кэш, если ещё не загружены
        if (self::$settingsCache === null) {
            $this->loadAllSettings();
        }

        return self::$settingsCache[$key] ?? $default;
    }

    /**
     * Сохранить значение настройки
     *
     * @param string $key   Ключ параметра
     * @param mixed  $value Значение
     *
     * @return bool
     * @throws ReflectionException
     */
    public function saveValue(string $key, $value): bool
    {
        $exists = $this->where('parameter', $key)->first();

        $data = [
            'parameter' => $key,
            'value'     => $value
        ];

        if ($exists) {
            $result = $this->update($key, $data);
        } else {
            $result = $this->insert($data);
        }

        if ($result) {
            // Обновляем кэш
            self::$settingsCache[$key] = $value;
        }

        return $result;
    }

    /**
     * Загрузить все настройки в кэш
     *
     * @return void
     */
    private function loadAllSettings(): void
    {
        $settings = $this->findAll();
        self::$settingsCache = [];

        foreach ($settings as $setting) {
            self::$settingsCache[$setting['parameter']] = $setting['value'];
        }
    }

    /**
     * Получить все настройки
     *
     * @return array
     */
    public function getAll(): array
    {
        if (self::$settingsCache === null) {
            $this->loadAllSettings();
        }

        return self::$settingsCache;
    }

    /**
     * Очистить кэш настроек
     *
     * @return void
     */
    public function clearCache(): void
    {
        self::$settingsCache = null;
    }
}