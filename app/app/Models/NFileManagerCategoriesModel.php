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
        'name',
        'create', 'modify', 'create_by_user', 'modify_by_user'
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

    /**
     * Получить категории для селекта
     *
     * @return array
     */
    public function getForSelect(): array
    {
        $categories = $this->orderBy('name', 'ASC')->findAll();
        $result = [0 => '— Без категории —'];

        foreach ($categories as $cat) {
            $result[$cat['id']] = $cat['name'];
        }

        return $result;
    }
}