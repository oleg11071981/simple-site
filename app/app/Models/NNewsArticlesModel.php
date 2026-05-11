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
    protected $table = 'n_news_articles';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'name', 'anons_text', 'more_info', 'publish', 'date',
        'path', 'keywords', 'description', 'author', 'source',
        'source_href', 'href', 'foto', 'media', 'type', 'category_news',
        'show_all', 'target', 'publish_time', 'morder',
        'create', 'modify', 'create_by_user', 'modify_by_user',
        'name_en', 'anons_text_en', 'more_info_en', 'keywords_en', 'description_en'
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

    /**
     * Получить название категории новости
     *
     * @param int $categoryId
     * @return string
     */
    public function getCategoryName(int $categoryId): string
    {
        if ($categoryId <= 0) {
            return '';
        }

        $categoriesModel = new NNewsCategoriesModel();
        $category = $categoriesModel->find($categoryId);
        return $category ? $category['name'] : '';
    }

    /**
     * Получить категорию новости (объект)
     *
     * @param int $categoryId
     * @return array|null
     */
    public function getCategory(int $categoryId): ?array
    {
        if ($categoryId <= 0) {
            return null;
        }

        $categoriesModel = new NNewsCategoriesModel();
        return $categoriesModel->find($categoryId);
    }

    /**
     * Получить новость по пути с учетом языка
     * @param string $path
     * @param string $lang
     * @return array|null
     */
    public function getByPathWithLang(string $path, string $lang = 'ru'): ?array
    {
        $news = $this->where('path', $path)
            ->where('publish', 1)
            ->first();

        if (!$news) {
            return null;
        }

        if ($lang === 'en') {
            $news['name'] = $news['name_en'] ?? $news['name'];
            $news['anons_text'] = $news['anons_text_en'] ?? $news['anons_text'];
            $news['more_info'] = $news['more_info_en'] ?? $news['more_info'];
            $news['keywords'] = $news['keywords_en'] ?? $news['keywords'];
            $news['description'] = $news['description_en'] ?? $news['description'];
        }

        return $news;
    }

    /**
     * Получить список новостей с учетом языка
     * @param int $limit
     * @param string $lang
     * @return array
     */
    public function getLatestNewsWithLang(int $limit = 6, string $lang = 'ru'): array
    {
        $news = $this->where('publish', 1)
            ->where('show_all', 1)
            ->orderBy('date', 'DESC')
            ->orderBy('id', 'DESC')
            ->findAll($limit);

        if ($lang === 'en') {
            foreach ($news as &$item) {
                $item['name'] = $item['name_en'] ?? $item['name'];
                $item['anons_text'] = $item['anons_text_en'] ?? $item['anons_text'];
                $item['more_info'] = $item['more_info_en'] ?? $item['more_info'];
                $item['keywords'] = $item['keywords_en'] ?? $item['keywords'];
                $item['description'] = $item['description_en'] ?? $item['description'];
            }
        }

        return $news;
    }

    /**
     * Получить новости с пагинацией с учетом языка
     * @param int $perPage
     * @param string $lang
     * @return array
     */
    public function getPaginatedWithLang(int $perPage = 9, string $lang = 'ru'): array
    {
        $news = $this->where('publish', 1)
            ->orderBy('date', 'DESC')
            ->orderBy('id', 'DESC')
            ->paginate($perPage);

        if ($lang === 'en') {
            foreach ($news as &$item) {
                $item['name'] = $item['name_en'] ?? $item['name'];
                $item['anons_text'] = $item['anons_text_en'] ?? $item['anons_text'];
                $item['more_info'] = $item['more_info_en'] ?? $item['more_info'];
                $item['keywords'] = $item['keywords_en'] ?? $item['keywords'];
                $item['description'] = $item['description_en'] ?? $item['description'];
            }
        }

        return $news;
    }

}