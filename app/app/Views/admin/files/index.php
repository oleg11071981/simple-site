<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>

    <div class="page-header">
        <div class="header-actions">
            <h1>Файловый менеджер</h1>
            <a href="/admin-panel/files/upload" class="btn-create">📤 Загрузить файл</a>
        </div>
        <p>Управление файлами и изображениями сайта</p>
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
        <form action="/admin-panel/files" method="get" class="filters-form">
            <div class="filter-group">
                <label>Показывать:</label>
                <select name="show" class="filter-select" onchange="this.form.submit()">
                    <option value="1" <?= ($show ?? 1) == 1 ? 'selected' : '' ?>>Все файлы</option>
                    <option value="2" <?= ($show ?? 1) == 2 ? 'selected' : '' ?>>Изображения</option>
                    <option value="3" <?= ($show ?? 1) == 3 ? 'selected' : '' ?>>Документы</option>
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

    <div class="table-container">
        <form action="/admin-panel/files/bulk-action" method="post" id="bulkForm">
            <?= csrf_field() ?>

            <table class="data-table">
                <thead>
                <tr>
                    <th style="width: 30px">
                        <input type="checkbox" id="selectAll" onclick="toggleAll(this)">
                    </th>
                    <th style="width: 80px">Превью</th>
                    <th style="width: 60px">ID</th>
                    <th>Название</th>
                    <th style="width: 100px">Тип</th>
                    <th style="width: 100px">Категория</th>
                    <th style="width: 80px">Размер</th>
                    <th style="width: 140px">Дата создания</th>
                    <th style="width: 100px">Действия</th>
                </tr>
                </thead>
                <tbody>
                <?php if (!empty($files) && is_array($files)): ?>
                    <?php foreach ($files as $file): ?>
                        <tr class="file-row">
                            <td class="text-center">
                                <input type="checkbox" name="selected_ids[]" value="<?= $file['id'] ?>">
                            </td>
                            <td class="text-center">
                                <?php if (in_array($file['file_type'], ['jpg', 'jpeg', 'png', 'gif', 'webp'])): ?>
                                    <img src="/uploads/<?= $file['file_name'] ?>" width="50" height="50" style="object-fit: cover; border-radius: 4px;">
                                <?php else: ?>
                                    <span style="font-size: 30px;"><?= $file['icon'] ?></span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center"><?= esc($file['id']) ?></td>
                            <td>
                                <div class="file-name">
                                    <a href="/admin-panel/files/edit/<?= $file['id'] ?>" class="file-link">
                                        <?= esc($file['name']) ?>
                                    </a>
                                </div>
                                <div class="file-original-name">
                                    <small><?= esc($file['file_name']) ?></small>
                                </div>
                            </td>
                            <td>
                                <span class="file-type-badge"><?= strtoupper(esc($file['file_type'])) ?></span>
                            </td>
                            <td class="text-center">
                                <?php if ($file['category'] > 0): ?>
                                    <a href="/admin-panel/files?category=<?= $file['category'] ?>" class="category-link">
                                        📁 <?= esc($file['category_name']) ?>
                                    </a>
                                <?php else: ?>
                                    <span class="no-category">—</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center"><?= $file['size_formatted'] ?></td>
                            <td class="date-cell"><?= date('d.m.Y H:i', strtotime($file['create'])) ?></td>
                            <td class="actions">
                                <a href="/admin-panel/files/edit/<?= $file['id'] ?>" class="btn-icon" title="Редактировать">
                                    <span class="icon-edit">✏️</span>
                                </a>
                                <a href="/admin-panel/files/delete/<?= $file['id'] ?>" class="btn-icon" title="Удалить" onclick="return confirm('Удалить файл «<?= esc($file['name']) ?>»?')">
                                    <span class="icon-delete">🗑️</span>
                                </a>
                            <td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr class="file-row">
                        <td colspan="9" class="text-center">Файлы не найдены</td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>

            <div class="table-actions">
                <div class="bulk-actions">
                    <span>С отмеченными:</span>
                    <select name="bulk_action" class="bulk-select">
                        <option value="">Выберите действие</option>
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
                alert('Пожалуйста, выберите хотя бы один файл');
                return;
            }

            if (action === 'delete' && confirm('Вы действительно хотите удалить выбранные файлы?')) {
                document.getElementById('bulkForm').submit();
            }
        }
    </script>

<?= $this->endSection() ?>