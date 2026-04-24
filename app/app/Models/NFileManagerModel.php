<?php

/**
 * Модель для работы с таблицей файлов n_file_manager
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

class NFileManagerModel extends Model
{
    protected $table = 'n_file_manager';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'file_name', 'file_type', 'mime_type', 'name', 'category',
        'width', 'height', 'file_size', 'thumb_width', 'thumb_height',
        'thumb_file_size', 'foto', 'cnt', 'title', 'priority',
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
     * Получить URL изображения
     *
     * @param int $id
     * @return string
     */
    public function getImageUrl(int $id): string
    {
        return '/uploads/' . $id;
    }

    /**
     * Получить URL миниатюры
     *
     * @param int $id
     * @return string
     */
    public function getThumbUrl(int $id): string
    {
        return '/uploads/thumb_' . $id;
    }
}