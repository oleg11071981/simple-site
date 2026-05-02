<?= $this->extend('site/layouts/base') ?>

<?= $this->section('content') ?>

    <!-- Заголовок на сером фоне -->
    <div class="page-header">
        <h1 class="page-title"><?= esc($page['name']) ?></h1>
    </div>

    <!-- Вложенные страницы (меню-навигация) -->
<?php if (!empty($childrenTree)): ?>
    <div class="subpages-section">
        <?= view('site/partials/subpages_nav', ['pages' => $childrenTree]) ?>
    </div>
<?php endif; ?>

    <!-- Контент в белой карточке -->
<?php if (!empty($page['more_info'])): ?>
    <div class="page-card">
        <div class="page-text">
            <?= $page['more_info'] ?>
        </div>
    </div>
<?php endif; ?>

    <!-- Галерея -->
<?= view('site/partials/gallery', ['files' => $galleryFiles ?? []]) ?>

<?= $this->endSection() ?>