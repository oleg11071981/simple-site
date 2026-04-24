<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>

    <div class="form-container">
        <div class="page-header">
            <h1><?= isset($category) ? 'Редактирование категории' : 'Создание категории' ?></h1>
            <p><?= isset($category) ? 'Редактирование «' . esc($category['name']) . '»' : 'Добавление новой категории для файлов' ?></p>
        </div>

        <!-- Flash сообщения -->
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
        <form action="<?= isset($category) ? '/admin-panel/categories/update/' . $category['id'] : '/admin-panel/categories/store' ?>" method="post" class="settings-form">
            <?= csrf_field() ?>

            <?php if (isset($category)): ?>
                <input type="hidden" name="id" value="<?= $category['id'] ?>">
            <?php endif; ?>

            <div class="settings-section">
                <h2>Информация о категории</h2>

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
                <a href="/admin-panel/categories" class="btn-cancel">Отмена</a>
                <button type="submit" class="btn-save">💾 <?= isset($category) ? 'Сохранить' : 'Создать' ?></button>
            </div>
        </form>
    </div>

<?= $this->endSection() ?>