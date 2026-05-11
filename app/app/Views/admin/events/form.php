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

            <!-- ======================================== -->
            <!-- ВКЛАДКИ РУССКИЙ / ENGLISH -->
            <!-- ======================================== -->

            <div class="tabs">
                <button type="button" class="tab-btn active" data-tab="ru">🇷🇺 Русский</button>
                <button type="button" class="tab-btn" data-tab="en">🇬🇧 English</button>
            </div>

            <!-- ======================================== -->
            <!-- ВКЛАДКА РУССКИЙ -->
            <!-- ======================================== -->

            <div id="tab-ru" class="tab-content active">
                <div class="settings-section">
                    <h2>Основная информация (Русский)</h2>

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
                               required>
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
                        <label for="anons_text">Краткое описание (Русский)</label>
                        <textarea id="anons_text" name="anons_text" rows="3"
                                  class="form-control"
                                  placeholder="Краткое описание мероприятия"><?= esc($event['anons_text'] ?? '') ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="more_info">Полное описание (Русский)</label>
                        <textarea id="more_info" name="more_info" rows="15"
                                  class="form-control"
                                  placeholder="Подробное описание мероприятия (HTML)"><?= htmlspecialchars($event['more_info'] ?? '') ?></textarea>
                        <small>Поддерживается HTML разметка. Используйте визуальный редактор.</small>
                    </div>
                </div>
            </div>

            <!-- ======================================== -->
            <!-- ВКЛАДКА ENGLISH -->
            <!-- ======================================== -->

            <div id="tab-en" class="tab-content">
                <div class="settings-section">
                    <h2>Basic Information (English)</h2>

                    <?php if (isset($event)): ?>
                        <div class="form-group">
                            <label>Event ID</label>
                            <div class="form-control-static"><?= $event['id'] ?></div>
                        </div>

                        <div class="form-group">
                            <label>Project</label>
                            <div class="form-control-static">
                                <a href="/admin-panel/projects/edit/<?= $project['id'] ?>"><?= esc($project['name']) ?></a>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="form-group">
                        <label for="name_en">Event Title <span class="required">*</span></label>
                        <input type="text" id="name_en" name="name_en"
                               value="<?= esc($event['name_en'] ?? '') ?>"
                               class="form-control"
                               placeholder="Enter event title">
                    </div>

                    <div class="form-group">
                        <label for="anons_text_en">Short description (English)</label>
                        <textarea id="anons_text_en" name="anons_text_en" rows="3"
                                  class="form-control"
                                  placeholder="Short event description"><?= esc($event['anons_text_en'] ?? '') ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="more_info_en">Full description (English)</label>
                        <textarea id="more_info_en" name="more_info_en" rows="15"
                                  class="form-control"
                                  placeholder="Detailed event description (HTML)"><?= htmlspecialchars($event['more_info_en'] ?? '') ?></textarea>
                        <small>HTML supported. Use visual editor.</small>
                    </div>

                    <div class="form-group">
                        <label for="location_en">Location (English)</label>
                        <input type="text" id="location_en" name="location_en"
                               value="<?= esc($event['location_en'] ?? '') ?>"
                               class="form-control"
                               placeholder="Moscow, Kremlin">
                    </div>
                </div>
            </div>

            <!-- ======================================== -->
            <!-- ОБЩИЕ НАСТРОЙКИ (НЕ ЗАВИСЯТ ОТ ЯЗЫКА) -->
            <!-- ======================================== -->

            <div class="settings-section">
                <h2>Изображения / Images</h2>

                <div class="form-group">
                    <label for="foto">Главное изображение / Main image</label>
                    <div class="foto-preview" id="fotoPreview">
                        <?php if (isset($event) && $event['foto'] > 0 && !empty($event['foto_file'])): ?>
                            <img src="/uploads/<?= $event['foto_file'] ?>" style="max-width: 200px; border-radius: 8px;">
                        <?php else: ?>
                            <div class="foto-placeholder" style="width: 200px; height: 150px; background: #f8f9fa; display: flex; align-items: center; justify-content: center; border-radius: 8px; border: 1px dashed #dee2e6;">
                                <span style="color: #6c757d;">Нет изображения / No image</span>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="foto-actions" style="margin-top: 10px;">
                        <input type="hidden" name="foto" id="foto" value="<?= esc($event['foto'] ?? 0) ?>">
                        <input type="hidden" name="foto_file" id="foto_file" value="<?= esc($event['foto_file'] ?? '') ?>">
                        <button type="button" class="btn-select-foto" onclick="openFileManager('foto')" style="background: #007bff; color: white; padding: 6px 12px; border: none; border-radius: 4px; cursor: pointer;">📁 Выбрать изображение / Select image</button>
                        <button type="button" class="btn-clear-foto" onclick="clearFoto()" style="background: #dc3545; color: white; padding: 6px 12px; border: none; border-radius: 4px; cursor: pointer; margin-left: 8px;">🗑️ Удалить / Remove</button>
                    </div>
                    <small>Рекомендуемый размер: 800x600px / Recommended size: 800x600px</small>
                </div>

                <div class="form-group">
                    <label for="media">Галерея мероприятия / Event gallery</label>
                    <div class="media-select-wrapper">
                        <input type="text"
                               id="mediaSearch"
                               class="form-control"
                               placeholder="🔍 Поиск галереи / Search gallery..."
                               autocomplete="off">
                        <select id="media" name="media" class="form-control" size="6" style="margin-top: 8px;">
                            <option value="0">— Без галереи / No gallery —</option>
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
                    <small>Выберите галерею для отображения на странице мероприятия / Select gallery to display on event page</small>
                </div>
            </div>

            <div class="settings-section">
                <h2>Дата и место / Date and Location</h2>

                <div class="form-row">
                    <div class="form-group half">
                        <label for="date_start">Дата начала / Start date</label>
                        <input type="date" id="date_start" name="date_start"
                               value="<?= esc($event['date_start'] ?? '') ?>"
                               class="form-control">
                    </div>
                    <div class="form-group half">
                        <label for="date_end">Дата окончания / End date</label>
                        <input type="date" id="date_end" name="date_end"
                               value="<?= esc($event['date_end'] ?? '') ?>"
                               class="form-control">
                    </div>
                </div>

                <div class="form-group">
                    <label for="location">Место проведения / Location (Russian)</label>
                    <input type="text" id="location" name="location"
                           value="<?= esc($event['location'] ?? '') ?>"
                           class="form-control"
                           placeholder="Москва, Кремль">
                </div>

                <div class="form-group">
                    <label for="link">Внешняя ссылка / External link</label>
                    <input type="url" id="link" name="link"
                           value="<?= esc($event['link'] ?? '') ?>"
                           class="form-control"
                           placeholder="https://example.com/event">
                    <small>Ссылка на страницу мероприятия (если есть) / Link to event page (if any)</small>
                </div>
            </div>

            <div class="settings-section">
                <h2>Настройки отображения / Display Settings</h2>

                <div class="form-row">
                    <div class="form-group half">
                        <label for="priority">Приоритет / Priority (порядок сортировки)</label>
                        <input type="number" id="priority" name="priority"
                               value="<?= esc($event['priority'] ?? 0) ?>"
                               class="form-control">
                        <small>Чем меньше число, тем выше в списке / Smaller number = higher position</small>
                    </div>
                    <div class="form-group half">
                        <label for="publish">Статус / Status</label>
                        <select id="publish" name="publish" class="form-control">
                            <option value="0" <?= (isset($event) && $event['publish'] == 0) ? 'selected' : '' ?>>Черновик / Draft</option>
                            <option value="1" <?= (isset($event) && $event['publish'] == 1) ? 'selected' : '' ?>>Опубликовано / Published</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Время создания/изменения -->
            <?php if (isset($event)): ?>
                <div class="settings-section">
                    <div class="form-group">
                        <label>📅 Время создания / Created</label>
                        <div class="form-control-static"><?= date('d.m.Y H:i:s', strtotime($event['create'])) ?></div>
                    </div>
                    <div class="form-group">
                        <label>🔄 Время изменения / Modified</label>
                        <div class="form-control-static"><?= date('d.m.Y H:i:s', strtotime($event['modify'])) ?></div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Кнопки -->
            <div class="form-actions">
                <a href="/admin-panel/projects/edit/<?= $project['id'] ?>" class="btn-cancel">Отмена / Cancel</a>
                <button type="submit" class="btn-save">💾 <?= isset($event) ? 'Сохранить / Save' : 'Создать / Create' ?></button>
            </div>
        </form>
    </div>

    <script>
        // Переключение вкладок
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const tabId = this.dataset.tab;
                document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
                document.querySelectorAll('.tab-content').forEach(t => t.classList.remove('active'));
                this.classList.add('active');
                document.getElementById(`tab-${tabId}`).classList.add('active');
            });
        });

        // Функции для работы с изображениями
        function openFileManager(fieldName) {
            var url = '/admin-panel/editor/ckeditor-browse?type=image&field=' + fieldName;
            window.open(url, 'FileManager', 'width=1200,height=700,left=100,top=50,toolbar=no,scrollbars=yes,resizable=yes');
        }

        function clearFoto() {
            document.getElementById('foto').value = 0;
            document.getElementById('foto_file').value = '';
            document.getElementById('fotoPreview').innerHTML = '<div class="foto-placeholder" style="width: 200px; height: 150px; background: #f8f9fa; display: flex; align-items: center; justify-content: center; border-radius: 8px; border: 1px dashed #dee2e6;"><span style="color: #6c757d;">Нет изображения / No image</span></div>';
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

        // Транслитерация
        function rusToTranslit(field, sourceField) {
            var rus = "абвгдеёжзийклмнопрстуфхцчшщъыьэюя";
            var eng = "abvgdeejziyklmnoprstufhccss_yaeuya";
            var text = sourceField.value;
            var result = "";
            for (var i = 0; i < text.length; i++) {
                var char = text[i].toLowerCase();
                var index = rus.indexOf(char);
                if (index >= 0) {
                    result += eng[index];
                } else if (char.match(/[a-z0-9]/)) {
                    result += char;
                } else if (char.match(/\s/)) {
                    result += "-";
                }
            }
            document.getElementById(field).value = result;
        }

        // CKEditor для русской версии
        if (typeof CKEDITOR !== 'undefined' && document.getElementById('more_info')) {
            CKEDITOR.replace('more_info', {
                language: 'ru',
                height: 400,
                toolbar: [
                    ['Source', '-', 'NewPage', 'Preview'],
                    ['Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord'],
                    ['Undo', 'Redo', '-', 'Find', 'Replace', '-', 'SelectAll', 'RemoveFormat'],
                    ['Bold', 'Italic', 'Underline', 'Strike'],
                    ['NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', 'Blockquote'],
                    ['JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock'],
                    ['Link', 'Unlink', 'Anchor'],
                    ['Image', 'Table', 'HorizontalRule', 'SpecialChar'],
                    ['Styles', 'Format', 'Font', 'FontSize'],
                    ['TextColor', 'BGColor'],
                    ['Maximize', 'ShowBlocks']
                ],
                filebrowserBrowseUrl: '/admin-panel/editor/ckeditor-browse',
                filebrowserUploadUrl: '/admin-panel/editor/upload-image'
            });
        }

        // CKEditor для английской версии
        if (typeof CKEDITOR !== 'undefined' && document.getElementById('more_info_en')) {
            CKEDITOR.replace('more_info_en', {
                language: 'en',
                height: 400,
                toolbar: [
                    ['Source', '-', 'NewPage', 'Preview'],
                    ['Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord'],
                    ['Undo', 'Redo', '-', 'Find', 'Replace', '-', 'SelectAll', 'RemoveFormat'],
                    ['Bold', 'Italic', 'Underline', 'Strike'],
                    ['NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', 'Blockquote'],
                    ['JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock'],
                    ['Link', 'Unlink', 'Anchor'],
                    ['Image', 'Table', 'HorizontalRule', 'SpecialChar'],
                    ['Styles', 'Format', 'Font', 'FontSize'],
                    ['TextColor', 'BGColor'],
                    ['Maximize', 'ShowBlocks']
                ],
                filebrowserBrowseUrl: '/admin-panel/editor/ckeditor-browse',
                filebrowserUploadUrl: '/admin-panel/editor/upload-image'
            });
        }
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

        .form-row {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
        }

        .form-group.half {
            flex: 1;
            min-width: 200px;
        }
    </style>

<?= $this->endSection() ?>