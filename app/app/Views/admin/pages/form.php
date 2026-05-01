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
            <!-- ОСНОВНАЯ ИНФОРМАЦИЯ -->
            <!-- ======================================== -->

            <div class="settings-section">
                <h2>Основная информация</h2>

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

            <!-- ======================================== -->
            <!-- РАСПОЛОЖЕНИЕ (РОДИТЕЛЬСКАЯ СТРАНИЦА) -->
            <!-- ======================================== -->

            <div class="settings-section">
                <h2>Расположение</h2>

                <?php if (isset($parent_id) && $parent_id > 0 && !isset($page)): ?>
                    <!-- Создание новой страницы внутри раздела -->
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
                    <!-- Редактирование существующей страницы или создание без parent -->
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

            <!-- ======================================== -->
            <!-- СОДЕРЖИМОЕ СТРАНИЦЫ -->
            <!-- ======================================== -->

            <div class="settings-section">
                <h2>Содержимое страницы</h2>

                <div class="form-group">
                    <label for="more_info">Подробная информация</label>
                    <textarea id="more_info" name="more_info" rows="15"
                              class="form-control"
                              placeholder="Введите содержимое страницы"><?= htmlspecialchars($page['more_info'] ?? '') ?></textarea>
                    <small>Поддерживается HTML разметка. Используйте визуальный редактор.</small>
                </div>
            </div>

            <!-- ======================================== -->
            <!-- SEO НАСТРОЙКИ -->
            <!-- ======================================== -->

            <div class="settings-section">
                <h2>SEO настройки</h2>

                <div class="form-group">
                    <label for="keywords">Ключевые слова (Keywords)</label>
                    <textarea id="keywords" name="keywords" rows="3"
                              class="form-control"
                              placeholder="Ключевые слова через запятую"><?= esc($page['keywords'] ?? '') ?></textarea>
                    <small>Пример: библиотека, книги, чтение, Карелия</small>
                </div>

                <div class="form-group">
                    <label for="description">Мета-описание (Description)</label>
                    <textarea id="description" name="description" rows="4"
                              class="form-control"
                              placeholder="Краткое описание страницы для поисковых систем"><?= esc($page['description'] ?? '') ?></textarea>
                    <small>Рекомендуемая длина: 150-160 символов</small>
                </div>

                <?php if (isset($page)): ?>
                    <div class="form-group">
                        <label>Счетчик символов</label>
                        <div class="char-counter">
                            <span id="keywordsCount">0</span> символов в ключевых словах<br>
                            <span id="descriptionCount">0</span> / 160 символов в описании
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- ======================================== -->
            <!-- НАСТРОЙКИ ОТОБРАЖЕНИЯ -->
            <!-- ======================================== -->

            <div class="settings-section">
                <h2>Настройки отображения</h2>

                <div class="form-group">
                    <label for="show_in_menu">Показывать в меню</label>
                    <select id="show_in_menu" name="show_in_menu" class="form-control">
                        <option value="1" <?= (isset($page) && $page['show_in_menu'] == 1) ? 'selected' : '' ?>>Да</option>
                        <option value="0" <?= (isset($page) && $page['show_in_menu'] == 0) ? 'selected' : '' ?>>Нет</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="priority">Приоритет (порядок сортировки)</label>
                    <input type="number" id="priority" name="priority"
                           value="<?= esc($page['priority'] ?? 0) ?>"
                           class="form-control">
                    <small>Чем меньше число, тем выше в списке</small>
                </div>

                <div class="form-group">
                    <label for="publish">Публикация</label>
                    <select id="publish" name="publish" class="form-control">
                        <option value="0" <?= (isset($page) && $page['publish'] == 0) ? 'selected' : '' ?>>Черновик</option>
                        <option value="1" <?= (isset($page) && $page['publish'] == 1) ? 'selected' : '' ?>>Опубликовано</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="new_on_site">Пометить как новинку</label>
                    <select id="new_on_site" name="new_on_site" class="form-control">
                        <option value="0" <?= (isset($page) && $page['new_on_site'] == 0) ? 'selected' : '' ?>>Нет</option>
                        <option value="1" <?= (isset($page) && $page['new_on_site'] == 1) ? 'selected' : '' ?>>Да</option>
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
                                        <?= (isset($page) && $page['media'] == $cat['id']) ? 'selected' : '' ?>>
                                        <?= str_repeat('—', $cat['level'] ?? 0) ?> 📁 <?= esc($cat['name']) ?>
                                        <?php if ($cat['files_count'] > 0): ?>
                                            (<?= $cat['files_count'] ?> файлов)
                                        <?php endif; ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    <small>Выберите галерею для отображения на странице</small>
                </div>
            </div>

            <!-- ======================================== -->
            <!-- АНОНС -->
            <!-- ======================================== -->

            <div class="settings-section">
                <h2>Текст анонса</h2>

                <div class="form-group">
                    <label for="anons_text">Краткое описание для анонса</label>
                    <textarea id="anons_text" name="anons_text" rows="4"
                              class="form-control"
                              placeholder="Краткое описание для списка новостей или анонсов"><?= esc($page['anons_text'] ?? '') ?></textarea>
                    <small>Отображается в списках страниц</small>
                </div>
            </div>

            <!-- ======================================== -->
            <!-- ВРЕМЯ СОЗДАНИЯ И ИЗМЕНЕНИЯ -->
            <!-- ======================================== -->

            <?php if (isset($page)): ?>
                <div class="settings-section">
                    <div class="form-group">
                        <label>📅 Время создания</label>
                        <div class="form-control-static"><?= date('d.m.Y H:i:s', strtotime($page['create'])) ?></div>
                    </div>
                    <div class="form-group">
                        <label>🔄 Время изменения</label>
                        <div class="form-control-static"><?= date('d.m.Y H:i:s', strtotime($page['modify'])) ?></div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- ======================================== -->
            <!-- КНОПКИ -->
            <!-- ======================================== -->

            <div class="form-actions">
                <a href="/admin-panel/pages<?= (isset($parent_id) && $parent_id > 0) ? '?parent=' . $parent_id : '' ?>" class="btn-cancel">Отмена</a>
                <button type="submit" class="btn-save">💾 Сохранить страницу</button>
            </div>
        </form>
    </div>

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