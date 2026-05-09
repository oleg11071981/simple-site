<?php

namespace App\Models;

use CodeIgniter\Model;

class NProjectsModel extends Model
{
    protected $table = 'n_projects';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'name',
        'path',
        'anons_text',
        'organizing_committee',
        'supported_by',
        'foto',
        'media',
        'publish',
        'priority',
        'date_start',
        'date_end',
        'keywords',
        'description',
        'create',
        'modify',
        'create_by_user',
        'modify_by_user'
    ];

    protected $useTimestamps = false;
    protected $beforeInsert = ['setCreateFields', 'setModifyFields', 'generatePath'];
    protected $beforeUpdate = ['setModifyFields', 'generatePath'];

    /**
     * Установка полей создания
     */
    protected function setCreateFields(array $data): array
    {
        $data['data']['create'] = date('Y-m-d H:i:s');
        $data['data']['create_by_user'] = session()->get('user_id') ?? 0;
        return $data;
    }

    /**
     * Установка полей изменения
     */
    protected function setModifyFields(array $data): array
    {
        $data['data']['modify'] = date('Y-m-d H:i:s');
        $data['data']['modify_by_user'] = session()->get('user_id') ?? 0;
        return $data;
    }

    /**
     * Генерация пути (slug) из названия
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

    /**
     * Получить только опубликованные проекты
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
     * Получить проект по slug
     */
    public function getByPath(string $path): ?array
    {
        return $this->where('path', $path)
            ->where('publish', 1)
            ->first();
    }

    /**
     * Получить проекты для главной страницы (последние 3)
     */
    public function getLatestProjects(int $limit = 3): array
    {
        return $this->where('publish', 1)
            ->orderBy('priority', 'ASC')
            ->orderBy('date_start', 'DESC')
            ->findAll($limit);
    }

    /**
     * Получить полный путь к проекту
     */
    public function getFullPath(int $id): string
    {
        $project = $this->find($id);
        return $project ? '/' . $project['path'] : '';
    }

    /**
     * Получить дерево проектов для меню
     */
    public function getMenuProjects(): array
    {
        return $this->where('publish', 1)
            ->orderBy('priority', 'ASC')
            ->orderBy('name', 'ASC')
            ->findAll();
    }
}