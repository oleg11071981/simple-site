<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>

    <div class="form-container">
        <div class="page-header">
            <h1><?= isset($category) ? 'Редактирование категории' : 'Создание категории' ?></h1>
            <p><?= isset($category) ? 'Редактирование «' . esc($category['name']) . '»' : 'Добавление новой категории для файлов' ?></p>
        </div>

        <?php if (session()->getFlashdata('errors')): ?>
            <div class="alert alert-error">
                <?php foreach (session()->getFlashdata('errors') as $error): ?>
                    <div>⚠ <?= esc($error) ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

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

        <form action="<?= isset($category) ? '/admin-panel/categories/update/' . $category['id'] : '/admin-panel/categories/store' ?>" method="post" class="settings-form" id="categoryForm">
            <?= csrf_field() ?>

            <?php if (isset($category)): ?>
                <input type="hidden" name="id" value="<?= $category['id'] ?>">
            <?php endif; ?>

            <!-- Для РЕДАКТИРОВАНИЯ: показываем вкладки -->
            <?php if (isset($category)): ?>
                <div class="tabs">
                    <button type="button" class="tab-btn active" data-tab="main">📋 Основное</button>
                    <button type="button" class="tab-btn" data-tab="files">📁 Файлы (<?= count($files ?? []) ?>)</button>
                </div>
            <?php endif; ?>

            <!-- Основное содержимое (поля формы) -->
            <div id="main-content" <?= isset($category) ? 'class="tab-content active"' : '' ?>>
                <div class="settings-section">
                    <h2>Основная информация</h2>

                    <?php if (isset($category)): ?>
                        <div class="form-group">
                            <label>ID категории</label>
                            <div class="form-control-static"><?= $category['id'] ?></div>
                        </div>
                    <?php endif; ?>

                    <div class="form-group">
                        <label for="name">Название категории <span class="required">*</span></label>
                        <input type="text" id="name" name="name"
                               value="<?= esc($category['name'] ?? '') ?>"
                               class="form-control"
                               placeholder="Введите название категории"
                               required autofocus>
                        <small>Например: Изображения, Документы, Баннеры и т.д.</small>
                    </div>

                    <div class="form-group">
                        <label for="parent">Родительская категория</label>
                        <select id="parent" name="parent" class="form-control">
                            <option value="0">— Корневая категория —</option>
                            <?php if (!empty($categories)): ?>
                                <?php foreach ($categories as $cat): ?>
                                    <?php if (isset($category) && $category['id'] == $cat['id']) continue; ?>
                                    <option value="<?= $cat['id'] ?>"
                                        <?= (isset($category) && $category['parent'] == $cat['id']) ? 'selected' : '' ?>
                                        <?= (isset($parent_id) && $parent_id == $cat['id'] && !isset($category)) ? 'selected' : '' ?>>
                                        <?= str_repeat('—', $cat['level'] ?? 0) ?> <?= esc($cat['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                        <small>Выберите родительскую категорию для создания вложенности</small>
                    </div>

                    <div class="form-group">
                        <label for="priority">Приоритет (порядок сортировки)</label>
                        <input type="number" id="priority" name="priority"
                               value="<?= esc($category['priority'] ?? 0) ?>"
                               class="form-control">
                        <small>Чем меньше число, тем выше в списке</small>
                    </div>

                    <div class="form-group">
                        <label for="description">Описание категории</label>
                        <textarea id="description" name="description" rows="4"
                                  class="form-control"
                                  placeholder="Описание категории (необязательно)"><?= esc($category['description'] ?? '') ?></textarea>
                    </div>
                </div>

                <?php if (isset($category)): ?>
                    <div class="settings-section">
                        <div class="form-group">
                            <label>📅 Время создания</label>
                            <div class="form-control-static"><?= date('d.m.Y H:i:s', strtotime($category['create'])) ?></div>
                        </div>
                        <div class="form-group">
                            <label>🔄 Время изменения</label>
                            <div class="form-control-static"><?= date('d.m.Y H:i:s', strtotime($category['modify'])) ?></div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Вкладка: Файлы (только для существующей категории) -->
            <?php if (isset($category)): ?>
                <div id="tab-files" class="tab-content">
                    <div class="settings-section">
                        <div class="section-header">
                            <h2>Файлы в категории «<?= esc($category['name']) ?>»</h2>
                            <a href="/admin-panel/files?category=<?= $category['id'] ?>" class="btn-create" style="padding: 6px 12px; font-size: 13px;">
                                📁 Перейти в файловый менеджер
                            </a>
                        </div>

                        <?php if (!empty($files)): ?>
                            <div class="table-container">
                                <table class="data-table">
                                    <thead>
                                    <tr>
                                        <th style="width: 60px">ID</th>
                                        <th style="width: 80px">Превью</th>
                                        <th>Название файла</th>
                                        <th style="width: 100px">Тип</th>
                                        <th style="width: 100px">Размер</th>
                                        <th style="width: 100px">Действия</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach ($files as $file): ?>
                                        <tr>
                                            <td class="text-center"><?= esc($file['id']) ?></td>
                                            <td class="text-center">
                                                <?php if (in_array($file['file_type'], ['jpg', 'jpeg', 'png', 'gif', 'webp'])): ?>
                                                    <img src="/uploads/<?= esc($file['file_name']) ?>" style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;">
                                                <?php else: ?>
                                                    <span style="font-size: 30px;"><?= $file['icon'] ?></span>
                                                <?php endif; ?>
                                            </td>
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
                                            <td><span class="file-type-badge"><?= strtoupper(esc($file['file_type'])) ?></span></td>
                                            <td><?= $file['size_formatted'] ?></td>
                                            <td class="actions">
                                                <a href="/admin-panel/files/edit/<?= $file['id'] ?>" class="btn-icon" title="Редактировать">
                                                    <span class="icon-edit">✏️</span>
                                                </a>
                                                <a href="/admin-panel/files/delete/<?= $file['id'] ?>" class="btn-icon" title="Удалить" onclick="return confirm('Удалить файл «<?= esc($file['name']) ?>»?')">
                                                    <span class="icon-delete">🗑️</span>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info" style="text-align: center; padding: 40px;">
                                <span style="font-size: 48px;">📁</span>
                                <p style="margin-top: 12px;">В этой категории пока нет файлов</p>
                                <a href="/admin-panel/files/upload?category=<?= $category['id'] ?>" class="btn-create" style="margin-top: 12px; display: inline-block;">
                                    ➕ Загрузить файл
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>

            <div class="form-actions">
                <a href="/admin-panel/categories<?= (isset($parent_id) && $parent_id > 0) ? '?parent=' . $parent_id : '' ?>" class="btn-cancel">Отмена</a>
                <button type="submit" class="btn-save">💾 <?= isset($category) ? 'Сохранить' : 'Создать' ?></button>
            </div>
        </form>
    </div>

    <script>
        // Переключение вкладок (только если есть категория)
        <?php if (isset($category)): ?>
        // Функция переключения вкладок
        function switchTab(tabId) {
            // Убираем активный класс у всех кнопок
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.classList.remove('active');
            });

            // Убираем активный класс у всех вкладок
            document.querySelectorAll('.tab-content').forEach(tab => {
                tab.classList.remove('active');
            });

            // Добавляем активный класс выбранной кнопке
            const activeBtn = document.querySelector(`.tab-btn[data-tab="${tabId}"]`);
            if (activeBtn) activeBtn.classList.add('active');

            // Показываем выбранную вкладку
            if (tabId === 'main') {
                document.getElementById('main-content').classList.add('active');
            } else if (tabId === 'files') {
                document.getElementById('tab-files').classList.add('active');
            }
        }

        // Навешиваем обработчики на кнопки
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                switchTab(this.dataset.tab);
            });
        });

        // При загрузке активируем вкладку "Основное"
        switchTab('main');
        <?php endif; ?>
    </script>

    <style>
        .tabs {
            display: flex;
            gap: 0;
            margin-bottom: 0;
            border-bottom: 1px solid #dee2e6;
            background: white;
            border-radius: 12px 12px 0 0;
            overflow: hidden;
        }

        .tab-btn {
            padding: 12px 24px;
            background: #f8f9fa;
            border: none;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.2s;
            color: #6c757d;
        }

        .tab-btn:hover {
            background: #e9ecef;
        }

        .tab-btn.active {
            background: white;
            color: #007bff;
            border-bottom: 2px solid #007bff;
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .section-header h2 {
            margin: 0;
        }

        .file-name {
            margin-bottom: 4px;
        }

        .file-link {
            color: #2c3e50;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.2s;
        }

        .file-link:hover {
            color: #007bff;
            text-decoration: underline;
        }

        .file-original-name small {
            font-size: 11px;
            color: #6c757d;
            font-family: monospace;
        }

        .file-type-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 600;
            background: #e9ecef;
            color: #495057;
        }

        .alert-info {
            background: #d1ecf1;
            border-color: #bee5eb;
            color: #0c5460;
        }
    </style>

<?= $this->endSection() ?>