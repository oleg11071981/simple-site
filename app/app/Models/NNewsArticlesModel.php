<?php

/**
 * Модель для работы с таблицей новостей n_news_articles
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

class NNewsArticlesModel extends Model
{

    /**
     * Категории новостей
     */
    const CATEGORY_COMMITTEE = 1;      // Новости комитета
    const CATEGORY_RUSSIA_WORLD = 2;   // Новости в РФ и мир

    protected $table = 'n_news_articles';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'name', 'anons_text', 'more_info', 'publish', 'date',
        'path', 'keywords', 'description', 'author', 'source',
        'source_href', 'href', 'foto', 'media', 'type', 'category_news',  // добавляем category_news
        'show_all', 'target', 'publish_time', 'morder',
        'create', 'modify', 'create_by_user', 'modify_by_user'
    ];

    protected $useTimestamps = false;
    protected $beforeInsert = ['setCreateFields', 'setModifyFields'];
    protected $beforeUpdate = ['setModifyFields'];

    /**
     * Установка полей создания
     *
     * @param array $data
     * @return array
     */
    protected function setCreateFields(array $data): array
    {
        $data['data']['create'] = date('Y-m-d H:i:s');
        $data['data']['create_by_user'] = session()->get('user_id') ?? 0;
        // Устанавливаем дату новости, если не задана
        if (empty($data['data']['date'])) {
            $data['data']['date'] = date('Y-m-d');
        }
        return $data;
    }

    /**
     * Установка полей изменения
     *
     * @param array $data
     * @return array
     */
    protected function setModifyFields(array $data): array
    {
        $data['data']['modify'] = date('Y-m-d H:i:s');
        $data['data']['modify_by_user'] = session()->get('user_id') ?? 0;
        return $data;
    }

    /**
     * Получить опубликованные новости
     *
     * @param int $limit
     * @return array
     */
    public function getPublished(int $limit = 10): array
    {
        return $this->where('publish', 1)
            ->orderBy('date', 'DESC')
            ->orderBy('id', 'DESC')
            ->findAll($limit);
    }

    /**
     * Получить новости для главной страницы
     *
     * @param int $limit
     * @return array
     */
    public function getLatestNews(int $limit = 6): array
    {
        return $this->where('publish', 1)
            ->where('show_all', 1)
            ->orderBy('date', 'DESC')
            ->orderBy('id', 'DESC')
            ->findAll($limit);
    }

    /**
     * Получить новость по пути
     *
     * @param string $path
     * @return array|null
     */
    public function getByPath(string $path): ?array
    {
        return $this->where('path', $path)
            ->where('publish', 1)
            ->first();
    }
}