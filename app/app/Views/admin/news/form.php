<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>

    <div class="form-container">
        <div class="page-header">
            <h1><?= isset($news) ? 'Редактирование новости' : 'Создание новости' ?></h1>
            <p><?= isset($news) ? 'Редактирование «' . esc($news['name']) . '»' : 'Добавление новой новости на сайт' ?></p>
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

        <form action="<?= isset($news) ? '/admin-panel/news/update/' . $news['id'] : '/admin-panel/news/store' ?>" method="post" class="settings-form">
            <?= csrf_field() ?>

            <?php if (isset($news)): ?>
                <input type="hidden" name="id" value="<?= $news['id'] ?>">
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
                <div class="settings-section">
                    <h2>Основная информация (Русский)</h2>

                    <?php if (isset($news)): ?>
                        <div class="form-group">
                            <label>ID новости</label>
                            <div class="form-control-static"><?= $news['id'] ?></div>
                        </div>
                    <?php endif; ?>

                    <div class="form-group">
                        <label for="name">Заголовок новости <span class="required">*</span></label>
                        <input type="text" id="name" name="name"
                               value="<?= esc($news['name'] ?? '') ?>"
                               class="form-control"
                               placeholder="Введите заголовок новости"
                               required>
                    </div>

                    <div class="form-group">
                        <label for="path">URL-путь</label>
                        <input type="text" id="path" name="path"
                               value="<?= esc($news['path'] ?? '') ?>"
                               class="form-control"
                               placeholder="avto-iz-germanii">
                        <small>
                            <a href="#" onclick="rusToTranslit('path', document.getElementById('name')); return false;">Сформировать из названия</a>
                        </small>
                    </div>

                    <div class="form-group">
                        <label for="author">Автор</label>
                        <input type="text" id="author" name="author"
                               value="<?= esc($news['author'] ?? '') ?>"
                               class="form-control"
                               placeholder="Автор новости">
                    </div>

                    <div class="form-group">
                        <label for="anons_text">Краткий текст анонса (Русский)</label>
                        <textarea id="anons_text" name="anons_text" rows="5"
                                  class="form-control"
                                  placeholder="Краткое описание новости"><?= htmlspecialchars($news['anons_text'] ?? '') ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="more_info">Полное содержание (Русский)</label>
                        <textarea id="more_info" name="more_info" rows="15"
                                  class="form-control"
                                  placeholder="Введите полный текст новости"><?= htmlspecialchars($news['more_info'] ?? '') ?></textarea>
                        <small>Поддерживается HTML разметка. Используйте визуальный редактор.</small>
                    </div>
                </div>

                <div class="settings-section">
                    <h2>SEO настройки (Русский)</h2>

                    <div class="form-group">
                        <label for="keywords">Ключевые слова</label>
                        <textarea id="keywords" name="keywords" rows="3"
                                  class="form-control"
                                  placeholder="Ключевые слова через запятую"><?= esc($news['keywords'] ?? '') ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="description">Мета-описание</label>
                        <textarea id="description" name="description" rows="4"
                                  class="form-control"
                                  placeholder="Краткое описание для поисковых систем"><?= esc($news['description'] ?? '') ?></textarea>
                        <small>Рекомендуемая длина: 150-160 символов</small>
                    </div>
                </div>
            </div>

            <!-- ======================================== -->
            <!-- ВКЛАДКА ENGLISH -->
            <!-- ======================================== -->

            <div id="tab-en" class="tab-content">
                <div class="settings-section">
                    <h2>Basic Information (English)</h2>

                    <?php if (isset($news)): ?>
                        <div class="form-group">
                            <label>News ID</label>
                            <div class="form-control-static"><?= $news['id'] ?></div>
                        </div>
                    <?php endif; ?>

                    <div class="form-group">
                        <label for="name_en">News Title <span class="required">*</span></label>
                        <input type="text" id="name_en" name="name_en"
                               value="<?= esc($news['name_en'] ?? '') ?>"
                               class="form-control"
                               placeholder="Enter news title">
                    </div>

                    <div class="form-group">
                        <label for="author_en">Author</label>
                        <input type="text" id="author_en" name="author_en"
                               value="<?= esc($news['author_en'] ?? '') ?>"
                               class="form-control"
                               placeholder="News author">
                    </div>

                    <div class="form-group">
                        <label for="anons_text_en">Announcement text (English)</label>
                        <textarea id="anons_text_en" name="anons_text_en" rows="5"
                                  class="form-control"
                                  placeholder="Short news description"><?= htmlspecialchars($news['anons_text_en'] ?? '') ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="more_info_en">Full content (English)</label>
                        <textarea id="more_info_en" name="more_info_en" rows="15"
                                  class="form-control"
                                  placeholder="Enter full news text"><?= htmlspecialchars($news['more_info_en'] ?? '') ?></textarea>
                        <small>HTML supported. Use visual editor.</small>
                    </div>
                </div>

                <div class="settings-section">
                    <h2>SEO Settings (English)</h2>

                    <div class="form-group">
                        <label for="keywords_en">Keywords</label>
                        <textarea id="keywords_en" name="keywords_en" rows="3"
                                  class="form-control"
                                  placeholder="Keywords separated by commas"><?= esc($news['keywords_en'] ?? '') ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="description_en">Meta Description</label>
                        <textarea id="description_en" name="description_en" rows="4"
                                  class="form-control"
                                  placeholder="Brief description for search engines"><?= esc($news['description_en'] ?? '') ?></textarea>
                        <small>Recommended length: 150-160 characters</small>
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
                        <?php if (isset($news) && $news['foto'] > 0 && !empty($news['foto_file'])): ?>
                            <img src="/uploads/<?= $news['foto_file'] ?>" style="max-width: 200px; border-radius: 8px;">
                        <?php else: ?>
                            <div class="foto-placeholder" style="width: 200px; height: 150px; background: #f8f9fa; display: flex; align-items: center; justify-content: center; border-radius: 8px; border: 1px dashed #dee2e6;">
                                <span style="color: #6c757d;">Нет изображения / No image</span>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="foto-actions" style="margin-top: 10px;">
                        <input type="hidden" name="foto" id="foto" value="<?= esc($news['foto'] ?? 0) ?>">
                        <input type="hidden" name="foto_file" id="foto_file" value="<?= esc($news['foto_file'] ?? '') ?>">
                        <button type="button" class="btn-select-foto" onclick="openFileManager('foto')" style="background: #007bff; color: white; padding: 6px 12px; border: none; border-radius: 4px; cursor: pointer;">📁 Выбрать изображение / Select image</button>
                        <button type="button" class="btn-clear-foto" onclick="clearFoto()" style="background: #dc3545; color: white; padding: 6px 12px; border: none; border-radius: 4px; cursor: pointer; margin-left: 8px;">🗑️ Удалить / Remove</button>
                    </div>
                    <small>Рекомендуемый размер: 1200x800px / Recommended size: 1200x800px</small>
                </div>

                <div class="form-group">
                    <label for="media">Галерея / Gallery</label>
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
                                        <?= (isset($news) && ($news['media'] ?? 0) == $cat['id']) ? 'selected' : '' ?>>
                                        <?= str_repeat('—', $cat['level'] ?? 0) ?> 📁 <?= esc($cat['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    <small>Выберите галерею для отображения на странице новости / Select gallery to display on news page</small>
                </div>
            </div>

            <div class="settings-section">
                <h2>Настройки публикации / Publication Settings</h2>

                <div class="form-row">
                    <div class="form-group half">
                        <label for="date">Дата новости / News date</label>
                        <input type="date" id="date" name="date"
                               value="<?= esc($news['date'] ?? date('Y-m-d')) ?>"
                               class="form-control">
                    </div>
                    <div class="form-group half">
                        <label for="morder">Порядок сортировки / Sort order</label>
                        <input type="number" id="morder" name="morder"
                               value="<?= esc($news['morder'] ?? 0) ?>"
                               class="form-control">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group half">
                        <label for="publish">Статус / Status</label>
                        <select id="publish" name="publish" class="form-control">
                            <option value="0" <?= (isset($news) && $news['publish'] == 0) ? 'selected' : '' ?>>Черновик / Draft</option>
                            <option value="1" <?= (isset($news) && $news['publish'] == 1) ? 'selected' : '' ?>>Опубликовано / Published</option>
                        </select>
                    </div>
                    <div class="form-group half">
                        <label for="show_all">Показывать на главной / Show on homepage</label>
                        <select id="show_all" name="show_all" class="form-control">
                            <option value="0" <?= (isset($news) && $news['show_all'] == 0) ? 'selected' : '' ?>>Нет / No</option>
                            <option value="1" <?= (isset($news) && $news['show_all'] == 1) ? 'selected' : '' ?>>Да / Yes</option>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group half">
                        <label for="type">Тип новости / News type</label>
                        <select id="type" name="type" class="form-control">
                            <option value="0" <?= (isset($news) && $news['type'] == 0) ? 'selected' : '' ?>>Обычная / Normal</option>
                            <option value="1" <?= (isset($news) && $news['type'] == 1) ? 'selected' : '' ?>>Важная / Important</option>
                            <option value="2" <?= (isset($news) && $news['type'] == 2) ? 'selected' : '' ?>>Срочная / Urgent</option>
                        </select>
                    </div>
                    <div class="form-group half">
                        <label for="category_news">Категория новости / News category</label>
                        <select id="category_news" name="category_news" class="form-control">
                            <option value="0">— Без категории / No category —</option>
                            <?php if (!empty($newsCategories)): ?>
                                <?php foreach ($newsCategories as $cat): ?>
                                    <option value="<?= $cat['id'] ?>"
                                        <?= (isset($news) && $news['category_news'] == $cat['id']) ? 'selected' : '' ?>>
                                        <?= str_repeat('—', $cat['level'] ?? 0) ?> <?= esc($cat['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Время создания/изменения -->
            <?php if (isset($news)): ?>
                <div class="settings-section">
                    <div class="form-group">
                        <label>📅 Время создания / Created</label>
                        <div class="form-control-static"><?= date('d.m.Y H:i:s', strtotime($news['create'])) ?></div>
                    </div>
                    <div class="form-group">
                        <label>🔄 Время изменения / Modified</label>
                        <div class="form-control-static"><?= date('d.m.Y H:i:s', strtotime($news['modify'])) ?></div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Кнопки -->
            <div class="form-actions">
                <a href="/admin-panel/news" class="btn-cancel">Отмена / Cancel</a>
                <button type="submit" class="btn-save">💾 Сохранить новость / Save news</button>
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