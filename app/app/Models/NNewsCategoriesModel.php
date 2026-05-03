<?php

namespace App\Models;

use CodeIgniter\Model;

class NNewsCategoriesModel extends Model
{
    protected $table = 'n_news_categories';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'name', 'slug', 'parent', 'description', 'priority',
        'create', 'modify', 'create_by_user', 'modify_by_user'
    ];

    protected $useTimestamps = false;
    protected $beforeInsert = ['setCreateFields', 'setModifyFields', 'generateSlug'];
    protected $beforeUpdate = ['setModifyFields', 'generateSlug'];

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

    protected function generateSlug(array $data): array
    {
        if (empty($data['data']['slug']) && !empty($data['data']['name'])) {
            $slug = mb_strtolower($data['data']['name'], 'UTF-8');
            $slug = str_replace([' ', '_', '.'], '-', $slug);
            $slug = preg_replace('/[^a-zа-я0-9-]/ui', '', $slug);
            $slug = preg_replace('/-+/', '-', $slug);
            $slug = trim($slug, '-');

            $count = $this->where('slug', $slug)->countAllResults();
            if ($count > 0) {
                $slug .= '-' . ($count + 1);
            }

            $data['data']['slug'] = $slug;
        }
        return $data;
    }

    public function getForSelect(int $excludeId = 0): array
    {
        $categories = $this->orderBy('parent', 'ASC')
            ->orderBy('priority', 'ASC')
            ->orderBy('name', 'ASC')
            ->findAll();

        return $this->buildTreeForSelect($categories, 0, 0, $excludeId);
    }

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

    public function getCategoryName(int $id): string
    {
        $category = $this->find($id);
        return $category ? $category['name'] : '';
    }
}