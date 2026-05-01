<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>

    <div class="form-container">
        <div class="page-header">
            <h1><?= isset($file) ? 'Редактирование файла' : 'Загрузка файла' ?></h1>
            <p><?= isset($file) ? 'Редактирование «' . esc($file['name']) . '»' : 'Добавление нового файла на сайт' ?></p>
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

        <form action="<?= isset($file) ? '/admin-panel/files/update/' . $file['id'] : '/admin-panel/files/store' ?>"
              method="post"
              enctype="multipart/form-data"
              class="settings-form"
              id="fileForm">
            <?= csrf_field() ?>

            <?php if (isset($file)): ?>
                <input type="hidden" name="id" value="<?= $file['id'] ?>">
            <?php endif; ?>

            <div class="settings-section">
                <h2>Основная информация</h2>

                <?php if (isset($file)): ?>
                    <div class="form-group">
                        <label>ID файла</label>
                        <div class="form-control-static"><?= $file['id'] ?></div>
                    </div>
                <?php endif; ?>

                <div class="form-group">
                    <label for="name">Название файла <span class="required">*</span></label>
                    <input type="text" id="name" name="name"
                           value="<?= esc($file['name'] ?? '') ?>"
                           class="form-control"
                           placeholder="Введите название файла"
                           required>
                </div>

                <div class="form-group">
                    <label for="title">Подпись (для изображений)</label>
                    <input type="text" id="title" name="title"
                           value="<?= esc($file['title'] ?? '') ?>"
                           class="form-control"
                           placeholder="Подпись к изображению">
                </div>

                <div class="form-group">
                    <label for="priority">Приоритет сортировки</label>
                    <input type="number" id="priority" name="priority"
                           value="<?= esc($file['priority'] ?? 0) ?>"
                           class="form-control">
                    <small>Чем меньше число, тем выше в списке</small>
                </div>

                <div class="form-group">
                    <label for="category">Категория</label>
                    <div class="category-select-wrapper">
                        <input type="text"
                               id="categorySearch"
                               class="form-control"
                               placeholder="🔍 Поиск категории..."
                               autocomplete="off">
                        <select id="category" name="category" class="form-control" size="8" style="margin-top: 8px;">
                            <option value="0">— Без категории —</option>
                            <?php if (!empty($categories)): ?>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= $cat['id'] ?>"
                                            data-name="<?= esc(strtolower($cat['name'])) ?>"
                                        <?= (isset($file) && $file['category'] == $cat['id']) ? 'selected' : '' ?>
                                        <?= (isset($parent_id) && $parent_id == $cat['id'] && !isset($file)) ? 'selected' : '' ?>>
                                        <?= str_repeat('—', $cat['level'] ?? 0) ?> <?= esc($cat['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    <small>Выберите категорию для файла. Можно использовать поиск.</small>
                </div>
            </div>

            <div class="settings-section">
                <h2><?= isset($file) ? 'Информация о файле' : 'Загрузка файла' ?></h2>

                <?php if (isset($file) && in_array($file['file_type'], ['jpg', 'jpeg', 'png', 'gif', 'webp'])): ?>
                    <div class="form-group">
                        <label>Превью</label>
                        <div class="file-preview">
                            <img id="previewImage" src="/uploads/<?= $file['file_name'] ?>" style="max-width: 200px; max-height: 150px; border-radius: 8px;">
                        </div>
                        <div class="image-editor-buttons" style="margin-top: 15px; display: flex; gap: 10px;">
                            <button type="button" class="btn-edit-image" id="btnEditImage" style="background: #007bff; color: white; padding: 8px 16px; border: none; border-radius: 6px; cursor: pointer;">✂️ Редактировать изображение</button>
                        </div>
                    </div>

                    <!-- Модальное окно для редактирования изображения -->
                    <div id="cropModal" class="modal" style="display: none;">
                        <div class="modal-content">
                            <span class="modal-close">&times;</span>
                            <h3>✂️ Редактирование изображения</h3>
                            <div class="crop-container">
                                <img id="cropOriginalImage" src="/uploads/<?= $file['file_name'] ?>">
                            </div>
                            <div class="crop-controls">
                                <div class="crop-size-control">
                                    <label>Ш: <input type="number" id="cropWidth" placeholder="ширина"></label>
                                    <label>В: <input type="number" id="cropHeight" placeholder="высота"></label>
                                    <button type="button" id="applyCropSize" class="btn-apply" style="background: #28a745; padding: 4px 10px; font-size: 12px;">Применить</button>
                                </div>
                                <div class="crop-ratio-control">
                                    <label>Пропорция:
                                        <select id="cropRatio">
                                            <option value="0">Свободная</option>
                                            <option value="1">1:1</option>
                                            <option value="4/3">4:3</option>
                                            <option value="16/9">16:9</option>
                                            <option value="3/4">3:4</option>
                                        </select>
                                    </label>
                                </div>
                            </div>
                            <div class="crop-actions">
                                <button type="button" id="cropRotateLeft" class="btn-crop">↺ Влево</button>
                                <button type="button" id="cropRotateRight" class="btn-crop">↻ Вправо</button>
                                <button type="button" id="cropFlipHorizontal" class="btn-crop">⟷ Отразить по горизонтали</button>
                                <button type="button" id="cropFlipVertical" class="btn-crop">⟷ Отразить по вертикали</button>
                                <button type="button" id="cropReset" class="btn-crop">⟳ Сброс</button>
                            </div>
                            <div class="modal-actions">
                                <button type="button" id="cancelCrop" class="btn-cancel">Отмена</button>
                                <button type="button" id="saveCrop" class="btn-save">Сохранить</button>
                            </div>
                            <div id="cropMessage" class="crop-message"></div>
                        </div>
                    </div>
                <?php elseif (isset($file)): ?>
                    <div class="form-group">
                        <label>Тип файла</label>
                        <div class="form-control-static">
                            <span style="font-size: 30px;"><?= $file['icon'] ?></span>
                            <?= strtoupper($file['file_type']) ?>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="form-group">
                        <label for="userfile">Выберите файл <span class="required">*</span></label>
                        <input type="file" id="userfile" name="userfile" class="form-control" required>
                        <small>Поддерживаемые форматы: JPG, PNG, GIF, PDF, DOC, XLS, ZIP</small>
                    </div>
                <?php endif; ?>

                <?php if (isset($file)): ?>
                    <div class="form-group">
                        <label>Имя файла</label>
                        <div class="form-control-static"><?= esc($file['file_name']) ?></div>
                    </div>

                    <div class="form-group">
                        <label>Тип файла</label>
                        <div class="form-control-static"><?= strtoupper(esc($file['file_type'])) ?></div>
                    </div>

                    <div class="form-group">
                        <label>Размер</label>
                        <div class="form-control-static"><?= $file['size_formatted'] ?></div>
                    </div>

                    <?php if ($file['width'] > 0): ?>
                        <div class="form-group">
                            <label>Размеры изображения</label>
                            <div class="form-control-static"><?= $file['width'] ?> x <?= $file['height'] ?> px</div>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>

            <?php if (isset($file)): ?>
                <div class="settings-section">
                    <div class="form-group">
                        <label>📅 Время создания</label>
                        <div class="form-control-static"><?= date('d.m.Y H:i:s', strtotime($file['create'])) ?></div>
                    </div>
                    <div class="form-group">
                        <label>🔄 Время изменения</label>
                        <div class="form-control-static"><?= date('d.m.Y H:i:s', strtotime($file['modify'])) ?></div>
                    </div>
                </div>
            <?php endif; ?>

            <div class="form-actions">
                <a href="/admin-panel/files" class="btn-cancel">Отмена</a>
                <button type="submit" class="btn-save">💾 <?= isset($file) ? 'Сохранить' : 'Загрузить' ?></button>
            </div>
        </form>
    </div>

    <script>
        let cropper = null;

        document.addEventListener('DOMContentLoaded', function() {
            const btnEditImage = document.getElementById('btnEditImage');
            const modal = document.getElementById('cropModal');
            const closeModal = document.querySelector('.modal-close');
            const cancelCrop = document.getElementById('cancelCrop');
            const saveCrop = document.getElementById('saveCrop');
            const cropReset = document.getElementById('cropReset');
            const cropRotateLeft = document.getElementById('cropRotateLeft');
            const cropRotateRight = document.getElementById('cropRotateRight');
            const cropFlipHorizontal = document.getElementById('cropFlipHorizontal');
            const cropFlipVertical = document.getElementById('cropFlipVertical');
            const applyCropSize = document.getElementById('applyCropSize');
            const cropRatio = document.getElementById('cropRatio');
            const cropWidth = document.getElementById('cropWidth');
            const cropHeight = document.getElementById('cropHeight');

            const imageElement = document.getElementById('cropOriginalImage');

            if (btnEditImage && imageElement) {
                btnEditImage.addEventListener('click', function() {
                    modal.style.display = 'flex';
                    document.body.style.overflow = 'hidden';

                    if (imageElement.complete) {
                        initCropper();
                    } else {
                        imageElement.onload = initCropper;
                    }
                });
            }

            function initCropper() {
                if (cropper) {
                    cropper.destroy();
                }

                if (imageElement && typeof Cropper !== 'undefined') {
                    cropper = new Cropper(imageElement, {
                        aspectRatio: NaN,
                        viewMode: 1,
                        dragMode: 'crop',
                        cropBoxResizable: true,
                        cropBoxMovable: true,
                        zoomable: true,
                        rotatable: true,
                        scalable: true,
                        minContainerWidth: 300,
                        minContainerHeight: 200
                    });
                }
            }

            function closeModalFunc() {
                if (modal) modal.style.display = 'none';
                document.body.style.overflow = '';
                if (cropper) {
                    cropper.destroy();
                    cropper = null;
                }
            }

            if (closeModal) closeModal.addEventListener('click', closeModalFunc);
            if (cancelCrop) cancelCrop.addEventListener('click', closeModalFunc);

            if (cropReset) {
                cropReset.addEventListener('click', function() {
                    if (cropper) cropper.reset();
                });
            }

            if (cropRotateLeft) {
                cropRotateLeft.addEventListener('click', function() {
                    if (cropper) cropper.rotate(-90);
                });
            }

            if (cropRotateRight) {
                cropRotateRight.addEventListener('click', function() {
                    if (cropper) cropper.rotate(90);
                });
            }

            if (cropFlipHorizontal) {
                cropFlipHorizontal.addEventListener('click', function() {
                    if (cropper) {
                        let data = cropper.getData();
                        cropper.scaleX(data.scaleX === -1 ? 1 : -1);
                    }
                });
            }

            if (cropFlipVertical) {
                cropFlipVertical.addEventListener('click', function() {
                    if (cropper) {
                        let data = cropper.getData();
                        cropper.scaleY(data.scaleY === -1 ? 1 : -1);
                    }
                });
            }

            if (cropRatio) {
                cropRatio.addEventListener('change', function() {
                    let ratio = this.value;
                    if (cropper) {
                        cropper.setAspectRatio(ratio == 0 ? NaN : parseFloat(ratio));
                    }
                });
            }

            if (applyCropSize) {
                applyCropSize.addEventListener('click', function() {
                    let width = parseInt(cropWidth.value);
                    let height = parseInt(cropHeight.value);
                    if (width > 0 && height > 0 && cropper) {
                        cropper.setCropBoxData({ width: width, height: height });
                    } else {
                        alert('Введите корректные ширину и высоту');
                    }
                });
            }

            if (saveCrop) {
                saveCrop.addEventListener('click', function() {
                    if (cropper) {
                        const canvas = cropper.getCroppedCanvas();
                        const croppedImageData = canvas.toDataURL('image/jpeg', 0.9);
                        const messageDiv = document.getElementById('cropMessage');

                        if (messageDiv) {
                            messageDiv.textContent = '⏳ Сохранение...';
                            messageDiv.style.color = '#007bff';
                            messageDiv.style.display = 'block';
                        }

                        <?php if (isset($file)): ?>
                        fetch('/admin-panel/files/crop-image/<?= $file['id'] ?>', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: JSON.stringify({
                                image_data: croppedImageData,
                                width: canvas.width,
                                height: canvas.height
                            })
                        })
                            .then(response => response.json())
                            .then(data => {
                                if (messageDiv) {
                                    if (data.success) {
                                        messageDiv.textContent = '✅ Изображение успешно обновлено! Страница будет перезагружена...';
                                        messageDiv.style.color = '#28a745';
                                        setTimeout(() => {
                                            location.reload();
                                        }, 2000);
                                    } else {
                                        messageDiv.textContent = '❌ Ошибка: ' + (data.error || 'Неизвестная ошибка');
                                        messageDiv.style.color = '#dc3545';
                                    }
                                }
                            })
                            .catch(error => {
                                if (messageDiv) {
                                    messageDiv.textContent = '❌ Ошибка при сохранении: ' + error;
                                    messageDiv.style.color = '#dc3545';
                                }
                            });
                        <?php else: ?>
                        if (messageDiv) {
                            messageDiv.textContent = '❌ Функция доступна только для редактирования существующих файлов';
                            messageDiv.style.color = '#dc3545';
                            setTimeout(() => {
                                messageDiv.style.display = 'none';
                            }, 3000);
                        }
                        <?php endif; ?>
                    }
                });
            }
        });
    </script>

    <script>
        // Поиск по категориям
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('categorySearch');
            const selectEl = document.getElementById('category');

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
                        if (emptyOption && emptyOption.textContent !== '— Без категории —') {
                            emptyOption.textContent = '— Без категории —';
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