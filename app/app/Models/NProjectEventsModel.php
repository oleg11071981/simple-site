<?php

namespace App\Models;

use CodeIgniter\Model;

class NProjectEventsModel extends Model
{
    protected $table = 'n_project_events';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'project_id',
        'name',
        'path',
        'anons_text',
        'more_info',      // изменено с description
        'foto',
        'media',
        'date_start',
        'date_end',
        'location',
        'link',
        'priority',
        'publish',
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

            // Проверка уникальности в рамках одного проекта
            $count = $this->where('project_id', $data['data']['project_id'] ?? 0)
                ->where('path', $slug)
                ->countAllResults();
            if ($count > 0) {
                $slug .= '-' . ($count + 1);
            }

            $data['data']['path'] = $slug;
        }
        return $data;
    }

    /**
     * Получить мероприятия по ID проекта
     */
    public function getByProjectId(int $projectId): array
    {
        return $this->where('project_id', $projectId)
            ->where('publish', 1)
            ->orderBy('priority', 'ASC')
            ->orderBy('date_start', 'ASC')
            ->findAll();
    }

    /**
     * Получить все опубликованные мероприятия
     */
    public function getPublished(): array
    {
        return $this->where('publish', 1)
            ->orderBy('date_start', 'DESC')
            ->findAll();
    }

    /**
     * Получить мероприятие по slug проекта и slug мероприятия
     */
    public function getByProjectPathAndEventPath(string $projectPath, string $eventPath): ?array
    {
        $projectsModel = new NProjectsModel();
        $project = $projectsModel->getByPath($projectPath);

        if (!$project) {
            return null;
        }

        return $this->where('project_id', $project['id'])
            ->where('path', $eventPath)
            ->where('publish', 1)
            ->first();
    }

    /**
     * Получить полный URL мероприятия
     */
    public function getFullUrl(int $eventId): string
    {
        $event = $this->find($eventId);
        if (!$event) {
            return '';
        }

        $projectsModel = new NProjectsModel();
        $project = $projectsModel->find($event['project_id']);

        return $project ? '/projects/' . $project['path'] . '/' . $event['path'] : '';
    }

    /**
     * Проверить, есть ли у проекта мероприятия
     */
    public function hasEvents(int $projectId): bool
    {
        return $this->where('project_id', $projectId)
                ->where('publish', 1)
                ->countAllResults() > 0;
    }

    /**
     * Количество мероприятий у проекта
     */
    public function getEventsCount(int $projectId): int
    {
        return $this->where('project_id', $projectId)
            ->countAllResults();
    }

    /**
     * Получить следующие мероприятия (для блока "Предстоящие мероприятия")
     */
    public function getUpcomingEvents(int $limit = 5): array
    {
        return $this->where('publish', 1)
            ->where('date_start >=', date('Y-m-d'))
            ->orderBy('date_start', 'ASC')
            ->findAll($limit);
    }

    /**
     * Получить прошедшие мероприятия
     */
    public function getPastEvents(int $limit = 5): array
    {
        return $this->where('publish', 1)
            ->where('date_end <', date('Y-m-d'))
            ->orderBy('date_end', 'DESC')
            ->findAll($limit);
    }
}