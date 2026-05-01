<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>

    <div class="form-container">
        <div class="page-header">
            <h1><?= isset($category) ? 'Редактирование категории' : 'Создание категории' ?></h1>
            <p><?= isset($category) ? 'Редактирование «' . esc($category['name']) . '»' : 'Добавление новой категории для файлов' ?></p>
        </div>

        <?php if (session()->getFlashdata('errors')): ?>
            <div class="alert alert-error">
                <?php foreach (session()->getFlashdata('errors') as $error): ?>
                    <div>⚠ <?= esc($error) ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form action="<?= isset($category) ? '/admin-panel/categories/update/' . $category['id'] : '/admin-panel/categories/store' ?>" method="post" class="settings-form">
            <?= csrf_field() ?>

            <?php if (isset($category)): ?>
                <input type="hidden" name="id" value="<?= $category['id'] ?>">
            <?php endif; ?>

            <div class="settings-section">
                <h2>Основная информация</h2>

                <?php if (isset($category)): ?>
                    <div class="form-group">
                        <label>ID категории</label>
                        <div class="form-control-static"><?= $category['id'] ?></div>
                    </div>
                <?php endif; ?>

                <div class="form-group">
                    <label for="name">Название категории <span class="required">*</span></label>
                    <input type="text" id="name" name="name"
                           value="<?= esc($category['name'] ?? '') ?>"
                           class="form-control"
                           placeholder="Введите название категории"
                           required autofocus>
                    <small>Например: Изображения, Документы, Баннеры и т.д.</small>
                </div>

                <div class="form-group">
                    <label for="parent">Родительская категория</label>
                    <select id="parent" name="parent" class="form-control">
                        <option value="0">— Корневая категория —</option>
                        <?php if (!empty($categories)): ?>
                            <?php foreach ($categories as $cat): ?>
                                <?php if (isset($category) && $category['id'] == $cat['id']) continue; ?>
                                <option value="<?= $cat['id'] ?>"
                                    <?= (isset($category) && $category['parent'] == $cat['id']) ? 'selected' : '' ?>
                                    <?= (isset($parent_id) && $parent_id == $cat['id'] && !isset($category)) ? 'selected' : '' ?>>
                                    <?= str_repeat('—', $cat['level'] ?? 0) ?> <?= esc($cat['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                    <small>Выберите родительскую категорию для создания вложенности</small>
                </div>

                <div class="form-group">
                    <label for="priority">Приоритет (порядок сортировки)</label>
                    <input type="number" id="priority" name="priority"
                           value="<?= esc($category['priority'] ?? 0) ?>"
                           class="form-control">
                    <small>Чем меньше число, тем выше в списке</small>
                </div>

                <div class="form-group">
                    <label for="description">Описание категории</label>
                    <textarea id="description" name="description" rows="4"
                              class="form-control"
                              placeholder="Описание категории (необязательно)"><?= esc($category['description'] ?? '') ?></textarea>
                </div>
            </div>

            <?php if (isset($category)): ?>
                <div class="settings-section">
                    <div class="form-group">
                        <label>📅 Время создания</label>
                        <div class="form-control-static"><?= date('d.m.Y H:i:s', strtotime($category['create'])) ?></div>
                    </div>
                    <div class="form-group">
                        <label>🔄 Время изменения</label>
                        <div class="form-control-static"><?= date('d.m.Y H:i:s', strtotime($category['modify'])) ?></div>
                    </div>
                </div>
            <?php endif; ?>

            <div class="form-actions">
                <a href="/admin-panel/categories<?= (isset($parent_id) && $parent_id > 0) ? '?parent=' . $parent_id : '' ?>" class="btn-cancel">Отмена</a>
                <button type="submit" class="btn-save">💾 <?= isset($category) ? 'Сохранить' : 'Создать' ?></button>
            </div>
        </form>
    </div>

<?= $this->endSection() ?>