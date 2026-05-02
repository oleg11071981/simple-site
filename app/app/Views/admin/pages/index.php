<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>

    <div class="page-header">
        <div class="header-actions">
            <h1>Страницы сайта</h1>
            <a href="/admin-panel/pages/create?parent=<?= $parent_id ?? 0 ?>" class="btn-create">➕ Добавить страницу</a>
        </div>
        <p>Управление структурой и содержимым страниц сайта</p>
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

    <!-- Хлебные крошки -->
<?php if (!empty($breadcrumbs)): ?>
    <div class="breadcrumbs">
        <a href="/admin-panel/pages">📁 Все страницы</a>
        <?php foreach ($breadcrumbs as $crumb): ?>
            <span class="breadcrumb-separator">/</span>
            <a href="/admin-panel/pages?parent=<?= $crumb['id'] ?>">
                <?= esc($crumb['name']) ?>
            </a>
        <?php endforeach; ?>
        <?php if ($parent_id > 0): ?>
            <span class="breadcrumb-separator">/</span>
            <span class="breadcrumb-current"><?= esc($current_page_name) ?></span>
        <?php endif; ?>
    </div>
<?php elseif ($parent_id > 0): ?>
    <div class="breadcrumbs">
        <a href="/admin-panel/pages">📁 Все страницы</a>
        <span class="breadcrumb-separator">/</span>
        <span class="breadcrumb-current"><?= esc($current_page_name) ?></span>
    </div>
<?php endif; ?>

    <!-- Фильтры и сортировка -->
    <div class="filters-bar">
        <form action="/admin-panel/pages" method="get" class="filters-form">
            <input type="hidden" name="parent" value="<?= $parent_id ?? 0 ?>">

            <div class="filter-group">
                <label>Показывать:</label>
                <select name="show" class="filter-select" onchange="this.form.submit()">
                    <option value="1" <?= ($show ?? 1) == 1 ? 'selected' : '' ?>>Все страницы</option>
                    <option value="2" <?= ($show ?? 1) == 2 ? 'selected' : '' ?>>Опубликованные</option>
                    <option value="3" <?= ($show ?? 1) == 3 ? 'selected' : '' ?>>Черновики</option>
                </select>
            </div>

            <div class="filter-group">
                <label>Сортировать по:</label>
                <select name="sort" class="filter-select" onchange="this.form.submit()">
                    <option value="1" <?= ($sort ?? 2) == 1 ? 'selected' : '' ?>>ID (возрастание)</option>
                    <option value="2" <?= ($sort ?? 2) == 2 ? 'selected' : '' ?>>ID (убывание)</option>
                    <option value="3" <?= ($sort ?? 2) == 3 ? 'selected' : '' ?>>Названию (А-Я)</option>
                    <option value="4" <?= ($sort ?? 2) == 4 ? 'selected' : '' ?>>Названию (Я-А)</option>
                    <option value="5" <?= ($sort ?? 2) == 5 ? 'selected' : '' ?>>Дате создания (старые)</option>
                    <option value="6" <?= ($sort ?? 2) == 6 ? 'selected' : '' ?>>Дате создания (новые)</option>
                    <option value="7" <?= ($sort ?? 2) == 7 ? 'selected' : '' ?>>Дате изменения (старые)</option>
                    <option value="8" <?= ($sort ?? 2) == 8 ? 'selected' : '' ?>>Дате изменения (новые)</option>
                    <option value="9" <?= ($sort ?? 2) == 9 ? 'selected' : '' ?>>Статусу (сначала опубликованные)</option>
                    <option value="10" <?= ($sort ?? 2) == 10 ? 'selected' : '' ?>>Статусу (сначала черновики)</option>
                </select>
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
        </form>
    </div>

    <!-- Таблица страниц -->
    <div class="table-container">
        <form action="/admin-panel/pages/bulk-action" method="post" id="bulkForm">
            <?= csrf_field() ?>
            <input type="hidden" name="parent" value="<?= $parent_id ?? 0 ?>">

            <table class="data-table">
                <thead>
                <tr>
                    <th style="width: 30px">
                        <input type="checkbox" id="selectAll" onclick="toggleAll(this)">
                    </th>
                    <th style="width: 60px">ID</th>
                    <th>Название страницы</th>
                    <th style="width: 100px">Статус</th>
                    <th style="width: 140px">Время создания</th>
                    <th style="width: 140px">Время изменения</th>
                    <th style="width: 100px">Действия</th>
                </tr>
                </thead>
                <tbody>
                <?php if (!empty($pages) && is_array($pages)): ?>
                    <?php foreach ($pages as $page): ?>
                        <tr class="<?= $page['publish'] == 0 ? 'draft-row' : '' ?>">
                            <td class="text-center">
                                <input type="checkbox" name="selected_ids[]" value="<?= $page['id'] ?>">
                            </td>
                            <td class="text-center"><?= esc($page['id']) ?></td>
                            <td>
                                <div class="page-name">
                                    <span class="page-icon">📄</span>
                                    <a href="/admin-panel/pages?parent=<?= $page['id'] ?>" class="page-link">
                                        <?= esc($page['name']) ?>
                                    </a>
                                </div>
                                <div class="page-path">
                                    <small>/<?= esc($page['path']) ?></small>
                                </div>
                            </td>
                            <td>
                                <?php if ($page['publish'] == 1): ?>
                                    <span class="badge badge-success">Опубликовано</span>
                                <?php else: ?>
                                    <span class="badge badge-warning">Черновик</span>
                                <?php endif; ?>
                            </td>
                            <td class="date-cell"><?= date('d.m.Y H:i', strtotime($page['create'])) ?></td>
                            <td class="date-cell"><?= date('d.m.Y H:i', strtotime($page['modify'])) ?></td>
                            <td class="actions">
                                <a href="/admin-panel/pages/toggle/<?= $page['id'] ?>?parent=<?= $parent_id ?? 0 ?>" class="btn-icon" title="<?= $page['publish'] == 1 ? 'Снять с публикации' : 'Опубликовать' ?>">
                                    <?php if ($page['publish'] == 1): ?>
                                        <span class="icon-eye">👁️</span>
                                    <?php else: ?>
                                        <span class="icon-eye-off">👁️‍🗨️</span>
                                    <?php endif; ?>
                                </a>
                                <a href="/admin-panel/pages/edit/<?= $page['id'] ?>" class="btn-icon" title="Редактировать">
                                    <span class="icon-edit">✏️</span>
                                </a>
                                <a href="/admin-panel/pages/delete/<?= $page['id'] ?>?parent=<?= $parent_id ?? 0 ?>" class="btn-icon" title="Удалить" onclick="return confirm('Удалить страницу «<?= esc($page['name']) ?>»?')">
                                    <span class="icon-delete">🗑️</span>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center">Страницы не найдены</td>
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
                    <button type="button" class="btn-apply" onclick="confirmBulkAction()">Применить</button>
                </div>

                <?php if (isset($pager) && $pager->getPageCount() > 1): ?>
                    <div class="pagination">
                        <?= $pager->links() ?>
                    </div>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <script>
        function toggleAll(source) {
            var checkboxes = document.querySelectorAll('input[name="selected_ids[]"]');
            for (var i = 0; i < checkboxes.length; i++) {
                checkboxes[i].checked = source.checked;
            }
        }

        function confirmBulkAction() {
            var action = document.querySelector('select[name="bulk_action"]').value;
            if (action === '') {
                alert('Пожалуйста, выберите действие');
                return;
            }

            var checkboxes = document.querySelectorAll('input[name="selected_ids[]"]:checked');
            if (checkboxes.length === 0) {
                alert('Пожалуйста, выберите хотя бы одну страницу');
                return;
            }

            var message = '';
            if (action === 'delete') {
                message = 'Вы действительно хотите удалить выбранные страницы?';
            } else if (action === 'publish') {
                message = 'Опубликовать выбранные страницы?';
            } else if (action === 'unpublish') {
                message = 'Снять с публикации выбранные страницы?';
            }

            if (confirm(message)) {
                document.getElementById('bulkForm').submit();
            }
        }
    </script>

<?= $this->endSection() ?>