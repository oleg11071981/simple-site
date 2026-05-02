<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>

    <div class="page-header">
        <div class="header-actions">
            <h1>Управление новостями</h1>
            <a href="/admin-panel/news/create" class="btn-create">➕ Добавить новость</a>
        </div>
        <p>Управление новостями и публикациями сайта</p>
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

    <div class="filters-bar">
        <form action="/admin-panel/news" method="get" class="filters-form">
            <div class="filter-group">
                <label>Фильтр:</label>
                <select name="show" class="filter-select" onchange="this.form.submit()">
                    <option value="1" <?= ($show ?? 1) == 1 ? 'selected' : '' ?>>Все новости</option>
                    <option value="2" <?= ($show ?? 1) == 2 ? 'selected' : '' ?>>Опубликованные</option>
                    <option value="3" <?= ($show ?? 1) == 3 ? 'selected' : '' ?>>Черновики</option>
                </select>
            </div>

            <div class="filter-group">
                <label>Сортировка:</label>
                <select name="sort" class="filter-select" onchange="this.form.submit()">
                    <option value="1" <?= ($sort ?? 2) == 1 ? 'selected' : '' ?>>ID (возрастание)</option>
                    <option value="2" <?= ($sort ?? 2) == 2 ? 'selected' : '' ?>>ID (убывание)</option>
                    <option value="3" <?= ($sort ?? 2) == 3 ? 'selected' : '' ?>>Заголовку (А-Я)</option>
                    <option value="4" <?= ($sort ?? 2) == 4 ? 'selected' : '' ?>>Заголовку (Я-А)</option>
                    <option value="5" <?= ($sort ?? 2) == 5 ? 'selected' : '' ?>>Дате (старые)</option>
                    <option value="6" <?= ($sort ?? 2) == 6 ? 'selected' : '' ?>>Дате (новые)</option>
                </select>
            </div>

            <div class="filter-group">
                <label>Поиск:</label>
                <input type="text" name="search" class="filter-input" value="<?= esc($search ?? '') ?>" placeholder="Название...">
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
            <button type="submit" class="btn-apply">Применить</button>
        </form>
    </div>

    <div class="table-container">
        <form action="/admin-panel/news/bulk-action" method="post" id="bulkForm">
            <?= csrf_field() ?>

            <table class="data-table">
                <thead>
                <tr>
                    <th style="width: 30px">
                        <input type="checkbox" id="selectAll" onclick="toggleAll(this)">
                    </th>
                    <th style="width: 60px">ID</th>
                    <th>Заголовок</th>
                    <th style="width: 100px">Статус</th>
                    <th style="width: 110px">Дата</th>
                    <th style="width: 140px">Дата создания</th>
                    <th style="width: 100px">Действия</th>
                </tr>
                </thead>
                <tbody>
                <?php if (!empty($news) && is_array($news)): ?>
                    <?php foreach ($news as $item): ?>
                        <tr>
                            <td class="text-center">
                                <input type="checkbox" name="selected_ids[]" value="<?= $item['id'] ?>">
                            </td>
                            <td class="text-center"><?= esc($item['id']) ?></td>
                            <td>
                                <div class="news-name">
                                    <span class="news-icon">📰</span>
                                    <a href="/admin-panel/news/edit/<?= $item['id'] ?>" class="news-link">
                                        <?= esc($item['name']) ?>
                                    </a>
                                </div>
                            </td>
                            <td>
                                <?php if ($item['publish'] == 1): ?>
                                    <span class="badge badge-success">Опубликовано</span>
                                <?php else: ?>
                                    <span class="badge badge-warning">Черновик</span>
                                <?php endif; ?>
                            </td>
                            <td class="date-cell"><?= date('d.m.Y', strtotime($item['date'])) ?></td>
                            <td class="date-cell"><?= date('d.m.Y H:i', strtotime($item['create'])) ?></td>
                            <td class="actions">
                                <a href="/admin-panel/news/toggle/<?= $item['id'] ?>" class="btn-icon" title="<?= $item['publish'] == 1 ? 'Снять с публикации' : 'Опубликовать' ?>">
                                    <?php if ($item['publish'] == 1): ?>
                                        <span class="icon-eye">👁️</span>
                                    <?php else: ?>
                                        <span class="icon-eye-off">👁️‍🗨️</span>
                                    <?php endif; ?>
                                </a>
                                <a href="/admin-panel/news/edit/<?= $item['id'] ?>" class="btn-icon" title="Редактировать">
                                    <span class="icon-edit">✏️</span>
                                </a>
                                <a href="/admin-panel/news/delete/<?= $item['id'] ?>" class="btn-icon" title="Удалить" onclick="return confirm('Удалить новость «<?= esc($item['name']) ?>»?')">
                                    <span class="icon-delete">🗑️</span>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center">Новости не найдены</td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>

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

    <script>
        function toggleAll(source) {
            var checkboxes = document.querySelectorAll('input[name="selected_ids[]"]');
            for (var i = 0; i < checkboxes.length; i++) {
                checkboxes[i].checked = source.checked;
            }
        }

        function confirmBulkAction(formId) {
            var action = document.querySelector('select[name="bulk_action"]').value;
            if (action === '') {
                alert('Пожалуйста, выберите действие');
                return;
            }

            var checkboxes = document.querySelectorAll('input[name="selected_ids[]"]:checked');
            if (checkboxes.length === 0) {
                alert('Пожалуйста, выберите хотя бы одну новость');
                return;
            }

            var message = '';
            if (action === 'delete') {
                message = 'Вы действительно хотите удалить выбранные новости?';
            } else if (action === 'publish') {
                message = 'Опубликовать выбранные новости?';
            } else if (action === 'unpublish') {
                message = 'Снять с публикации выбранные новости?';
            }

            if (confirm(message)) {
                document.getElementById(formId).submit();
            }
        }
    </script>

<?= $this->endSection() ?>