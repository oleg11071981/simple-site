<?= $this->extend('site/layouts/base') ?>

<?= $this->section('content') ?>

    <!-- Заголовок на сером фоне -->
    <div class="page-header">
        <h1 class="page-title"><?= esc($page['name']) ?></h1>
    </div>

    <!-- Контент в белой карточке -->
    <div class="page-card">
        <div class="page-text">
            <?= $page['more_info'] ?>
        </div>
    </div>

<?= $this->endSection() ?>