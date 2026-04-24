<?php

/**
 * Модель для работы с таблицей страниц сайта n_site
 *
 * Предоставляет методы для работы со страницами:
 * - CRUD операции
 * - Древовидная структура (родитель-потомок)
 * - Построение меню
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
    /**
     * Имя таблицы
     *
     * @var string
     */
    protected $table = 'n_site';

    /**
     * Первичный ключ
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * Автоинкремент
     *
     * @var bool
     */
    protected $useAutoIncrement = true;

    /**
     * Тип возвращаемых данных
     *
     * @var string
     */
    protected $returnType = 'array';

    /**
     * Мягкое удаление
     *
     * @var bool
     */
    protected $useSoftDeletes = false;

    /**
     * Разрешённые поля для массового заполнения
     *
     * @var string[]
     */
    protected $allowedFields = [
        'name', 'path', 'publish', 'more_info',
        'keywords', 'description', 'anons_text',
        'show_in_menu', 'priority', 'new_on_site', 'parent',  // parent обязательно должен быть здесь
        'create', 'modify', 'create_by_user', 'modify_by_user'
    ];

    /**
     * Отключаем автоматические timestamps
     *
     * @var bool
     */
    protected $useTimestamps = false;

    /**
     * События перед вставкой
     *
     * @var string[]
     */
    protected $beforeInsert = ['setCreateFields', 'setModifyFields'];

    /**
     * События перед обновлением
     *
     * @var string[]
     */
    protected $beforeUpdate = ['setModifyFields'];

    /**
     * Установка полей создания
     *
     * @param array $data Данные
     *
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
     * @param array $data Данные
     *
     * @return array
     */
    protected function setModifyFields(array $data): array
    {
        $data['data']['modify'] = date('Y-m-d H:i:s');
        $data['data']['modify_by_user'] = session()->get('user_id') ?? 0;
        return $data;
    }

    /**
     * Получить все опубликованные страницы
     *
     * @param int $limit Лимит
     *
     * @return array
     */
    public function getPublished(int $limit = 0): array
    {
        $builder = $this->where('publish', 1)
            ->orderBy('priority', 'ASC');

        if ($limit > 0) {
            $builder->limit($limit);
        }

        return $builder->findAll();
    }

    /**
     * Получить страницы для меню
     *
     * @param int $parent Родительская страница
     *
     * @return array
     */
    public function getMenuPages(int $parent = 0): array
    {
        return $this->where('publish', 1)
            ->where('show_in_menu', 1)
            ->where('parent', $parent)
            ->orderBy('priority', 'ASC')
            ->findAll();
    }

    /**
     * Построить дерево страниц
     *
     * @param int $parent Родитель
     *
     * @return array
     */
    public function getTree(int $parent = 0): array
    {
        $pages = $this->where('parent', $parent)
            ->orderBy('priority', 'ASC')
            ->findAll();

        foreach ($pages as &$page) {
            $page['children'] = $this->getTree($page['id']);
        }

        return $pages;
    }

    /**
     * Получить хлебные крошки
     *
     * @param int $id ID страницы
     *
     * @return array
     */
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

    /**
     * Получить список страниц для выбора родителя с уровнями
     *
     * @param int $excludeId ID страницы для исключения
     * @return array
     */
    public function getParentList(int $excludeId = 0): array
    {
        $pages = $this->where('publish', 1)
            ->orderBy('parent', 'ASC')
            ->orderBy('priority', 'ASC')
            ->findAll();

        return $this->buildTreeWithLevels($pages, 0, 0, $excludeId);
    }

    /**
     * Построение дерева с уровнями
     *
     * @param array $pages Все страницы
     * @param int $parent Parent ID
     * @param int $level Уровень вложенности
     * @param int $excludeId ID для исключения
     * @return array
     */
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

}