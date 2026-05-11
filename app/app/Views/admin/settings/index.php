<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>

    <div class="form-container">
        <div class="page-header">
            <h1>Настройки сайта / Site Settings</h1>
            <p>Управление параметрами конфигурации сайта / Manage site configuration parameters</p>
        </div>

        <!-- Flash сообщения -->
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

        <!-- Форма настроек -->
        <form action="/admin-panel/settings/save" method="post" class="settings-form">
            <?= csrf_field() ?>

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
                    <h2>Основные настройки (Русский)</h2>

                    <div class="form-group">
                        <label for="SiteName">Название сайта</label>
                        <input type="text" id="SiteName" name="SiteName"
                               value="<?= esc($settings['SiteName'] ?? '') ?>"
                               class="form-control"
                               placeholder="Введите название сайта">
                    </div>

                    <div class="form-group">
                        <label for="Slogan">Слоган</label>
                        <input type="text" id="Slogan" name="Slogan"
                               value="<?= esc($settings['Slogan'] ?? '') ?>"
                               class="form-control"
                               placeholder="Слоган сайта">
                    </div>

                    <div class="form-group">
                        <label for="MainText">Главный текст</label>
                        <textarea id="MainText" name="MainText" rows="15"
                                  class="form-control"
                                  placeholder="Введите основной текст сайта"><?= htmlspecialchars($settings['MainText'] ?? '') ?></textarea>
                        <small>Поддерживается HTML разметка. Используйте визуальный редактор.</small>
                    </div>
                </div>

                <div class="settings-section">
                    <h2>Контактная информация (Русский)</h2>

                    <div class="form-group">
                        <label for="Email">Email (публичный)</label>
                        <input type="email" id="Email" name="Email"
                               value="<?= esc($settings['Email'] ?? '') ?>"
                               class="form-control"
                               placeholder="example@mail.ru">
                    </div>

                    <div class="form-group">
                        <label for="AdminEmail">Email администратора</label>
                        <input type="email" id="AdminEmail" name="AdminEmail"
                               value="<?= esc($settings['AdminEmail'] ?? '') ?>"
                               class="form-control"
                               placeholder="admin@example.com">
                    </div>

                    <div class="form-group">
                        <label for="Phone">Телефон</label>
                        <input type="text" id="Phone" name="Phone"
                               value="<?= esc($settings['Phone'] ?? '') ?>"
                               class="form-control"
                               placeholder="+7 (999) 123-45-67">
                    </div>

                    <div class="form-group">
                        <label for="Adress">Адрес</label>
                        <input type="text" id="Adress" name="Adress"
                               value="<?= esc($settings['Adress'] ?? '') ?>"
                               class="form-control"
                               placeholder="г. Москва, ул. Примерная, д. 1">
                    </div>

                    <div class="form-group">
                        <label for="WorkSchedule">График работы</label>
                        <input type="text" id="WorkSchedule" name="WorkSchedule"
                               value="<?= esc($settings['WorkSchedule'] ?? '') ?>"
                               class="form-control"
                               placeholder="Пн-Пт 9:00 - 18:00">
                    </div>
                </div>

                <div class="settings-section">
                    <h2>SEO настройки (Русский)</h2>

                    <div class="form-group">
                        <label for="Keywords">Ключевые слова</label>
                        <textarea id="Keywords" name="Keywords" rows="3"
                                  class="form-control"
                                  placeholder="Ключевые слова через запятую"><?= esc($settings['Keywords'] ?? '') ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="Description">Описание</label>
                        <textarea id="Description" name="Description" rows="3"
                                  class="form-control"
                                  placeholder="SEO описание сайта"><?= esc($settings['Description'] ?? '') ?></textarea>
                    </div>
                </div>

                <div class="settings-section">
                    <h2>Счетчики и аналитика</h2>

                    <div class="form-group">
                        <label for="Counters">Код счетчиков</label>
                        <textarea id="Counters" name="Counters" rows="10"
                                  class="form-control"
                                  placeholder="Вставьте код счетчиков (Яндекс.Метрика, Google Analytics и др.)"><?= esc($settings['Counters'] ?? '') ?></textarea>
                        <small>JavaScript код будет вставлен перед закрывающим тегом body</small>
                    </div>
                </div>

                <div class="settings-section">
                    <h2>Дополнительные поля</h2>

                    <div class="form-group">
                        <label for="additional_field1">Дополнительное поле 1</label>
                        <input type="text" id="additional_field1" name="additional_field1"
                               value="<?= esc($settings['additional_field1'] ?? '') ?>"
                               class="form-control"
                               placeholder="Дополнительное поле">
                    </div>

                    <div class="form-group">
                        <label for="additional_field2">Дополнительное поле 2</label>
                        <input type="text" id="additional_field2" name="additional_field2"
                               value="<?= esc($settings['additional_field2'] ?? '') ?>"
                               class="form-control"
                               placeholder="Дополнительное поле">
                    </div>
                </div>
            </div>

            <!-- ======================================== -->
            <!-- ВКЛАДКА ENGLISH -->
            <!-- ======================================== -->

            <div id="tab-en" class="tab-content">
                <div class="settings-section">
                    <h2>Basic Settings (English)</h2>

                    <div class="form-group">
                        <label for="SiteName_en">Site Name</label>
                        <input type="text" id="SiteName_en" name="SiteName_en"
                               value="<?= esc($settings['SiteName_en'] ?? '') ?>"
                               class="form-control"
                               placeholder="Enter site name">
                    </div>

                    <div class="form-group">
                        <label for="Slogan_en">Slogan</label>
                        <input type="text" id="Slogan_en" name="Slogan_en"
                               value="<?= esc($settings['Slogan_en'] ?? '') ?>"
                               class="form-control"
                               placeholder="Site slogan">
                    </div>

                    <div class="form-group">
                        <label for="MainText_en">Main Text</label>
                        <textarea id="MainText_en" name="MainText_en" rows="15"
                                  class="form-control"
                                  placeholder="Enter main site text"><?= htmlspecialchars($settings['MainText_en'] ?? '') ?></textarea>
                        <small>HTML supported. Use visual editor.</small>
                    </div>
                </div>

                <div class="settings-section">
                    <h2>Contact Information (English)</h2>

                    <div class="form-group">
                        <label for="Email">Email (public)</label>
                        <input type="email" id="Email" name="Email"
                               value="<?= esc($settings['Email'] ?? '') ?>"
                               class="form-control"
                               placeholder="example@mail.ru">
                    </div>

                    <div class="form-group">
                        <label for="AdminEmail">Admin Email</label>
                        <input type="email" id="AdminEmail" name="AdminEmail"
                               value="<?= esc($settings['AdminEmail'] ?? '') ?>"
                               class="form-control"
                               placeholder="admin@example.com">
                    </div>

                    <div class="form-group">
                        <label for="Phone">Phone</label>
                        <input type="text" id="Phone" name="Phone"
                               value="<?= esc($settings['Phone'] ?? '') ?>"
                               class="form-control"
                               placeholder="+7 (999) 123-45-67">
                    </div>

                    <div class="form-group">
                        <label for="Adress">Address</label>
                        <input type="text" id="Adress" name="Adress"
                               value="<?= esc($settings['Adress'] ?? '') ?>"
                               class="form-control"
                               placeholder="Moscow, Example str., 1">
                    </div>

                    <div class="form-group">
                        <label for="WorkSchedule">Working Hours</label>
                        <input type="text" id="WorkSchedule" name="WorkSchedule"
                               value="<?= esc($settings['WorkSchedule'] ?? '') ?>"
                               class="form-control"
                               placeholder="Mon-Fri 9:00 - 18:00">
                    </div>
                </div>

                <div class="settings-section">
                    <h2>SEO Settings (English)</h2>

                    <div class="form-group">
                        <label for="Keywords_en">Keywords</label>
                        <textarea id="Keywords_en" name="Keywords_en" rows="3"
                                  class="form-control"
                                  placeholder="Keywords separated by commas"><?= esc($settings['Keywords_en'] ?? '') ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="Description_en">Description</label>
                        <textarea id="Description_en" name="Description_en" rows="3"
                                  class="form-control"
                                  placeholder="SEO description of the site"><?= esc($settings['Description_en'] ?? '') ?></textarea>
                    </div>
                </div>

                <!-- Счетчики и дополнительные поля не переводятся -->
                <div class="settings-section">
                    <h2>Counters & Analytics / Счетчики и аналитика</h2>

                    <div class="form-group">
                        <label for="Counters">Code / Код</label>
                        <textarea id="Counters" name="Counters" rows="10"
                                  class="form-control"
                                  placeholder="Вставьте код счетчиков (Яндекс.Метрика, Google Analytics и др.)"><?= esc($settings['Counters'] ?? '') ?></textarea>
                        <small>JavaScript код будет вставлен перед закрывающим тегом body</small>
                    </div>
                </div>

                <div class="settings-section">
                    <h2>Additional Fields / Дополнительные поля</h2>

                    <div class="form-group">
                        <label for="additional_field1">Additional Field 1 / Дополнительное поле 1</label>
                        <input type="text" id="additional_field1" name="additional_field1"
                               value="<?= esc($settings['additional_field1'] ?? '') ?>"
                               class="form-control"
                               placeholder="Additional field">
                    </div>

                    <div class="form-group">
                        <label for="additional_field2">Additional Field 2 / Дополнительное поле 2</label>
                        <input type="text" id="additional_field2" name="additional_field2"
                               value="<?= esc($settings['additional_field2'] ?? '') ?>"
                               class="form-control"
                               placeholder="Additional field">
                    </div>
                </div>
            </div>

            <!-- Кнопки -->
            <div class="form-actions">
                <button type="submit" class="btn-save">💾 Сохранить настройки / Save Settings</button>
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

        // CKEditor для русской версии
        if (typeof CKEDITOR !== 'undefined') {
            if (document.getElementById('MainText')) {
                CKEDITOR.replace('MainText', {
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
            if (document.getElementById('MainText_en')) {
                CKEDITOR.replace('MainText_en', {
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