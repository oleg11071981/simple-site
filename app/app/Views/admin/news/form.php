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

            <div class="settings-section">
                <h2>Основная информация</h2>

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
                    <label for="foto">Главное изображение</label>
                    <div class="foto-preview" id="fotoPreview">
                        <?php if (isset($news) && $news['foto'] > 0): ?>
                            <img src="/uploads/<?= $news['foto_file'] ?? '' ?>" style="max-width: 200px; border-radius: 8px;">
                        <?php else: ?>
                            <div class="foto-placeholder" style="width: 200px; height: 150px; background: #f8f9fa; display: flex; align-items: center; justify-content: center; border-radius: 8px; border: 1px dashed #dee2e6;">
                                <span style="color: #6c757d;">Нет изображения</span>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="foto-actions" style="margin-top: 10px;">
                        <input type="hidden" name="foto" id="foto" value="<?= esc($news['foto'] ?? 0) ?>">
                        <input type="hidden" name="foto_file" id="foto_file" value="<?= esc($news['foto_file'] ?? '') ?>">
                        <button type="button" class="btn-select-foto" onclick="openFileManager('foto')" style="background: #007bff; color: white; padding: 6px 12px; border: none; border-radius: 4px; cursor: pointer;">📁 Выбрать изображение</button>
                        <button type="button" class="btn-clear-foto" onclick="clearFoto()" style="background: #dc3545; color: white; padding: 6px 12px; border: none; border-radius: 4px; cursor: pointer; margin-left: 8px;">🗑️ Удалить</button>
                    </div>
                    <small>Рекомендуемый размер: 1200x800px</small>
                </div>

                <div class="form-row">
                    <div class="form-group half">
                        <label for="date">Дата новости</label>
                        <input type="date" id="date" name="date"
                               value="<?= esc($news['date'] ?? date('Y-m-d')) ?>"
                               class="form-control">
                    </div>
                    <div class="form-group half">
                        <label for="morder">Порядок сортировки</label>
                        <input type="number" id="morder" name="morder"
                               value="<?= esc($news['morder'] ?? 0) ?>"
                               class="form-control">
                    </div>
                </div>
            </div>

            <div class="settings-section">
                <h2>Анонс</h2>

                <div class="form-group">
                    <label for="anons_text">Краткий текст анонса</label>
                    <textarea id="anons_text" name="anons_text" rows="5"
                              class="form-control"
                              placeholder="Краткое описание новости"><?= htmlspecialchars($news['anons_text'] ?? '') ?></textarea>
                </div>
            </div>

            <div class="settings-section">
                <h2>Полное содержание</h2>

                <div class="form-group">
                    <label for="more_info">Подробная информация</label>
                    <textarea id="more_info" name="more_info" rows="15"
                              class="form-control"
                              placeholder="Введите полный текст новости"><?= htmlspecialchars($news['more_info'] ?? '') ?></textarea>
                    <small>Поддерживается HTML разметка. Используйте визуальный редактор.</small>
                </div>
            </div>

            <div class="settings-section">
                <h2>SEO настройки</h2>

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

            <div class="settings-section">
                <h2>Настройки публикации</h2>

                <div class="form-row">
                    <div class="form-group half">
                        <label for="publish">Статус</label>
                        <select id="publish" name="publish" class="form-control">
                            <option value="0" <?= (isset($news) && $news['publish'] == 0) ? 'selected' : '' ?>>Черновик</option>
                            <option value="1" <?= (isset($news) && $news['publish'] == 1) ? 'selected' : '' ?>>Опубликовано</option>
                        </select>
                    </div>
                    <div class="form-group half">
                        <label for="show_all">Показывать на главной</label>
                        <select id="show_all" name="show_all" class="form-control">
                            <option value="0" <?= (isset($news) && $news['show_all'] == 0) ? 'selected' : '' ?>>Нет</option>
                            <option value="1" <?= (isset($news) && $news['show_all'] == 1) ? 'selected' : '' ?>>Да</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="category_news">Категория новости</label>
                    <select id="category_news" name="category_news" class="form-control">
                        <option value="1" <?= (isset($news) && $news['category_news'] == 1) ? 'selected' : '' ?>>📋 Новости комитета</option>
                        <option value="2" <?= (isset($news) && $news['category_news'] == 2) ? 'selected' : '' ?>>🌍 Новости в РФ и мире</option>
                    </select>
                    <small>Выберите категорию для отображения на сайте</small>
                </div>

                <div class="form-group">
                    <label for="type">Тип новости</label>
                    <select id="type" name="type" class="form-control">
                        <option value="0" <?= (isset($news) && $news['type'] == 0) ? 'selected' : '' ?>>Обычная</option>
                        <option value="1" <?= (isset($news) && $news['type'] == 1) ? 'selected' : '' ?>>Важная</option>
                        <option value="2" <?= (isset($news) && $news['type'] == 2) ? 'selected' : '' ?>>Срочная</option>
                    </select>
                </div>
            </div>

            <div class="settings-section">
                <h2>Галерея (медиа)</h2>

                <div class="form-group">
                    <label for="media">Привязать галерею</label>
                    <div class="media-select-wrapper">
                        <input type="text"
                               id="mediaSearch"
                               class="form-control"
                               placeholder="🔍 Поиск галереи..."
                               autocomplete="off">
                        <select id="media" name="media" class="form-control" size="8" style="margin-top: 8px;">
                            <option value="0">— Без галереи —</option>
                            <?php if (!empty($mediaCategories)): ?>
                                <?php foreach ($mediaCategories as $cat): ?>
                                    <option value="<?= $cat['id'] ?>"
                                            data-name="<?= esc(strtolower($cat['name'])) ?>"
                                        <?= (isset($news) && $news['media'] == $cat['id']) ? 'selected' : '' ?>>
                                        <?= str_repeat('—', $cat['level'] ?? 0) ?> 📁 <?= esc($cat['name']) ?>
                                        <?php if ($cat['files_count'] > 0): ?>
                                            (<?= $cat['files_count'] ?> файлов)
                                        <?php endif; ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    <small>Выберите галерею для отображения на странице новости</small>
                </div>
            </div>

            <?php if (isset($news)): ?>
                <div class="settings-section">
                    <div class="form-group">
                        <label>📅 Время создания</label>
                        <div class="form-control-static"><?= date('d.m.Y H:i:s', strtotime($news['create'])) ?></div>
                    </div>
                    <div class="form-group">
                        <label>🔄 Время изменения</label>
                        <div class="form-control-static"><?= date('d.m.Y H:i:s', strtotime($news['modify'])) ?></div>
                    </div>
                </div>
            <?php endif; ?>

            <div class="form-actions">
                <a href="/admin-panel/news" class="btn-cancel">Отмена</a>
                <button type="submit" class="btn-save">💾 Сохранить новость</button>
            </div>
        </form>
    </div>

    <script>
        function openFileManager(fieldName) {
            // Открываем окно выбора файлов с фильтром изображений
            var url = '/admin-panel/editor/ckeditor-browse?type=image&field=' + fieldName;
            window.open(url, 'FileManager', 'width=1200,height=700,left=100,top=50,toolbar=no,scrollbars=yes,resizable=yes');
        }

        function clearFoto() {
            document.getElementById('foto').value = 0;
            document.getElementById('foto_file').value = '';
            document.getElementById('fotoPreview').innerHTML = '<div class="foto-placeholder" style="width: 200px; height: 150px; background: #f8f9fa; display: flex; align-items: center; justify-content: center; border-radius: 8px; border: 1px dashed #dee2e6;"><span style="color: #6c757d;">Нет изображения</span></div>';
        }

        // Функция для установки выбранного файла (вызывается из окна выбора)
        function setSelectedFile(fileId, fileName, fileUrl) {
            document.getElementById('foto').value = fileId;
            document.getElementById('foto_file').value = fileName;
            document.getElementById('fotoPreview').innerHTML = '<img src="' + fileUrl + '" style="max-width: 200px; border-radius: 8px;">';
        }

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
    </script>

    <script>
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
                            emptyOption.textContent = '🔍 Ничего не найдено';
                        }
                    } else {
                        const emptyOption = Array.from(options).find(opt => opt.value === '0');
                        if (emptyOption && emptyOption.textContent !== '— Без галереи —') {
                            emptyOption.textContent = '— Без галереи —';
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
    </script>

<?= $this->endSection() ?>