<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>

    <div class="form-container">
        <div class="page-header">
            <h1><?= isset($project) ? 'Редактирование проекта' : 'Создание проекта' ?></h1>
            <p><?= isset($project) ? 'Редактирование «' . esc($project['name']) . '»' : 'Добавление нового проекта' ?></p>
        </div>

        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success" id="successAlert">
                <span class="alert-icon">✓</span>
                <span class="alert-message"><?= esc(session()->getFlashdata('success')) ?></span>
                <button class="alert-close" onclick="this.parentElement.style.display='none'">×</button>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('errors')): ?>
            <div class="alert alert-error">
                <?php foreach (session()->getFlashdata('errors') as $error): ?>
                    <div>⚠ <?= esc($error) ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-error">
                <span class="alert-icon">⚠</span>
                <span class="alert-message"><?= esc(session()->getFlashdata('error')) ?></span>
                <button class="alert-close" onclick="this.parentElement.style.display='none'">×</button>
            </div>
        <?php endif; ?>

        <!-- Вкладки -->
        <div class="tabs">
            <button type="button" class="tab-btn active" data-tab="main">📋 Основное</button>
            <button type="button" class="tab-btn" data-tab="events">📅 Мероприятия</button>
        </div>

        <!-- Форма проекта -->
        <form action="<?= isset($project) ? '/admin-panel/projects/update/' . $project['id'] : '/admin-panel/projects/store' ?>" method="post" class="settings-form" id="projectForm">
            <?= csrf_field() ?>

            <?php if (isset($project)): ?>
                <input type="hidden" name="id" value="<?= $project['id'] ?>">
            <?php endif; ?>

            <!-- Вкладка: Основное -->
            <div id="tab-main" class="tab-content active">
                <div class="settings-section">
                    <h2>Основная информация</h2>

                    <?php if (isset($project)): ?>
                        <div class="form-group">
                            <label>ID проекта</label>
                            <div class="form-control-static"><?= $project['id'] ?></div>
                        </div>
                    <?php endif; ?>

                    <div class="form-group">
                        <label for="name">Название проекта <span class="required">*</span></label>
                        <input type="text" id="name" name="name"
                               value="<?= esc($project['name'] ?? '') ?>"
                               class="form-control"
                               placeholder="Введите название проекта"
                               required autofocus>
                    </div>

                    <div class="form-group">
                        <label for="path">URL-путь</label>
                        <input type="text" id="path" name="path"
                               value="<?= esc($project['path'] ?? '') ?>"
                               class="form-control"
                               placeholder="avto-iz-germanii">
                        <small>
                            <a href="#" onclick="rusToTranslit('path', document.getElementById('name')); return false;">Сформировать из названия</a>
                        </small>
                    </div>

                    <div class="form-group">
                        <label for="anons_text">Краткое описание</label>
                        <textarea id="anons_text" name="anons_text" rows="4"
                                  class="form-control"
                                  placeholder="Краткое описание проекта"><?= esc($project['anons_text'] ?? '') ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="organizing_committee">Оргкомитет</label>
                        <textarea id="organizing_committee" name="organizing_committee" rows="6"
                                  class="form-control"
                                  placeholder="Состав организационного комитета"><?= esc($project['organizing_committee'] ?? '') ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="supported_by">Проводится при поддержке</label>
                        <textarea id="supported_by" name="supported_by" rows="4"
                                  class="form-control"
                                  placeholder="Партнёры и спонсоры"><?= esc($project['supported_by'] ?? '') ?></textarea>
                    </div>

                    <div class="form-row">
                        <div class="form-group half">
                            <label for="date_start">Дата начала проекта</label>
                            <input type="date" id="date_start" name="date_start"
                                   value="<?= esc($project['date_start'] ?? '') ?>"
                                   class="form-control">
                        </div>
                        <div class="form-group half">
                            <label for="date_end">Дата окончания проекта</label>
                            <input type="date" id="date_end" name="date_end"
                                   value="<?= esc($project['date_end'] ?? '') ?>"
                                   class="form-control">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="foto">Главное изображение</label>
                        <div class="foto-preview" id="fotoPreview">
                            <?php if (isset($project) && $project['foto'] > 0 && !empty($project['foto_file'])): ?>
                                <img src="/uploads/<?= $project['foto_file'] ?>" style="max-width: 200px; border-radius: 8px;">
                            <?php else: ?>
                                <div class="foto-placeholder" style="width: 200px; height: 150px; background: #f8f9fa; display: flex; align-items: center; justify-content: center; border-radius: 8px; border: 1px dashed #dee2e6;">
                                    <span style="color: #6c757d;">Нет изображения</span>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="foto-actions" style="margin-top: 10px;">
                            <input type="hidden" name="foto" id="foto" value="<?= esc($project['foto'] ?? 0) ?>">
                            <input type="hidden" name="foto_file" id="foto_file" value="<?= esc($project['foto_file'] ?? '') ?>">
                            <button type="button" class="btn-select-foto" onclick="openFileManager('foto')" style="background: #007bff; color: white; padding: 6px 12px; border: none; border-radius: 4px; cursor: pointer;">📁 Выбрать изображение</button>
                            <button type="button" class="btn-clear-foto" onclick="clearFoto()" style="background: #dc3545; color: white; padding: 6px 12px; border: none; border-radius: 4px; cursor: pointer; margin-left: 8px;">🗑️ Удалить</button>
                        </div>
                        <small>Рекомендуемый размер: 1200x800px</small>
                    </div>

                    <div class="form-group">
                        <label for="media">Галерея</label>
                        <div class="media-select-wrapper">
                            <input type="text"
                                   id="mediaSearch"
                                   class="form-control"
                                   placeholder="🔍 Поиск галереи..."
                                   autocomplete="off">
                            <select id="media" name="media" class="form-control" size="6" style="margin-top: 8px;">
                                <option value="0">— Без галереи —</option>
                                <?php if (!empty($mediaCategories)): ?>
                                    <?php foreach ($mediaCategories as $cat): ?>
                                        <option value="<?= $cat['id'] ?>"
                                                data-name="<?= esc(strtolower($cat['name'])) ?>"
                                            <?= (isset($project) && ($project['media'] ?? 0) == $cat['id']) ? 'selected' : '' ?>>
                                            <?= str_repeat('—', $cat['level'] ?? 0) ?> 📁 <?= esc($cat['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        <small>Выберите галерею для отображения на странице проекта</small>
                    </div>
                </div>

                <div class="settings-section">
                    <h2>Настройки отображения</h2>

                    <div class="form-row">
                        <div class="form-group half">
                            <label for="priority">Приоритет (порядок сортировки)</label>
                            <input type="number" id="priority" name="priority"
                                   value="<?= esc($project['priority'] ?? 0) ?>"
                                   class="form-control">
                            <small>Чем меньше число, тем выше в списке</small>
                        </div>
                        <div class="form-group half">
                            <label for="publish">Статус</label>
                            <select id="publish" name="publish" class="form-control">
                                <option value="0" <?= (isset($project) && $project['publish'] == 0) ? 'selected' : '' ?>>Черновик</option>
                                <option value="1" <?= (isset($project) && $project['publish'] == 1) ? 'selected' : '' ?>>Опубликовано</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="settings-section">
                    <h2>SEO настройки</h2>

                    <div class="form-group">
                        <label for="keywords">Ключевые слова</label>
                        <textarea id="keywords" name="keywords" rows="3"
                                  class="form-control"
                                  placeholder="Ключевые слова через запятую"><?= esc($project['keywords'] ?? '') ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="description">Мета-описание</label>
                        <textarea id="description" name="description" rows="4"
                                  class="form-control"
                                  placeholder="Краткое описание для поисковых систем"><?= esc($project['description'] ?? '') ?></textarea>
                        <small>Рекомендуемая длина: 150-160 символов</small>
                    </div>
                </div>
            </div>

            <!-- Вкладка: Мероприятия -->
            <div id="tab-events" class="tab-content">
                <div class="settings-section">
                    <div class="section-header">
                        <h2>Мероприятия проекта</h2>
                        <?php if (isset($project)): ?>
                            <a href="/admin-panel/events/create?project_id=<?= $project['id'] ?>" class="btn-create" style="padding: 6px 12px; font-size: 13px;">➕ Добавить мероприятие</a>
                        <?php endif; ?>
                    </div>

                    <?php if (isset($project) && !empty($events)): ?>
                        <div class="table-container">
                            <table class="data-table">
                                <thead>
                                <tr>
                                    <th style="width: 60px">ID</th>
                                    <th>Название мероприятия</th>
                                    <th style="width: 110px">Дата начала</th>
                                    <th style="width: 110px">Дата окончания</th>
                                    <th style="width: 100px">Статус</th>
                                    <th style="width: 100px">Действия</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($events as $event): ?>
                                    <tr>
                                        <td class="text-center"><?= esc($event['id']) ?></td>
                                        <td>
                                            <strong><?= esc($event['name']) ?></strong>
                                            <?php if (!empty($event['location'])): ?>
                                                <div class="event-location" style="font-size: 12px; color: #6c757d; margin-top: 4px;">
                                                    📍 <?= esc($event['location']) ?>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td class="date-cell"><?= $event['date_start'] ? date('d.m.Y', strtotime($event['date_start'])) : '—' ?></td>
                                        <td class="date-cell"><?= $event['date_end'] ? date('d.m.Y', strtotime($event['date_end'])) : '—' ?></td>
                                        <td class="text-center">
                                            <?php if ($event['publish'] == 1): ?>
                                                <span class="badge badge-success">Опубл.</span>
                                            <?php else: ?>
                                                <span class="badge badge-warning">Черновик</span>
                                            <?php endif; ?>
                                        </td>
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
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info" style="text-align: center; padding: 40px;">
                            <span style="font-size: 48px;">📅</span>
                            <p style="margin-top: 12px;">Мероприятия не добавлены</p>
                            <?php if (isset($project)): ?>
                                <a href="/admin-panel/events/create?project_id=<?= $project['id'] ?>" class="btn-create" style="margin-top: 12px; display: inline-block;">➕ Добавить мероприятие</a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <?php if (isset($project)): ?>
                <div class="settings-section">
                    <div class="form-group">
                        <label>📅 Время создания</label>
                        <div class="form-control-static"><?= date('d.m.Y H:i:s', strtotime($project['create'])) ?></div>
                    </div>
                    <div class="form-group">
                        <label>🔄 Время изменения</label>
                        <div class="form-control-static"><?= date('d.m.Y H:i:s', strtotime($project['modify'])) ?></div>
                    </div>
                </div>
            <?php endif; ?>

            <div class="form-actions">
                <a href="/admin-panel/projects" class="btn-cancel">Отмена</a>
                <button type="submit" class="btn-save">💾 Сохранить проект</button>
            </div>
        </form>
    </div>

    <!-- Скрипты для вкладок и выбора изображений -->
    <script>
        // Переключение вкладок
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const tabId = this.dataset.tab;

                // Убираем активные классы у всех кнопок и вкладок
                document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
                document.querySelectorAll('.tab-content').forEach(t => t.classList.remove('active'));

                // Добавляем активные классы
                this.classList.add('active');
                document.getElementById(`tab-${tabId}`).classList.add('active');
            });
        });

        // Выбор изображения из файлового менеджера
        function openFileManager(fieldName) {
            var url = '/admin-panel/editor/ckeditor-browse?type=image&field=' + fieldName;
            window.open(url, 'FileManager', 'width=1200,height=700,left=100,top=50,toolbar=no,scrollbars=yes,resizable=yes');
        }

        function clearFoto() {
            document.getElementById('foto').value = 0;
            document.getElementById('foto_file').value = '';
            document.getElementById('fotoPreview').innerHTML = '<div class="foto-placeholder" style="width: 200px; height: 150px; background: #f8f9fa; display: flex; align-items: center; justify-content: center; border-radius: 8px; border: 1px dashed #dee2e6;"><span style="color: #6c757d;">Нет изображения</span></div>';
        }

        function setSelectedFile(fileId, fileName, fileUrl) {
            document.getElementById('foto').value = fileId;
            document.getElementById('foto_file').value = fileName;
            document.getElementById('fotoPreview').innerHTML = '<img src="' + fileUrl + '" style="max-width: 200px; border-radius: 8px;">';
        }

        // Поиск по категориям галереи
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('mediaSearch');
            const selectEl = document.getElementById('media');

            if (searchInput && selectEl) {
                function filterCategories() {
                    const searchTerm = searchInput.value.toLowerCase().trim();
                    const options = selectEl.querySelectorAll('option');

                    options.forEach(option => {
                        const text = option.textContent.toLowerCase();
                        const categoryName = option.getAttribute('data-name') || text;

                        if (searchTerm === '') {
                            option.style.display = '';
                        } else if (categoryName.includes(searchTerm) || text.includes(searchTerm)) {
                            option.style.display = '';
                        } else {
                            option.style.display = 'none';
                        }
                    });
                }

                searchInput.addEventListener('input', filterCategories);
            }
        });
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

        .alert-info {
            background: #d1ecf1;
            border-color: #bee5eb;
            color: #0c5460;
        }
    </style>

<?= $this->endSection() ?>