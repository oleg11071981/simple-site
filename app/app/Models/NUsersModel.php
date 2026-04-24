<?php

/**
 * Модель для работы с таблицей пользователей n_users
 *
 * Предоставляет методы для работы с данными пользователей:
 * - CRUD операции
 * - Аутентификация (проверка пароля)
 * - Поиск пользователей по различным критериям
 * - Автоматическое заполнение полей create/modify
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

/**
 * Модель пользователей
 *
 * Обеспечивает взаимодействие с таблицей n_users базы данных.
 * Включает автоматическое хеширование паролей и заполнение
 * служебных полей (create, modify, create_by_user, modify_by_user).
 *
 * @package App\Models
 */
class NUsersModel extends Model
{
    /**
     * Имя таблицы в базе данных
     *
     * @var string
     */
    protected $table = 'n_users';

    /**
     * Первичный ключ таблицы
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * Автоматическое увеличение первичного ключа
     *
     * @var bool
     */
    protected $useAutoIncrement = true;

    /**
     * Тип возвращаемых данных
     * 'array' - массив, 'object' - объект
     *
     * @var string
     */
    protected $returnType = 'array';

    /**
     * Использовать мягкое удаление
     * false - записи удаляются физически
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
        'name', 'login', 'email', 'password',
        'description', 'site', 'media', 'publish', 'type',
        'create', 'modify', 'create_by_user', 'modify_by_user'
    ];

    /**
     * Отключаем автоматические timestamps,
     * так как поля называются иначе (create/modify)
     *
     * @var bool
     */
    protected $useTimestamps = false;

    /**
     * Правила валидации полей
     *
     * @var array
     */
    protected $validationRules = [
        'login'   => 'is_unique[n_users.login,id,{id}]',
        'email'   => 'valid_email|is_unique[n_users.email,id,{id}]',
        'type'    => 'in_list[0,1,2]',
        'publish' => 'in_list[0,1]'
    ];

    /**
     * Сообщения об ошибках валидации
     *
     * @var array
     */
    protected $validationMessages = [
        'login' => [
            'is_unique' => 'Этот логин уже используется'
        ],
        'email' => [
            'valid_email' => 'Введите корректный email',
            'is_unique'   => 'Этот email уже зарегистрирован'
        ]
    ];

    /**
     * События перед вставкой записи
     *
     * @var string[]
     */
    protected $beforeInsert = ['hashPassword', 'setCreateFields'];

    /**
     * События перед обновлением записи
     *
     * @var string[]
     */
    protected $beforeUpdate = ['hashPassword', 'setModifyFields'];

    // ============================================================
    // ЗАЩИЩЁННЫЕ МЕТОДЫ (ВНУТРЕННЯЯ ЛОГИКА)
    // ============================================================

    /**
     * Хеширование пароля перед сохранением
     *
     * Автоматически вызывается перед вставкой и обновлением записи.
     * Преобразует plain-text пароль в хеш через password_hash().
     *
     * @param array $data Данные для сохранения
     *
     * @return array Изменённые данные с хешем пароля
     *
     * @noinspection PhpUnused
     */
    protected function hashPassword(array $data): array
    {
        if (isset($data['data']['password'])) {
            $data['data']['password'] = password_hash(
                $data['data']['password'],
                PASSWORD_DEFAULT
            );
        }
        return $data;
    }

    /**
     * Установка полей создания (create, create_by_user)
     *
     * Автоматически заполняет дату создания и ID пользователя,
     * который создал запись.
     *
     * @param array $data Данные для сохранения
     *
     * @return array Изменённые данные
     *
     * @noinspection PhpUnused
     */
    protected function setCreateFields(array $data): array
    {
        // Устанавливаем текущую дату и время
        $data['data']['create'] = date('Y-m-d H:i:s');

        // Получаем ID текущего пользователя из сессии
        $userId = $this->getCurrentUserId();
        if ($userId) {
            $data['data']['create_by_user'] = $userId;
        }

        return $data;
    }

    /**
     * Установка полей изменения (modify, modify_by_user)
     *
     * Автоматически заполняет дату изменения и ID пользователя,
     * который изменил запись.
     *
     * @param array $data Данные для обновления
     *
     * @return array Изменённые данные
     *
     * @noinspection PhpUnused
     */
    protected function setModifyFields(array $data): array
    {
        // Устанавливаем текущую дату и время
        $data['data']['modify'] = date('Y-m-d H:i:s');

        // Получаем ID текущего пользователя из сессии
        $userId = $this->getCurrentUserId();
        if ($userId) {
            $data['data']['modify_by_user'] = $userId;
        }

        return $data;
    }

    /**
     * Получение ID текущего авторизованного пользователя
     *
     * Извлекает ID пользователя из сессии.
     * Если сессия не активна или пользователь не авторизован,
     * возвращает 0 (системная запись).
     *
     * @return int ID пользователя или 0 если не авторизован
     */
    private function getCurrentUserId(): int
    {
        // Проверяем, загружен ли сервис сессии и есть ли ID пользователя
        if (function_exists('session') && session()->has('user_id')) {
            return (int) session()->get('user_id');
        }

        return 0;
    }

    // ============================================================
    // ПУБЛИЧНЫЕ МЕТОДЫ (ДЛЯ ВНЕШНЕГО ИСПОЛЬЗОВАНИЯ)
    // ============================================================

    /**
     * Проверка пароля
     *
     * Сравнивает введённый plain-text пароль с хешем из базы данных.
     * Использует password_verify() для безопасного сравнения.
     *
     * @param string $plainPassword  Пароль в открытом виде
     * @param string $hashedPassword Хеш пароля из базы данных
     *
     * @return bool true - пароль верный, false - пароль неверный
     */
    public function verifyPassword(string $plainPassword, string $hashedPassword): bool
    {
        return password_verify($plainPassword, $hashedPassword);
    }

    /**
     * Получение пользователя по логину
     *
     * Ищет активного пользователя (publish = 1) с указанным логином.
     *
     * @param string $login Логин пользователя
     *
     * @return array|null Данные пользователя или null если не найден
     *
     * @noinspection PhpUnused
     */
    public function getUserByLogin(string $login): ?array
    {
        return $this->where('login', $login)
            ->where('publish', 1)
            ->first();
    }

    /**
     * Получение пользователя по email
     *
     * Ищет активного пользователя (publish = 1) с указанным email.
     *
     * @param string $email Email пользователя
     *
     * @return array|null Данные пользователя или null если не найден
     *
     * @noinspection PhpUnused
     */
    public function getUserByEmail(string $email): ?array
    {
        return $this->where('email', $email)
            ->where('publish', 1)
            ->first();
    }

    /**
     * Получение списка активных пользователей
     *
     * Возвращает массив активных пользователей,
     * отсортированных по дате создания (сначала новые).
     *
     * @param int $limit Количество записей (по умолчанию 10)
     *
     * @return array Массив данных пользователей
     *
     * @noinspection PhpUnused
     */
    public function getActiveUsers(int $limit = 10): array
    {
        return $this->where('publish', 1)
            ->orderBy('create', 'DESC')
            ->findAll($limit);
    }

    /**
     * Получение пользователей по типу
     *
     * Возвращает всех активных пользователей указанного типа.
     * Типы: 0 - обычный, 1 - администратор, 2 - модератор.
     *
     * @param int $type Тип пользователя (0, 1 или 2)
     *
     * @return array Массив данных пользователей
     *
     * @noinspection PhpUnused
     */
    public function getUsersByType(int $type): array
    {
        return $this->where('type', $type)
            ->where('publish', 1)
            ->findAll();
    }
}