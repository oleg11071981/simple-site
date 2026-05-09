<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>

    <div class="page-header">
        <div class="header-actions">
            <h1>Управление проектами</h1>
            <a href="/admin-panel/projects/create" class="btn-create">➕ Добавить проект</a>
        </div>
        <p>Управление проектами и связанными с ними мероприятиями</p>
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

    <!-- Фильтры -->
    <div class="filters-bar">
        <form action="/admin-panel/projects" method="get" class="filters-form">
            <div class="filter-group">
                <label>Поиск:</label>
                <input type="text" name="search" class="filter-input" value="<?= esc($search ?? '') ?>" placeholder="Название проекта...">
            </div>

            <div class="filter-group">
                <label>Статус:</label>
                <select name="publish" class="filter-select">
                    <option value="" <?= ($publish ?? '') == '' ? 'selected' : '' ?>>Все</option>
                    <option value="1" <?= ($publish ?? '') == '1' ? 'selected' : '' ?>>Опубликованные</option>
                    <option value="0" <?= ($publish ?? '') == '0' ? 'selected' : '' ?>>Черновики</option>
                </select>
            </div>

            <div class="filter-group">
                <label>На странице:</label>
                <select name="per_page" class="filter-select">
                    <option value="10" <?= ($per_page ?? 20) == 10 ? 'selected' : '' ?>>10</option>
                    <option value="20" <?= ($per_page ?? 20) == 20 ? 'selected' : '' ?>>20</option>
                    <option value="50" <?= ($per_page ?? 20) == 50 ? 'selected' : '' ?>>50</option>
                    <option value="100" <?= ($per_page ?? 20) == 100 ? 'selected' : '' ?>>100</option>
                </select>
            </div>

            <div class="filter-actions">
                <button type="submit" class="btn-apply">Применить</button>
                <a href="/admin-panel/projects" class="filter-reset-btn">Сбросить</a>
            </div>
        </form>
    </div>

    <!-- Таблица проектов -->
    <div class="table-container">
        <form action="/admin-panel/projects/bulk-action" method="post" id="bulkForm">
            <?= csrf_field() ?>

            <table class="data-table">
                <thead>
                <tr>
                    <th style="width: 30px">
                        <input type="checkbox" id="selectAll" onclick="toggleAll(this)">
                    </th>
                    <th style="width: 60px">ID</th>
                    <th>Название проекта</th>
                    <th style="width: 100px">Мероприятий</th>
                    <th style="width: 80px">Приоритет</th>
                    <th style="width: 100px">Статус</th>
                    <th style="width: 140px">Дата создания</th>
                    <th style="width: 140px">Действия</th>
                </tr>
                </thead>
                <tbody>
                <?php if (!empty($projects) && is_array($projects)): ?>
                    <?php foreach ($projects as $project): ?>
                        <tr>
                            <td class="text-center">
                                <input type="checkbox" name="selected_ids[]" value="<?= $project['id'] ?>">
                            </td>
                            <td class="text-center"><?= esc($project['id']) ?></td>
                            <td>
                                <div class="project-name">
                                    <span class="project-icon">📁</span>
                                    <a href="/admin-panel/projects/edit/<?= $project['id'] ?>" class="project-link">
                                        <?= esc($project['name']) ?>
                                    </a>
                                </div>
                                <?php if (!empty($project['path'])): ?>
                                    <div class="project-path">
                                        <small>/<?= esc($project['path']) ?></small>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <a href="/admin-panel/events?project_id=<?= $project['id'] ?>" class="events-count-link">
                                    <?= $project['events_count'] ?? 0 ?>
                                </a>
                            </td>
                            <td class="text-center"><?= esc($project['priority'] ?? 0) ?></td>
                            <td class="text-center">
                                <?php if ($project['publish'] == 1): ?>
                                    <span class="badge badge-success">Опубликовано</span>
                                <?php else: ?>
                                    <span class="badge badge-warning">Черновик</span>
                                <?php endif; ?>
                            </td>
                            <td class="date-cell"><?= date('d.m.Y H:i', strtotime($project['create'])) ?></td>
                            <td class="actions">
                                <a href="/admin-panel/events/create?project_id=<?= $project['id'] ?>" class="btn-icon" title="Добавить мероприятие">
                                    <span class="icon-add">➕</span>
                                </a>
                                <a href="/admin-panel/projects/toggle/<?= $project['id'] ?>" class="btn-icon" title="<?= $project['publish'] == 1 ? 'Снять с публикации' : 'Опубликовать' ?>">
                                    <?php if ($project['publish'] == 1): ?>
                                        <span class="icon-eye">👁️</span>
                                    <?php else: ?>
                                        <span class="icon-eye-off">👁️‍🗨️</span>
                                    <?php endif; ?>
                                </a>
                                <a href="/admin-panel/projects/edit/<?= $project['id'] ?>" class="btn-icon" title="Редактировать">
                                    <span class="icon-edit">✏️</span>
                                </a>
                                <a href="/admin-panel/projects/delete/<?= $project['id'] ?>" class="btn-icon" title="Удалить" onclick="return confirm('Удалить проект «<?= esc($project['name']) ?>»? Все связанные мероприятия также будут удалены.')">
                                    <span class="icon-delete">🗑️</span>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="text-center">Проекты не найдены</td>
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
                        <option value="publish">Опубликовать</option>
                        <option value="unpublish">Снять с публикации</option>
                        <option value="delete">Удалить</option>
                    </select>
                    <button type="button" class="btn-apply" onclick="confirmBulkAction('bulkForm')">Применить</button>
                </div>

                <?php if (isset($pager) && $pager->getPageCount() > 1): ?>
                    <div class="pagination">
                        <?= $pager->links() ?>
                    </div>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <style>
        .project-name {
            margin-bottom: 4px;
        }

        .project-link {
            color: #2c3e50;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.2s;
        }

        .project-link:hover {
            color: #007bff;
            text-decoration: underline;
        }

        .project-icon {
            font-size: 14px;
            margin-right: 4px;
        }

        .project-path small {
            font-size: 11px;
            color: #6c757d;
            font-family: monospace;
        }

        .events-count-link {
            color: #007bff;
            text-decoration: none;
            font-weight: 500;
            font-size: 13px;
            padding: 4px 8px;
            background: #e7f1ff;
            border-radius: 4px;
            display: inline-block;
            transition: all 0.2s;
        }

        .events-count-link:hover {
            background: #007bff;
            color: white;
            text-decoration: none;
        }
    </style>

<?= $this->endSection() ?>