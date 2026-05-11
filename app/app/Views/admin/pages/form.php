<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>

    <div class="form-container">
        <div class="page-header">
            <h1><?= isset($page) ? 'Редактирование страницы' : 'Создание страницы' ?></h1>
            <p><?= isset($page) ? 'Редактирование «' . esc($page['name']) . '»' : 'Добавление новой страницы на сайт' ?></p>
        </div>

        <!-- Flash сообщения -->
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

        <!-- Форма -->
        <form action="<?= isset($page) ? '/admin-panel/pages/update/' . $page['id'] : '/admin-panel/pages/store' ?>" method="post" class="settings-form">
            <?= csrf_field() ?>

            <?php if (isset($page)): ?>
                <input type="hidden" name="id" value="<?= $page['id'] ?>">
            <?php endif; ?>

            <!-- Скрытое поле для parent, если он передан из URL -->
            <?php if (isset($parent_id) && $parent_id > 0 && !isset($page)): ?>
                <input type="hidden" name="parent" value="<?= $parent_id ?>">
            <?php endif; ?>

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
                <!-- Расположение (только для русской версии) -->
                <div class="settings-section">
                    <h2>Расположение</h2>

                    <?php if (isset($parent_id) && $parent_id > 0 && !isset($page)): ?>
                        <input type="hidden" name="parent" value="<?= $parent_id ?>">
                        <div class="form-group">
                            <label>Родительская страница</label>
                            <div class="form-control-static">
                                <?php
                                $parentName = 'Корневая страница';
                                if (!empty($parents)) {
                                    foreach ($parents as $p) {
                                        if ($p['id'] == $parent_id) {
                                            $parentName = $p['name'];
                                            break;
                                        }
                                    }
                                }
                                ?>
                                📁 <?= esc($parentName) ?>
                            </div>
                            <small>Страница будет создана внутри выбранного раздела</small>
                        </div>
                    <?php else: ?>
                        <div class="form-group">
                            <label for="parent">Родительская страница</label>
                            <select id="parent" name="parent" class="form-control">
                                <option value="0">— Корневая страница (без родителя) —</option>
                                <?php if (!empty($parents)): ?>
                                    <?php foreach ($parents as $parent): ?>
                                        <?php if (isset($page) && $page['id'] == $parent['id']) continue; ?>
                                        <option value="<?= $parent['id'] ?>"
                                            <?= (isset($page) && ($page['parent'] ?? 0) == $parent['id']) ? 'selected' : '' ?>>
                                            <?= str_repeat('—', $parent['level'] ?? 0) ?> <?= esc($parent['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                            <small>Выберите родительскую страницу для создания вложенности</small>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="settings-section">
                    <h2>Основная информация (Русский)</h2>

                    <?php if (isset($page)): ?>
                        <div class="form-group">
                            <label>ID страницы</label>
                            <div class="form-control-static"><?= $page['id'] ?></div>
                        </div>
                    <?php endif; ?>

                    <div class="form-group">
                        <label for="name">Название страницы <span class="required">*</span></label>
                        <input type="text" id="name" name="name"
                               value="<?= esc($page['name'] ?? '') ?>"
                               class="form-control"
                               placeholder="Введите название страницы"
                               required>
                    </div>

                    <div class="form-group">
                        <label for="path">Виртуальный путь (URL)</label>
                        <input type="text" id="path" name="path"
                               value="<?= esc($page['path'] ?? '') ?>"
                               class="form-control"
                               placeholder="avto-iz-germanii">
                        <small>
                            <a href="#" onclick="rusToTranslit('path', document.getElementById('name')); return false;">Сформировать из названия</a>
                        </small>
                    </div>
                </div>

                <div class="settings-section">
                    <h2>Содержимое страницы (Русский)</h2>

                    <div class="form-group">
                        <label for="more_info">Подробная информация</label>
                        <textarea id="more_info" name="more_info" rows="15"
                                  class="form-control"
                                  placeholder="Введите содержимое страницы"><?= htmlspecialchars($page['more_info'] ?? '') ?></textarea>
                        <small>Поддерживается HTML разметка. Используйте визуальный редактор.</small>
                    </div>
                </div>

                <div class="settings-section">
                    <h2>SEO настройки (Русский)</h2>

                    <div class="form-group">
                        <label for="keywords">Ключевые слова (Keywords)</label>
                        <textarea id="keywords" name="keywords" rows="3"
                                  class="form-control"
                                  placeholder="Ключевые слова через запятую"><?= esc($page['keywords'] ?? '') ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="description">Мета-описание (Description)</label>
                        <textarea id="description" name="description" rows="4"
                                  class="form-control"
                                  placeholder="Краткое описание страницы для поисковых систем"><?= esc($page['description'] ?? '') ?></textarea>
                        <small>Рекомендуемая длина: 150-160 символов</small>
                    </div>
                </div>

                <div class="settings-section">
                    <h2>Текст анонса (Русский)</h2>

                    <div class="form-group">
                        <label for="anons_text">Краткое описание для анонса</label>
                        <textarea id="anons_text" name="anons_text" rows="4"
                                  class="form-control"
                                  placeholder="Краткое описание для списка новостей или анонсов"><?= esc($page['anons_text'] ?? '') ?></textarea>
                    </div>
                </div>
            </div>

            <!-- ======================================== -->
            <!-- ВКЛАДКА ENGLISH -->
            <!-- ======================================== -->

            <div id="tab-en" class="tab-content">
                <div class="settings-section">
                    <h2>Basic Information (English)</h2>

                    <?php if (isset($page)): ?>
                        <div class="form-group">
                            <label>Page ID</label>
                            <div class="form-control-static"><?= $page['id'] ?></div>
                        </div>
                    <?php endif; ?>

                    <div class="form-group">
                        <label for="name_en">Page Title <span class="required">*</span></label>
                        <input type="text" id="name_en" name="name_en"
                               value="<?= esc($page['name_en'] ?? '') ?>"
                               class="form-control"
                               placeholder="Enter page title">
                    </div>

                    <div class="form-group">
                        <label for="path_en">URL Path</label>
                        <input type="text" id="path_en" name="path_en"
                               value="<?= esc($page['path'] ?? '') ?>"
                               class="form-control"
                               placeholder="auto-from-germany">
                        <small>Path is shared between languages (same URL)</small>
                    </div>
                </div>

                <div class="settings-section">
                    <h2>Page Content (English)</h2>

                    <div class="form-group">
                        <label for="more_info_en">Detailed Information</label>
                        <textarea id="more_info_en" name="more_info_en" rows="15"
                                  class="form-control"
                                  placeholder="Enter page content"><?= htmlspecialchars($page['more_info_en'] ?? '') ?></textarea>
                        <small>HTML supported. Use visual editor.</small>
                    </div>
                </div>

                <div class="settings-section">
                    <h2>SEO Settings (English)</h2>

                    <div class="form-group">
                        <label for="keywords_en">Keywords</label>
                        <textarea id="keywords_en" name="keywords_en" rows="3"
                                  class="form-control"
                                  placeholder="Keywords separated by commas"><?= esc($page['keywords_en'] ?? '') ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="description_en">Meta Description</label>
                        <textarea id="description_en" name="description_en" rows="4"
                                  class="form-control"
                                  placeholder="Brief description for search engines"><?= esc($page['description_en'] ?? '') ?></textarea>
                        <small>Recommended length: 150-160 characters</small>
                    </div>
                </div>

                <div class="settings-section">
                    <h2>Announcement Text (English)</h2>

                    <div class="form-group">
                        <label for="anons_text_en">Short description for announcement</label>
                        <textarea id="anons_text_en" name="anons_text_en" rows="4"
                                  class="form-control"
                                  placeholder="Short description for news lists or announcements"><?= esc($page['anons_text_en'] ?? '') ?></textarea>
                    </div>
                </div>
            </div>

            <!-- ======================================== -->
            <!-- ОБЩИЕ НАСТРОЙКИ (НЕ ЗАВИСЯТ ОТ ЯЗЫКА) -->
            <!-- ======================================== -->

            <div class="settings-section">
                <h2>Настройки отображения (Display Settings)</h2>

                <div class="form-group">
                    <label for="show_in_menu">Показывать в меню / Show in menu</label>
                    <select id="show_in_menu" name="show_in_menu" class="form-control">
                        <option value="1" <?= (isset($page) && $page['show_in_menu'] == 1) ? 'selected' : '' ?>>Да / Yes</option>
                        <option value="0" <?= (isset($page) && $page['show_in_menu'] == 0) ? 'selected' : '' ?>>Нет / No</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="priority">Приоритет / Priority (порядок сортировки)</label>
                    <input type="number" id="priority" name="priority"
                           value="<?= esc($page['priority'] ?? 0) ?>"
                           class="form-control">
                    <small>Чем меньше число, тем выше в списке / Smaller number = higher position</small>
                </div>

                <div class="form-group">
                    <label for="publish">Публикация / Publication</label>
                    <select id="publish" name="publish" class="form-control">
                        <option value="0" <?= (isset($page) && $page['publish'] == 0) ? 'selected' : '' ?>>Черновик / Draft</option>
                        <option value="1" <?= (isset($page) && $page['publish'] == 1) ? 'selected' : '' ?>>Опубликовано / Published</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="new_on_site">Пометить как новинку / Mark as new</label>
                    <select id="new_on_site" name="new_on_site" class="form-control">
                        <option value="0" <?= (isset($page) && $page['new_on_site'] == 0) ? 'selected' : '' ?>>Нет / No</option>
                        <option value="1" <?= (isset($page) && $page['new_on_site'] == 1) ? 'selected' : '' ?>>Да / Yes</option>
                    </select>
                </div>
            </div>

            <div class="settings-section">
                <h2>Галерея / Gallery</h2>

                <div class="form-group">
                    <label for="media">Привязать галерею / Attach gallery</label>
                    <div class="media-select-wrapper">
                        <input type="text"
                               id="mediaSearch"
                               class="form-control"
                               placeholder="🔍 Поиск галереи / Search gallery..."
                               autocomplete="off">
                        <select id="media" name="media" class="form-control" size="8" style="margin-top: 8px;">
                            <option value="0">— Без галереи / No gallery —</option>
                            <?php if (!empty($mediaCategories)): ?>
                                <?php foreach ($mediaCategories as $cat): ?>
                                    <option value="<?= $cat['id'] ?>"
                                            data-name="<?= esc(strtolower($cat['name'])) ?>"
                                        <?= (isset($page) && $page['media'] == $cat['id']) ? 'selected' : '' ?>>
                                        <?= str_repeat('—', $cat['level'] ?? 0) ?> 📁 <?= esc($cat['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    <small>Выберите галерею для отображения на странице / Select gallery to display on page</small>
                </div>
            </div>

            <!-- ======================================== -->
            <!-- ВРЕМЯ СОЗДАНИЯ И ИЗМЕНЕНИЯ -->
            <!-- ======================================== -->

            <?php if (isset($page)): ?>
                <div class="settings-section">
                    <div class="form-group">
                        <label>📅 Время создания / Created</label>
                        <div class="form-control-static"><?= date('d.m.Y H:i:s', strtotime($page['create'])) ?></div>
                    </div>
                    <div class="form-group">
                        <label>🔄 Время изменения / Modified</label>
                        <div class="form-control-static"><?= date('d.m.Y H:i:s', strtotime($page['modify'])) ?></div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- ======================================== -->
            <!-- КНОПКИ -->
            <!-- ======================================== -->

            <div class="form-actions">
                <a href="/admin-panel/pages<?= (isset($parent_id) && $parent_id > 0) ? '?parent=' . $parent_id : '' ?>" class="btn-cancel">Отмена / Cancel</a>
                <button type="submit" class="btn-save">💾 Сохранить / Save</button>
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

        // Поиск по категориям галереи
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('mediaSearch');
            const selectEl = document.getElementById('media');

            if (searchInput && selectEl) {
                function filterCategories() {
                    const searchTerm = searchInput.value.toLowerCase().trim();
                    const options = selectEl.querySelectorAll('option');

                    let hasVisible = false;

                    options.forEach(option => {
                        const text = option.textContent.toLowerCase();
                        const categoryName = option.getAttribute('data-name') || text;

                        if (searchTerm === '') {
                            option.style.display = '';
                            hasVisible = true;
                        } else if (categoryName.includes(searchTerm) || text.includes(searchTerm)) {
                            option.style.display = '';
                            hasVisible = true;
                        } else {
                            option.style.display = 'none';
                        }
                    });

                    if (!hasVisible) {
                        const emptyOption = Array.from(options).find(opt => opt.value === '0');
                        if (emptyOption) {
                            emptyOption.style.display = '';
                            emptyOption.textContent = '🔍 Ничего не найдено / Nothing found';
                        }
                    } else {
                        const emptyOption = Array.from(options).find(opt => opt.value === '0');
                        if (emptyOption && emptyOption.textContent !== '— Без галереи / No gallery —') {
                            emptyOption.textContent = '— Без галереи / No gallery —';
                        }
                    }
                }

                searchInput.addEventListener('input', filterCategories);
                searchInput.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        const firstVisible = Array.from(selectEl.options).find(opt => opt.style.display !== 'none');
                        if (firstVisible) {
                            firstVisible.selected = true;
                        }
                    }
                });
            }
        });

        // Инициализация CKEditor для русской версии
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

        // Инициализация CKEditor для английской версии
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
    </style>

<?= $this->endSection() ?>