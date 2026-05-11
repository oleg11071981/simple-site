<?php

/**
 * Модель для работы с таблицей страниц сайта n_site
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
 * Модель страниц сайта
 */
class NSiteModel extends Model
{
    protected $table = 'n_site';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'name', 'path', 'publish', 'more_info',
        'keywords', 'description', 'anons_text',
        'show_in_menu', 'priority', 'new_on_site', 'parent',
        'media',
        'create', 'modify', 'create_by_user', 'modify_by_user',
        'name_en', 'more_info_en', 'anons_text_en', 'keywords_en', 'description_en'
    ];

    protected $useTimestamps = false;
    protected $beforeInsert = ['setCreateFields', 'setModifyFields'];
    protected $beforeUpdate = ['setModifyFields'];

    protected function setCreateFields(array $data): array
    {
        $data['data']['create'] = date('Y-m-d H:i:s');
        $data['data']['create_by_user'] = session()->get('user_id') ?? 0;
        return $data;
    }

    protected function setModifyFields(array $data): array
    {
        $data['data']['modify'] = date('Y-m-d H:i:s');
        $data['data']['modify_by_user'] = session()->get('user_id') ?? 0;
        return $data;
    }

    public function getPublished(int $limit = 0): array
    {
        $builder = $this->where('publish', 1)->orderBy('priority', 'ASC');
        if ($limit > 0) {
            $builder->limit($limit);
        }
        return $builder->findAll();
    }

    public function getMenuPages(int $parent = 0): array
    {
        return $this->where('publish', 1)
            ->where('show_in_menu', 1)
            ->where('parent', $parent)
            ->orderBy('priority', 'ASC')
            ->findAll();
    }

    public function getTree(int $parent = 0): array
    {
        $pages = $this->where('parent', $parent)->orderBy('priority', 'ASC')->findAll();
        foreach ($pages as &$page) {
            $page['children'] = $this->getTree($page['id']);
        }
        return $pages;
    }

    /**
     * Получить дерево страниц для отображения (с полными путями)
     *
     * @param int $parent
     * @return array
     */
    public function getTreeForDisplay(int $parent): array
    {
        $pages = $this->where('parent', $parent)
            ->where('publish', 1)
            ->orderBy('priority', 'ASC')
            ->orderBy('name', 'ASC')
            ->findAll();

        foreach ($pages as &$page) {
            $page['full_path'] = $this->getFullPath($page['id']);
            $page['children'] = $this->getTreeForDisplay($page['id']);
        }

        return $pages;
    }

    public function getBreadcrumbs(int $id): array
    {
        $breadcrumbs = [];
        $current = $this->find($id);
        while ($current && $current['parent'] > 0) {
            array_unshift($breadcrumbs, $current);
            $current = $this->find($current['parent']);
        }
        if ($current) {
            array_unshift($breadcrumbs, $current);
        }
        return $breadcrumbs;
    }

    public function getParentList(int $excludeId = 0): array
    {
        $pages = $this->where('publish', 1)
            ->orderBy('parent', 'ASC')
            ->orderBy('priority', 'ASC')
            ->findAll();
        return $this->buildTreeWithLevels($pages, 0, 0, $excludeId);
    }

    private function buildTreeWithLevels(array $pages, int $parent = 0, int $level = 0, int $excludeId = 0): array
    {
        $result = [];
        foreach ($pages as $page) {
            if ($page['parent'] == $parent && $page['id'] != $excludeId) {
                $page['level'] = $level;
                $result[] = $page;
                $children = $this->buildTreeWithLevels($pages, $page['id'], $level + 1, $excludeId);
                $result = array_merge($result, $children);
            }
        }
        return $result;
    }

    /**
     * Получить полный иерархический путь страницы
     *
     * @param int $id
     * @return string
     */
    public function getFullPath(int $id): string
    {
        $page = $this->find($id);
        if (!$page) {
            return '';
        }

        $path = $page['path'];
        $parent = $page['parent'];

        while ($parent > 0) {
            $parentPage = $this->find($parent);
            if ($parentPage) {
                $path = $parentPage['path'] . '/' . $path;
                $parent = $parentPage['parent'];
            } else {
                break;
            }
        }

        return $path;
    }

    /**
     * Получить страницу по пути с учетом языка
     * @param string $path
     * @param string $lang
     * @return array|null
     */
    public function getByPathWithLang(string $path, string $lang = 'ru'): ?array
    {
        $page = $this->where('path', $path)
            ->where('publish', 1)
            ->first();

        if (!$page) {
            return null;
        }

        if ($lang === 'en') {
            $page['name'] = $page['name_en'] ?? $page['name'];
            $page['more_info'] = $page['more_info_en'] ?? $page['more_info'];
            $page['anons_text'] = $page['anons_text_en'] ?? $page['anons_text'];
            $page['keywords'] = $page['keywords_en'] ?? $page['keywords'];
            $page['description'] = $page['description_en'] ?? $page['description'];
        }

        return $page;
    }

}