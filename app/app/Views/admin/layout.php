<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <title><?= $title ?? 'Админ-панель' ?> | n-cms</title>
    <link rel="stylesheet" href="/admin/css/main.css">
    <!-- Cropper.js для редактирования изображений -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css">
    <?php if (isset($additionalCss)): ?>
        <!-- дополнительный CSS уже не нужен, но оставляем для обратной совместимости -->
    <?php endif; ?>
    <!-- Подключаем CKEditor -->
    <script src="/admin/ckeditor/ckeditor.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
</head>
<body>
<!-- Шапка -->
<div class="header">
    <div class="logo">
        <h1>n-cms</h1>
        <p>Система управления сайтом</p>
    </div>

    <!-- Десктопное меню -->
    <div class="desktop-menu">
        <a href="/admin-panel/dashboard" class="<?= $activeMenu === 'dashboard' ? 'active' : '' ?>">📊 Дашборд</a>
        <a href="/admin-panel/pages" class="<?= $activeMenu === 'pages' ? 'active' : '' ?>">📄 Страницы</a>
        <a href="/admin-panel/news" class="<?= $activeMenu === 'news' ? 'active' : '' ?>">📰 Новости</a>
        <a href="/admin-panel/files" class="<?= $activeMenu === 'files' ? 'active' : '' ?>">📁 Файлы</a>
        <a href="/admin-panel/categories" class="<?= $activeMenu === 'categories' ? 'active' : '' ?>">📂 Категории</a>
        <a href="/admin-panel/settings" class="<?= $activeMenu === 'settings' ? 'active' : '' ?>">⚙️ Настройки</a>
        <a href="/admin-panel/logout" class="logout-link">🚪 Выйти</a>
    </div>

    <!-- Бургер меню (мобильные) -->
    <div class="burger-menu" id="burgerMenu">
        <span></span>
        <span></span>
        <span></span>
    </div>
</div>

<!-- Боковое меню (мобильное) -->
<div class="sidebar" id="sidebar">
    <ul class="sidebar-menu">
        <li><a href="/admin-panel/dashboard">📊 Дашборд</a></li>
        <li><a href="/admin-panel/pages">📄 Страницы</a></li>
        <li><a href="/admin-panel/news">📰 Новости</a></li>
        <li><a href="/admin-panel/files">📁 Файлы</a></li>
        <li><a href="/admin-panel/categories">📂 Категории</a></li>
        <li><a href="/admin-panel/settings">⚙️ Настройки</a></li>
        <li><a href="/admin-panel/logout">🚪 Выйти</a></li>
    </ul>
</div>

<!-- Затемнение -->
<div class="overlay" id="overlay"></div>

<!-- Основной контент -->
<div class="container">
    <?= $this->renderSection('content') ?>
</div>

<script src="/admin/js/main.js"></script>
<?php if (isset($additionalJs)): ?>
    <script src="<?= $additionalJs ?>"></script>
<?php endif; ?>

<!-- Инициализация CKEditor только если нет дополнительного JS -->
<?php if (!isset($additionalJs) || (strpos($additionalJs, 'pages.js') === false && strpos($additionalJs, 'settings.js') === false && strpos($additionalJs, 'files.js') === false)): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof CKEDITOR !== 'undefined' && document.getElementById('MainText')) {
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
                    ]
                });
            }
        });
    </script>
<?php endif; ?>
</body>
</html>