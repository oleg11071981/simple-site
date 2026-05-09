<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>

    <div class="form-container">
        <div class="page-header">
            <h1><?= isset($event) ? 'Редактирование мероприятия' : 'Создание мероприятия' ?></h1>
            <p>
                <?php if (isset($event)): ?>
                    Редактирование «<?= esc($event['name']) ?>»
                <?php else: ?>
                    Добавление мероприятия в проект: <strong><?= esc($project['name']) ?></strong>
                <?php endif; ?>
            </p>
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

        <form action="<?= isset($event) ? '/admin-panel/events/update/' . $event['id'] : '/admin-panel/events/store' ?>" method="post" class="settings-form">
            <?= csrf_field() ?>

            <?php if (isset($event)): ?>
                <input type="hidden" name="id" value="<?= $event['id'] ?>">
            <?php endif; ?>

            <input type="hidden" name="project_id" value="<?= $project['id'] ?>">

            <div class="settings-section">
                <h2>Основная информация</h2>

                <?php if (isset($event)): ?>
                    <div class="form-group">
                        <label>ID мероприятия</label>
                        <div class="form-control-static"><?= $event['id'] ?></div>
                    </div>

                    <div class="form-group">
                        <label>Проект</label>
                        <div class="form-control-static">
                            <a href="/admin-panel/projects/edit/<?= $project['id'] ?>"><?= esc($project['name']) ?></a>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="form-group">
                    <label for="name">Название мероприятия <span class="required">*</span></label>
                    <input type="text" id="name" name="name"
                           value="<?= esc($event['name'] ?? '') ?>"
                           class="form-control"
                           placeholder="Введите название мероприятия"
                           required autofocus>
                </div>

                <div class="form-group">
                    <label for="path">URL-путь</label>
                    <input type="text" id="path" name="path"
                           value="<?= esc($event['path'] ?? '') ?>"
                           class="form-control"
                           placeholder="nazvanie-meropriyatiya">
                    <small>
                        <a href="#" onclick="rusToTranslit('path', document.getElementById('name')); return false;">Сформировать из названия</a>
                    </small>
                </div>

                <div class="form-group">
                    <label for="anons_text">Краткое описание</label>
                    <textarea id="anons_text" name="anons_text" rows="3"
                              class="form-control"
                              placeholder="Краткое описание мероприятия"><?= esc($event['anons_text'] ?? '') ?></textarea>
                </div>

                <div class="form-group">
                    <label for="more_info">Полное описание</label>
                    <textarea id="more_info" name="more_info" rows="15"
                              class="form-control"
                              placeholder="Подробное описание мероприятия (HTML)"><?= htmlspecialchars($event['more_info'] ?? '') ?></textarea>
                    <small>Поддерживается HTML разметка. Используйте визуальный редактор.</small>
                </div>
            </div>

            <div class="settings-section">
                <h2>Изображения</h2>

                <div class="form-group">
                    <label for="foto">Главное изображение</label>
                    <div class="foto-preview" id="fotoPreview">
                        <?php if (isset($event) && $event['foto'] > 0 && !empty($event['foto_file'])): ?>
                            <img src="/uploads/<?= $event['foto_file'] ?>" style="max-width: 200px; border-radius: 8px;">
                        <?php else: ?>
                            <div class="foto-placeholder" style="width: 200px; height: 150px; background: #f8f9fa; display: flex; align-items: center; justify-content: center; border-radius: 8px; border: 1px dashed #dee2e6;">
                                <span style="color: #6c757d;">Нет изображения</span>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="foto-actions" style="margin-top: 10px;">
                        <input type="hidden" name="foto" id="foto" value="<?= esc($event['foto'] ?? 0) ?>">
                        <input type="hidden" name="foto_file" id="foto_file" value="<?= esc($event['foto_file'] ?? '') ?>">
                        <button type="button" class="btn-select-foto" onclick="openFileManager('foto')" style="background: #007bff; color: white; padding: 6px 12px; border: none; border-radius: 4px; cursor: pointer;">📁 Выбрать изображение</button>
                        <button type="button" class="btn-clear-foto" onclick="clearFoto()" style="background: #dc3545; color: white; padding: 6px 12px; border: none; border-radius: 4px; cursor: pointer; margin-left: 8px;">🗑️ Удалить</button>
                    </div>
                    <small>Рекомендуемый размер: 800x600px</small>
                </div>

                <div class="form-group">
                    <label for="media">Галерея мероприятия</label>
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
                                        <?= (isset($event) && ($event['media'] ?? 0) == $cat['id']) ? 'selected' : '' ?>>
                                        <?= str_repeat('—', $cat['level'] ?? 0) ?> 📁 <?= esc($cat['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    <small>Выберите галерею для отображения на странице мероприятия</small>
                </div>
            </div>

            <div class="settings-section">
                <h2>Дата и место</h2>

                <div class="form-row">
                    <div class="form-group half">
                        <label for="date_start">Дата начала</label>
                        <input type="date" id="date_start" name="date_start"
                               value="<?= esc($event['date_start'] ?? '') ?>"
                               class="form-control">
                    </div>
                    <div class="form-group half">
                        <label for="date_end">Дата окончания</label>
                        <input type="date" id="date_end" name="date_end"
                               value="<?= esc($event['date_end'] ?? '') ?>"
                               class="form-control">
                    </div>
                </div>

                <div class="form-group">
                    <label for="location">Место проведения</label>
                    <input type="text" id="location" name="location"
                           value="<?= esc($event['location'] ?? '') ?>"
                           class="form-control"
                           placeholder="Москва, Кремль">
                </div>

                <div class="form-group">
                    <label for="link">Внешняя ссылка</label>
                    <input type="url" id="link" name="link"
                           value="<?= esc($event['link'] ?? '') ?>"
                           class="form-control"
                           placeholder="https://example.com/event">
                    <small>Ссылка на страницу мероприятия (если есть)</small>
                </div>
            </div>

            <div class="settings-section">
                <h2>Настройки отображения</h2>

                <div class="form-row">
                    <div class="form-group half">
                        <label for="priority">Приоритет (порядок сортировки)</label>
                        <input type="number" id="priority" name="priority"
                               value="<?= esc($event['priority'] ?? 0) ?>"
                               class="form-control">
                        <small>Чем меньше число, тем выше в списке</small>
                    </div>
                    <div class="form-group half">
                        <label for="publish">Статус</label>
                        <select id="publish" name="publish" class="form-control">
                            <option value="0" <?= (isset($event) && $event['publish'] == 0) ? 'selected' : '' ?>>Черновик</option>
                            <option value="1" <?= (isset($event) && $event['publish'] == 1) ? 'selected' : '' ?>>Опубликовано</option>
                        </select>
                    </div>
                </div>
            </div>

            <?php if (isset($event)): ?>
                <div class="settings-section">
                    <div class="form-group">
                        <label>📅 Время создания</label>
                        <div class="form-control-static"><?= date('d.m.Y H:i:s', strtotime($event['create'])) ?></div>
                    </div>
                    <div class="form-group">
                        <label>🔄 Время изменения</label>
                        <div class="form-control-static"><?= date('d.m.Y H:i:s', strtotime($event['modify'])) ?></div>
                    </div>
                </div>
            <?php endif; ?>

            <div class="form-actions">
                <a href="/admin-panel/projects/edit/<?= $project['id'] ?>" class="btn-cancel">Отмена</a>
                <button type="submit" class="btn-save">💾 <?= isset($event) ? 'Сохранить' : 'Создать' ?></button>
            </div>
        </form>
    </div>

    <!-- Скрипты для выбора изображений -->
    <script>
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

        // Инициализация CKEditor для поля more_info
        if (typeof CKEDITOR !== 'undefined' && document.getElementById('more_info')) {
            CKEDITOR.replace('more_info', {
                language: 'ru',
                height: 400,
                toolbar: [
                    ['Source', '-', 'Bold', 'Italic', 'Underline', 'Strike'],
                    ['NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', 'Blockquote'],
                    ['JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock'],
                    ['Link', 'Unlink', 'Anchor'],
                    ['Image', 'Table', 'HorizontalRule'],
                    ['Styles', 'Format', 'Font', 'FontSize'],
                    ['TextColor', 'BGColor'],
                    ['Maximize', 'ShowBlocks']
                ],
                filebrowserBrowseUrl: '/admin-panel/editor/ckeditor-browse',
                filebrowserUploadUrl: '/admin-panel/editor/upload-image'
            });
        }
    </script>

<?= $this->endSection() ?>