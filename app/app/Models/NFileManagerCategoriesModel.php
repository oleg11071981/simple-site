<?php

/**
 * Модель для работы с таблицей категорий файлов n_file_manager_categories
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

class NFileManagerCategoriesModel extends Model
{
    protected $table = 'n_file_manager_categories';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'name', 'path', 'parent', 'priority', 'description',
        'create', 'modify', 'create_by_user', 'modify_by_user'
    ];

    protected $useTimestamps = false;
    protected $beforeInsert = ['setCreateFields', 'setModifyFields'];
    protected $beforeUpdate = ['setModifyFields'];

    protected function setCreateFields(array $data): array
    {
        $data['data']['create'] = date('Y-m-d H:i:s');
        $data['data']['create_by_user'] = session()->get('user_id') ?? 0;

        // Генерация пути из названия
        if (empty($data['data']['path']) && !empty($data['data']['name'])) {
            $data['data']['path'] = $this->generateSlug($data['data']['name']);
        }

        return $data;
    }

    protected function setModifyFields(array $data): array
    {
        $data['data']['modify'] = date('Y-m-d H:i:s');
        $data['data']['modify_by_user'] = session()->get('user_id') ?? 0;
        return $data;
    }

    /**
     * Генерация slug из названия
     */
    private function generateSlug(string $name): string
    {
        $slug = mb_strtolower($name, 'UTF-8');
        $slug = str_replace([' ', '_', '.'], '-', $slug);
        $slug = preg_replace('/[^a-zа-я0-9-]/ui', '', $slug);
        $slug = preg_replace('/-+/', '-', $slug);
        $slug = trim($slug, '-');

        // Проверка уникальности
        $count = $this->where('path', $slug)->countAllResults();
        if ($count > 0) {
            $slug .= '-' . ($count + 1);
        }

        return $slug;
    }

    /**
     * Получить дерево категорий
     */
    public function getTree(int $parent = 0): array
    {
        $categories = $this->where('parent', $parent)
            ->orderBy('priority', 'ASC')
            ->orderBy('name', 'ASC')
            ->findAll();

        foreach ($categories as &$cat) {
            $cat['children'] = $this->getTree($cat['id']);
            $cat['files_count'] = $this->getFilesCount($cat['id']);
        }

        return $categories;
    }

    /**
     * Получить количество файлов в категории
     */
    public function getFilesCount(int $categoryId): int
    {
        $filesModel = new NFileManagerModel();
        return $filesModel->where('category', $categoryId)->countAllResults();
    }

    /**
     * Получить список категорий для селекта (с уровнями)
     */
    public function getForSelect(int $excludeId = 0): array
    {
        $categories = $this->orderBy('parent', 'ASC')
            ->orderBy('priority', 'ASC')
            ->orderBy('name', 'ASC')
            ->findAll();

        return $this->buildTreeForSelect($categories, 0, 0, $excludeId);
    }

    /**
     * Построение дерева для селекта
     */
    private function buildTreeForSelect(array $categories, int $parent = 0, int $level = 0, int $excludeId = 0): array
    {
        $result = [];

        foreach ($categories as $cat) {
            if ($cat['parent'] == $parent && $cat['id'] != $excludeId) {
                $cat['level'] = $level;
                $result[] = $cat;
                $children = $this->buildTreeForSelect($categories, $cat['id'], $level + 1, $excludeId);
                $result = array_merge($result, $children);
            }
        }

        return $result;
    }

    /**
     * Получить хлебные крошки для категории
     */
    public function getBreadcrumbs(int $id): array
    {
        $breadcrumbs = [];
        $current = $this->find($id);

        $parents = [];
        while ($current && $current['parent'] > 0) {
            array_unshift($parents, $current);
            $current = $this->find($current['parent']);
        }

        foreach ($parents as $parent) {
            $breadcrumbs[] = [
                'id'   => $parent['id'],
                'name' => $parent['name'],
            ];
        }

        return $breadcrumbs;
    }

    /**
     * Проверить, есть ли дочерние категории
     */
    public function hasChildren(int $id): bool
    {
        return $this->where('parent', $id)->countAllResults() > 0;
    }
}