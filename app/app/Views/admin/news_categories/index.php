<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>

    <div class="page-header">
        <div class="header-actions">
            <h1>Категории новостей</h1>
            <a href="/admin-panel/news-categories/create?parent=<?= $parent_id ?? 0 ?>" class="btn-create">➕ Добавить категорию</a>
        </div>
        <p>Управление категориями для новостей (поддерживается вложенность)</p>
    </div>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success" id="successAlert">
        <span class="alert-icon">✓</span>
        <span class="alert-message"><?= esc(session()->getFlashdata('success')) ?></span>
        <button class="alert-close" onclick="this.parentElement.style.display='none'">×</button>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-error" id="errorAlert">
        <span class="alert-icon">⚠</span>
        <span class="alert-message"><?= esc(session()->getFlashdata('error')) ?></span>
        <button class="alert-close" onclick="this.parentElement.style.display='none'">×</button>
    </div>
<?php endif; ?>

    <!-- Хлебные крошки -->
<?php if (!empty($breadcrumbs)): ?>
    <div class="breadcrumbs">
        <a href="/admin-panel/news-categories">📁 Все категории</a>
        <?php foreach ($breadcrumbs as $crumb): ?>
            <span class="breadcrumb-separator">/</span>
            <a href="/admin-panel/news-categories?parent=<?= $crumb['id'] ?>">
                <?= esc($crumb['name']) ?>
            </a>
        <?php endforeach; ?>
        <?php if ($parent_id > 0): ?>
            <span class="breadcrumb-separator">/</span>
            <span class="breadcrumb-current"><?= esc($current_category_name) ?></span>
        <?php endif; ?>
    </div>
<?php elseif ($parent_id > 0): ?>
    <div class="breadcrumbs">
        <a href="/admin-panel/news-categories">📁 Все категории</a>
        <span class="breadcrumb-separator">/</span>
        <span class="breadcrumb-current"><?= esc($current_category_name) ?></span>
    </div>
<?php endif; ?>

    <!-- Таблица категорий -->
    <div class="table-container">
        <form action="/admin-panel/news-categories/bulk-action" method="post" id="bulkForm">
            <?= csrf_field() ?>
            <input type="hidden" name="parent" value="<?= $parent_id ?? 0 ?>">

            <table class="data-table">
                <thead>
                <tr>
                    <th style="width: 30px">
                        <input type="checkbox" id="selectAll" onclick="toggleAll(this)">
                    </th>
                    <th style="width: 60px">ID</th>
                    <th>Название категории</th>
                    <th style="width: 100px">Новостей</th>
                    <th style="width: 80px">Приоритет</th>
                    <th style="width: 140px">Дата создания</th>
                    <th style="width: 100px">Действия</th>
                </tr>
                </thead>
                <tbody>
                <?php if (!empty($categories) && is_array($categories)): ?>
                    <?php foreach ($categories as $cat): ?>
                        <tr class="<?= $cat['has_children'] ? 'has-children' : '' ?>">
                            <td class="text-center">
                                <input type="checkbox" name="selected_ids[]" value="<?= $cat['id'] ?>">
                            </td>
                            <td class="text-center"><?= esc($cat['id']) ?></td>
                            <td>
                                <div class="category-name">
                                    <span class="category-icon"><?= $cat['has_children'] ? '📁' : '📂' ?></span>
                                    <a href="/admin-panel/news-categories?parent=<?= $cat['id'] ?>" class="category-link">
                                        <?= esc($cat['name']) ?>
                                    </a>
                                </div>
                                <?php if ($cat['has_children']): ?>
                                    <div class="category-children-hint">
                                        <small>📁 есть подкатегории</small>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <a href="/admin-panel/news?category_news=<?= $cat['id'] ?>" class="badge badge-info">
                                    <?= $cat['news_count'] ?>
                                </a>
                            </td>
                            <td class="text-center"><?= esc($cat['priority'] ?? 0) ?></td>
                            <td class="date-cell"><?= date('d.m.Y H:i', strtotime($cat['create'])) ?></td>
                            <td class="actions">
                                <a href="/admin-panel/news-categories/edit/<?= $cat['id'] ?>" class="btn-icon" title="Редактировать">
                                    <span class="icon-edit">✏️</span>
                                </a>
                                <a href="/admin-panel/news-categories/create?parent=<?= $cat['id'] ?>" class="btn-icon" title="Добавить подкатегорию">
                                    <span class="icon-add">➕</span>
                                </a>
                                <a href="/admin-panel/news-categories/delete/<?= $cat['id'] ?>" class="btn-icon" title="Удалить" onclick="return confirm('Удалить категорию «<?= esc($cat['name']) ?>»?')">
                                    <span class="icon-delete">🗑️</span>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center">Категории не найдены</td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>

            <!-- Массовые действия -->
            <div class="table-actions">
                <div class="bulk-actions">
                    <span>С отмеченными:</span>
                    <select name="bulk_action" class="bulk-select">
                        <option value="">Выберите действие</option>
                        <option value="delete">Удалить</option>
                    </select>
                    <button type="button" class="btn-apply" onclick="confirmBulkAction('bulkForm')">Применить</button>
                </div>
            </div>
        </form>
    </div>

<?= $this->endSection() ?>