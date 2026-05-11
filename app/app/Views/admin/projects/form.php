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

        <form action="<?= isset($project) ? '/admin-panel/projects/update/' . $project['id'] : '/admin-panel/projects/store' ?>" method="post" class="settings-form">
            <?= csrf_field() ?>

            <?php if (isset($project)): ?>
                <input type="hidden" name="id" value="<?= $project['id'] ?>">
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
                               required>
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
                        <label for="anons_text">Краткое описание (Русский)</label>
                        <textarea id="anons_text" name="anons_text" rows="4"
                                  class="form-control"
                                  placeholder="Краткое описание проекта"><?= esc($project['anons_text'] ?? '') ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="organizing_committee">Оргкомитет (Русский)</label>
                        <textarea id="organizing_committee" name="organizing_committee" rows="6"
                                  class="form-control"
                                  placeholder="Состав организационного комитета"><?= esc($project['organizing_committee'] ?? '') ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="supported_by">Проводится при поддержке (Русский)</label>
                        <textarea id="supported_by" name="supported_by" rows="4"
                                  class="form-control"
                                  placeholder="Партнёры и спонсоры"><?= esc($project['supported_by'] ?? '') ?></textarea>
                    </div>
                </div>

                <div class="settings-section">
                    <h2>SEO настройки (Русский)</h2>

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

            <!-- ======================================== -->
            <!-- ВКЛАДКА ENGLISH -->
            <!-- ======================================== -->

            <div id="tab-en" class="tab-content">
                <div class="settings-section">
                    <h2>Basic Information (English)</h2>

                    <?php if (isset($project)): ?>
                        <div class="form-group">
                            <label>Project ID</label>
                            <div class="form-control-static"><?= $project['id'] ?></div>
                        </div>
                    <?php endif; ?>

                    <div class="form-group">
                        <label for="name_en">Project Title <span class="required">*</span></label>
                        <input type="text" id="name_en" name="name_en"
                               value="<?= esc($project['name_en'] ?? '') ?>"
                               class="form-control"
                               placeholder="Enter project title">
                    </div>

                    <div class="form-group">
                        <label for="anons_text_en">Short description (English)</label>
                        <textarea id="anons_text_en" name="anons_text_en" rows="4"
                                  class="form-control"
                                  placeholder="Short project description"><?= esc($project['anons_text_en'] ?? '') ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="organizing_committee_en">Organizing committee (English)</label>
                        <textarea id="organizing_committee_en" name="organizing_committee_en" rows="6"
                                  class="form-control"
                                  placeholder="Organizing committee members"><?= esc($project['organizing_committee_en'] ?? '') ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="supported_by_en">Supported by (English)</label>
                        <textarea id="supported_by_en" name="supported_by_en" rows="4"
                                  class="form-control"
                                  placeholder="Partners and sponsors"><?= esc($project['supported_by_en'] ?? '') ?></textarea>
                    </div>
                </div>

                <div class="settings-section">
                    <h2>SEO Settings (English)</h2>

                    <div class="form-group">
                        <label for="keywords_en">Keywords</label>
                        <textarea id="keywords_en" name="keywords_en" rows="3"
                                  class="form-control"
                                  placeholder="Keywords separated by commas"><?= esc($project['keywords_en'] ?? '') ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="description_en">Meta Description</label>
                        <textarea id="description_en" name="description_en" rows="4"
                                  class="form-control"
                                  placeholder="Brief description for search engines"><?= esc($project['description_en'] ?? '') ?></textarea>
                        <small>Recommended length: 150-160 characters</small>
                    </div>
                </div>
            </div>

            <!-- ======================================== -->
            <!-- ОБЩИЕ НАСТРОЙКИ (НЕ ЗАВИСЯТ ОТ ЯЗЫКА) -->
            <!-- ======================================== -->

            <div class="settings-section">
                <h2>Даты проекта / Project Dates</h2>

                <div class="form-row">
                    <div class="form-group half">
                        <label for="date_start">Дата начала / Start date</label>
                        <input type="date" id="date_start" name="date_start"
                               value="<?= esc($project['date_start'] ?? '') ?>"
                               class="form-control">
                    </div>
                    <div class="form-group half">
                        <label for="date_end">Дата окончания / End date</label>
                        <input type="date" id="date_end" name="date_end"
                               value="<?= esc($project['date_end'] ?? '') ?>"
                               class="form-control">
                    </div>
                </div>
            </div>

            <div class="settings-section">
                <h2>Изображения / Images</h2>

                <div class="form-group">
                    <label for="foto">Главное изображение / Main image</label>
                    <div class="foto-preview" id="fotoPreview">
                        <?php if (isset($project) && $project['foto'] > 0 && !empty($project['foto_file'])): ?>
                            <img src="/uploads/<?= $project['foto_file'] ?>" style="max-width: 200px; border-radius: 8px;">
                        <?php else: ?>
                            <div class="foto-placeholder" style="width: 200px; height: 150px; background: #f8f9fa; display: flex; align-items: center; justify-content: center; border-radius: 8px; border: 1px dashed #dee2e6;">
                                <span style="color: #6c757d;">Нет изображения / No image</span>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="foto-actions" style="margin-top: 10px;">
                        <input type="hidden" name="foto" id="foto" value="<?= esc($project['foto'] ?? 0) ?>">
                        <input type="hidden" name="foto_file" id="foto_file" value="<?= esc($project['foto_file'] ?? '') ?>">
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
                                        <?= (isset($project) && ($project['media'] ?? 0) == $cat['id']) ? 'selected' : '' ?>>
                                        <?= str_repeat('—', $cat['level'] ?? 0) ?> 📁 <?= esc($cat['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    <small>Выберите галерею для отображения на странице проекта / Select gallery to display on project page</small>
                </div>
            </div>

            <div class="settings-section">
                <h2>Настройки отображения / Display Settings</h2>

                <div class="form-row">
                    <div class="form-group half">
                        <label for="priority">Приоритет / Priority (порядок сортировки)</label>
                        <input type="number" id="priority" name="priority"
                               value="<?= esc($project['priority'] ?? 0) ?>"
                               class="form-control">
                        <small>Чем меньше число, тем выше в списке / Smaller number = higher position</small>
                    </div>
                    <div class="form-group half">
                        <label for="publish">Статус / Status</label>
                        <select id="publish" name="publish" class="form-control">
                            <option value="0" <?= (isset($project) && $project['publish'] == 0) ? 'selected' : '' ?>>Черновик / Draft</option>
                            <option value="1" <?= (isset($project) && $project['publish'] == 1) ? 'selected' : '' ?>>Опубликовано / Published</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Время создания/изменения -->
            <?php if (isset($project)): ?>
                <div class="settings-section">
                    <div class="form-group">
                        <label>📅 Время создания / Created</label>
                        <div class="form-control-static"><?= date('d.m.Y H:i:s', strtotime($project['create'])) ?></div>
                    </div>
                    <div class="form-group">
                        <label>🔄 Время изменения / Modified</label>
                        <div class="form-control-static"><?= date('d.m.Y H:i:s', strtotime($project['modify'])) ?></div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Кнопки -->
            <div class="form-actions">
                <a href="/admin-panel/projects" class="btn-cancel">Отмена / Cancel</a>
                <button type="submit" class="btn-save">💾 Сохранить проект / Save project</button>
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