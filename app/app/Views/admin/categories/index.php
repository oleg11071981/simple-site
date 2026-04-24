<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>

    <div class="page-header">
        <div class="header-actions">
            <h1>Категории файлов</h1>
            <a href="/admin-panel/categories/create" class="btn-create">➕ Добавить категорию</a>
        </div>
        <p>Управление категориями для файлового менеджера</p>
    </div>

    <!-- Flash сообщения -->
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

    <!-- Поиск -->
    <div class="filters-bar">
        <form action="/admin-panel/categories" method="get" class="filters-form">
            <div class="filter-group">
                <label>Поиск:</label>
                <input type="text" name="search" class="filter-select" style="width: 250px;" value="<?= esc($search ?? '') ?>" placeholder="Название категории...">
            </div>
            <div class="filter-group">
                <label>На странице:</label>
                <select name="per_page" class="filter-select" onchange="this.form.submit()">
                    <option value="10" <?= ($per_page ?? 50) == 10 ? 'selected' : '' ?>>10</option>
                    <option value="20" <?= ($per_page ?? 50) == 20 ? 'selected' : '' ?>>20</option>
                    <option value="30" <?= ($per_page ?? 50) == 30 ? 'selected' : '' ?>>30</option>
                    <option value="50" <?= ($per_page ?? 50) == 50 ? 'selected' : '' ?>>50</option>
                    <option value="100" <?= ($per_page ?? 50) == 100 ? 'selected' : '' ?>>100</option>
                </select>
            </div>
            <button type="submit" class="btn-apply">Найти</button>
            <?php if (!empty($search)): ?>
                <a href="/admin-panel/categories" class="btn-cancel" style="padding: 6px 16px;">Сбросить</a>
            <?php endif; ?>
        </form>
    </div>

    <!-- Таблица категорий -->
    <div class="table-container">
        <table class="data-table">
            <thead>
            <tr>
                <th style="width: 60px">ID</th>
                <th>Название категории</th>
                <th style="width: 100px">Файлов</th>
                <th style="width: 140px">Дата создания</th>
                <th style="width: 100px">Действия</th>
            </tr>
            </thead>
            <tbody>
            <?php if (!empty($categories) && is_array($categories)): ?>
                <?php foreach ($categories as $cat): ?>
                    <tr>
                        <td class="text-center"><?= esc($cat['id']) ?></td>
                        <td>
                            <div class="page-name">
                                <span class="page-link">📁 <?= esc($cat['name']) ?></span>
                            </div>
                        </td>
                        <td class="text-center">
                            <a href="/admin-panel/files?category=<?= $cat['id'] ?>" class="badge badge-info">
                                <?= $cat['files_count'] ?> файлов
                            </a>
                        </td>
                        <td class="date-cell"><?= date('d.m.Y H:i', strtotime($cat['create'])) ?></td>
                        <td class="actions">
                            <a href="/admin-panel/categories/edit/<?= $cat['id'] ?>" class="btn-icon" title="Редактировать">
                                <span class="icon-edit">✏️</span>
                            </a>
                            <a href="/admin-panel/categories/delete/<?= $cat['id'] ?>" class="btn-icon" title="Удалить" onclick="return confirm('Удалить категорию «<?= esc($cat['name']) ?>»?')">
                                <span class="icon-delete">🗑️</span>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" class="text-center">Категории не найдены</td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>

        <?php if (isset($pager) && $pager->getPageCount() > 1): ?>
            <div class="pagination">
                <?= $pager->links() ?>
            </div>
        <?php endif; ?>
    </div>

<?= $this->endSection() ?>