<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>

    <div class="page-header">
        <div class="header-actions">
            <h1>Управление мероприятиями</h1>
            <!--a href="/admin-panel/events/create?project_id=<?= $project_id ?? 0 ?>" class="btn-create">➕ Добавить мероприятие</a-->
        </div>
        <p>Управление мероприятиями всех проектов</p>
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
        <form action="/admin-panel/events" method="get" class="filters-form">
            <div class="filter-group">
                <label>Проект:</label>
                <select name="project_id" class="filter-select">
                    <option value="0" <?= ($project_id ?? 0) == 0 ? 'selected' : '' ?>>Все проекты</option>
                    <?php if (!empty($projects)): ?>
                        <?php foreach ($projects as $proj): ?>
                            <option value="<?= $proj['id'] ?>" <?= ($project_id ?? 0) == $proj['id'] ? 'selected' : '' ?>>
                                <?= esc($proj['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>

            <div class="filter-group">
                <label>Поиск:</label>
                <input type="text" name="search" class="filter-input" value="<?= esc($search ?? '') ?>" placeholder="Название мероприятия...">
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
                <select name="per_page" class="filter-select" onchange="this.form.submit()">
                    <option value="10" <?= ($per_page ?? 20) == 10 ? 'selected' : '' ?>>10</option>
                    <option value="20" <?= ($per_page ?? 20) == 20 ? 'selected' : '' ?>>20</option>
                    <option value="50" <?= ($per_page ?? 20) == 50 ? 'selected' : '' ?>>50</option>
                    <option value="100" <?= ($per_page ?? 20) == 100 ? 'selected' : '' ?>>100</option>
                </select>
            </div>

            <div class="filter-actions">
                <button type="submit" class="btn-apply">Применить</button>
                <a href="/admin-panel/events" class="filter-reset-btn">Сбросить</a>
            </div>
        </form>
    </div>

    <!-- Таблица мероприятий -->
    <div class="table-container">
        <table class="data-table">
            <thead>
            <tr>
                <th style="width: 60px">ID</th>
                <th>Название мероприятия</th>
                <th>Проект</th>
                <th style="width: 110px">Дата начала</th>
                <th style="width: 110px">Дата окончания</th>
                <th style="width: 100px">Статус</th>
                <th style="width: 140px">Дата создания</th>
                <th style="width: 100px">Действия</th>
            </tr>
            </thead>
            <tbody>
            <?php if (!empty($events) && is_array($events)): ?>
                <?php foreach ($events as $event): ?>
                    <tr>
                        <td class="text-center"><?= esc($event['id']) ?></td>
                        <td>
                            <div class="event-name">
                                <span class="event-icon">📅</span>
                                <a href="/admin-panel/events/edit/<?= $event['id'] ?>" class="event-link">
                                    <?= esc($event['name']) ?>
                                </a>
                            </div>
                            <?php if (!empty($event['location'])): ?>
                                <div class="event-location">
                                    <small>📍 <?= esc($event['location']) ?></small>
                                </div>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="page-name">
                                <span class="page-icon">📄</span>
                                <a href="/admin-panel/projects/edit/<?= $event['project_id'] ?>" class="page-link">
                                    <?= esc($event['project_name'] ?? '—') ?>
                                </a>
                            </div>
                        </td>
                        <td class="date-cell"><?= $event['date_start'] ? date('d.m.Y', strtotime($event['date_start'])) : '—' ?></td>
                        <td class="date-cell"><?= $event['date_end'] ? date('d.m.Y', strtotime($event['date_end'])) : '—' ?></td>
                        <td class="text-center">
                            <?php if ($event['publish'] == 1): ?>
                                <span class="badge badge-success">Опубликовано</span>
                            <?php else: ?>
                                <span class="badge badge-warning">Черновик</span>
                            <?php endif; ?>
                        </td>
                        <td class="date-cell"><?= date('d.m.Y H:i', strtotime($event['create'])) ?></td>
                        <td class="actions">
                            <a href="/admin-panel/events/toggle/<?= $event['id'] ?>" class="btn-icon" title="Переключить статус">
                                <?php if ($event['publish'] == 1): ?>
                                    <span class="icon-eye">👁️</span>
                                <?php else: ?>
                                    <span class="icon-eye-off">👁️‍🗨️</span>
                                <?php endif; ?>
                            </a>
                            <a href="/admin-panel/events/edit/<?= $event['id'] ?>" class="btn-icon" title="Редактировать">
                                <span class="icon-edit">✏️</span>
                            </a>
                            <a href="/admin-panel/events/delete/<?= $event['id'] ?>" class="btn-icon" title="Удалить" onclick="return confirm('Удалить мероприятие «<?= esc($event['name']) ?>»?')">
                                <span class="icon-delete">🗑️</span>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8" class="text-center">Мероприятия не найдены</td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>

        <!-- Пагинация -->
        <?php if (isset($pager) && $pager->getPageCount() > 1): ?>
            <div class="table-actions">
                <div class="pagination">
                    <?= $pager->links() ?>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <style>
        .event-name {
            margin-bottom: 4px;
        }

        .event-link {
            color: #2c3e50;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.2s;
        }

        .event-link:hover {
            color: #007bff;
            text-decoration: underline;
        }

        .event-icon {
            font-size: 14px;
            margin-right: 4px;
        }

        .event-location small {
            font-size: 11px;
            color: #6c757d;
        }
    </style>

<?= $this->endSection() ?>