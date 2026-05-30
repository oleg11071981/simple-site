<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Модель проектов
 *
 * Предоставляет методы для работы с таблицей n_projects:
 * - CRUD операции с проектами
 * - Поддержка многоязычности (RU/EN)
 * - Фильтрация по статусу (active/completed)
 * - Автоматическая генерация URL-путей
 *
 * @package App\Models
 */
class NProjectsModel extends Model
{
    /**
     * Статус проекта: активный
     */
    public const STATUS_ACTIVE = 'active';

    /**
     * Статус проекта: завершённый
     */
    public const STATUS_COMPLETED = 'completed';

    protected $table = 'n_projects';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;

    /**
     * Разрешённые для массового заполнения поля
     * Добавлено поле 'status'
     *
     * @var string[]
     */
    protected $allowedFields = [
        'name',
        'path',
        'anons_text',
        'organizing_committee',
        'supported_by',
        'foto',
        'media',
        'publish',
        'status',           // Статус проекта: active / completed
        'priority',
        'date_start',
        'date_end',
        'keywords',
        'description',
        'create',
        'modify',
        'create_by_user',
        'modify_by_user',
        // Английские версии полей
        'name_en',
        'anons_text_en',
        'organizing_committee_en',
        'supported_by_en',
        'keywords_en',
        'description_en'
    ];

    protected $useTimestamps = false;
    protected $beforeInsert = ['setCreateFields', 'setModifyFields', 'generatePath'];
    protected $beforeUpdate = ['setModifyFields', 'generatePath'];

    // ============================================================
    // СЛУЖЕБНЫЕ МЕТОДЫ (автоматическое заполнение полей)
    // ============================================================

    /**
     * Установка полей создания
     *
     * @param array $data Данные для сохранения
     * @return array
     */
    protected function setCreateFields(array $data): array
    {
        $data['data']['create'] = date('Y-m-d H:i:s');
        $data['data']['create_by_user'] = session()->get('user_id') ?? 0;
        return $data;
    }

    /**
     * Установка полей изменения
     *
     * @param array $data Данные для обновления
     * @return array
     */
    protected function setModifyFields(array $data): array
    {
        $data['data']['modify'] = date('Y-m-d H:i:s');
        $data['data']['modify_by_user'] = session()->get('user_id') ?? 0;
        return $data;
    }

    /**
     * Генерация уникального URL-пути (slug) из названия проекта
     *
     * @param array $data Данные для сохранения
     * @return array
     */
    protected function generatePath(array $data): array
    {
        if (empty($data['data']['path']) && !empty($data['data']['name'])) {
            $slug = mb_strtolower($data['data']['name'], 'UTF-8');
            $slug = str_replace([' ', '_', '.'], '-', $slug);
            $slug = preg_replace('/[^a-zа-я0-9-]/ui', '', $slug);
            $slug = preg_replace('/-+/', '-', $slug);
            $slug = trim($slug, '-');

            // Проверка уникальности
            $count = $this->where('path', $slug)->countAllResults();
            if ($count > 0) {
                $slug .= '-' . ($count + 1);
            }

            $data['data']['path'] = $slug;
        }
        return $data;
    }

    // ============================================================
    // ПУБЛИЧНЫЕ МЕТОДЫ ДЛЯ РАБОТЫ С ПРОЕКТАМИ
    // ============================================================

    /**
     * Получить только опубликованные проекты
     *
     * @param int $limit Лимит записей (0 - без ограничений)
     * @return array
     */
    public function getPublished(int $limit = 0): array
    {
        $builder = $this->where('publish', 1)
            ->orderBy('priority', 'ASC')
            ->orderBy('date_start', 'DESC');

        if ($limit > 0) {
            $builder->limit($limit);
        }

        return $builder->findAll();
    }

    /**
     * Получить активные проекты (status = 'active')
     *
     * @param int $limit Лимит записей (0 - без ограничений)
     * @return array
     */
    public function getActiveProjects(int $limit = 0): array
    {
        $builder = $this->where('publish', 1)
            ->where('status', self::STATUS_ACTIVE)
            ->orderBy('priority', 'ASC')
            ->orderBy('date_start', 'DESC');

        if ($limit > 0) {
            $builder->limit($limit);
        }

        return $builder->findAll();
    }

    /**
     * Получить завершённые проекты (status = 'completed')
     *
     * @param int $limit Лимит записей (0 - без ограничений)
     * @return array
     */
    public function getCompletedProjects(int $limit = 0): array
    {
        $builder = $this->where('publish', 1)
            ->where('status', self::STATUS_COMPLETED)
            ->orderBy('priority', 'ASC')
            ->orderBy('date_start', 'DESC');

        if ($limit > 0) {
            $builder->limit($limit);
        }

        return $builder->findAll();
    }

    /**
     * Получить проект по slug (URL-пути)
     *
     * @param string $path URL-путь проекта
     * @return array|null
     */
    public function getByPath(string $path): ?array
    {
        return $this->where('path', $path)
            ->where('publish', 1)
            ->first();
    }

    /**
     * Получить проекты для главной страницы (последние 3 активных)
     *
     * @param int $limit Лимит записей
     * @return array
     */
    public function getLatestProjects(int $limit = 3): array
    {
        return $this->where('publish', 1)
            ->where('status', self::STATUS_ACTIVE)
            ->orderBy('priority', 'ASC')
            ->orderBy('date_start', 'DESC')
            ->findAll($limit);
    }

    /**
     * Получить полный путь к проекту
     *
     * @param int $id ID проекта
     * @return string
     */
    public function getFullPath(int $id): string
    {
        $project = $this->find($id);
        return $project ? '/' . $project['path'] : '';
    }

    /**
     * Получить дерево проектов для меню
     *
     * @return array
     */
    public function getMenuProjects(): array
    {
        return $this->where('publish', 1)
            ->where('status', self::STATUS_ACTIVE)
            ->orderBy('priority', 'ASC')
            ->orderBy('name', 'ASC')
            ->findAll();
    }

    /**
     * Получить проект по пути с учетом языка
     *
     * @param string $path URL-путь проекта
     * @param string $lang Язык (ru/en)
     * @return array|null
     */
    public function getByPathWithLang(string $path, string $lang = 'ru'): ?array
    {
        $project = $this->where('path', $path)
            ->where('publish', 1)
            ->first();

        if (!$project) {
            return null;
        }

        if ($lang === 'en') {
            $project['name'] = $project['name_en'] ?? $project['name'];
            $project['anons_text'] = $project['anons_text_en'] ?? $project['anons_text'];
            $project['organizing_committee'] = $project['organizing_committee_en'] ?? $project['organizing_committee'];
            $project['supported_by'] = $project['supported_by_en'] ?? $project['supported_by'];
            $project['keywords'] = $project['keywords_en'] ?? $project['keywords'];
            $project['description'] = $project['description_en'] ?? $project['description'];
        }

        return $project;
    }

    /**
     * Получить список проектов с учетом языка
     *
     * @param int $limit Лимит записей (0 - без ограничений)
     * @param string $lang Язык (ru/en)
     * @return array
     */
    public function getPublishedWithLang(int $limit = 0, string $lang = 'ru'): array
    {
        $builder = $this->where('publish', 1)
            ->orderBy('priority', 'ASC')
            ->orderBy('date_start', 'DESC');

        if ($limit > 0) {
            $builder->limit($limit);
        }

        $projects = $builder->findAll();

        if ($lang === 'en') {
            foreach ($projects as &$project) {
                $project['name'] = $project['name_en'] ?? $project['name'];
                $project['anons_text'] = $project['anons_text_en'] ?? $project['anons_text'];
                $project['organizing_committee'] = $project['organizing_committee_en'] ?? $project['organizing_committee'];
                $project['supported_by'] = $project['supported_by_en'] ?? $project['supported_by'];
                $project['keywords'] = $project['keywords_en'] ?? $project['keywords'];
                $project['description'] = $project['description_en'] ?? $project['description'];
            }
        }

        return $projects;
    }

    /**
     * Получить список активных проектов с учетом языка
     *
     * @param int $limit Лимит записей (0 - без ограничений)
     * @param string $lang Язык (ru/en)
     * @return array
     */
    public function getActiveProjectsWithLang(int $limit = 0, string $lang = 'ru'): array
    {
        $builder = $this->where('publish', 1)
            ->where('status', self::STATUS_ACTIVE)
            ->orderBy('priority', 'ASC')
            ->orderBy('date_start', 'DESC');

        if ($limit > 0) {
            $builder->limit($limit);
        }

        $projects = $builder->findAll();

        if ($lang === 'en') {
            foreach ($projects as &$project) {
                $project['name'] = $project['name_en'] ?? $project['name'];
                $project['anons_text'] = $project['anons_text_en'] ?? $project['anons_text'];
                $project['organizing_committee'] = $project['organizing_committee_en'] ?? $project['organizing_committee'];
                $project['supported_by'] = $project['supported_by_en'] ?? $project['supported_by'];
                $project['keywords'] = $project['keywords_en'] ?? $project['keywords'];
                $project['description'] = $project['description_en'] ?? $project['description'];
            }
        }

        return $projects;
    }

    /**
     * Получить список завершённых проектов с учетом языка
     *
     * @param int $limit Лимит записей (0 - без ограничений)
     * @param string $lang Язык (ru/en)
     * @return array
     */
    public function getCompletedProjectsWithLang(int $limit = 0, string $lang = 'ru'): array
    {
        $builder = $this->where('publish', 1)
            ->where('status', self::STATUS_COMPLETED)
            ->orderBy('priority', 'ASC')
            ->orderBy('date_start', 'DESC');

        if ($limit > 0) {
            $builder->limit($limit);
        }

        $projects = $builder->findAll();

        if ($lang === 'en') {
            foreach ($projects as &$project) {
                $project['name'] = $project['name_en'] ?? $project['name'];
                $project['anons_text'] = $project['anons_text_en'] ?? $project['anons_text'];
                $project['organizing_committee'] = $project['organizing_committee_en'] ?? $project['organizing_committee'];
                $project['supported_by'] = $project['supported_by_en'] ?? $project['supported_by'];
                $project['keywords'] = $project['keywords_en'] ?? $project['keywords'];
                $project['description'] = $project['description_en'] ?? $project['description'];
            }
        }

        return $projects;
    }
}