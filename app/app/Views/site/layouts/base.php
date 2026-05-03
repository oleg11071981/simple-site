<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title><?= $title ?? 'Демо' ?></title>
    <?php if (!empty($description)): ?>
        <meta name="description" content="<?= esc($description) ?>">
    <?php endif; ?>
    <?php if (!empty($keywords)): ?>
        <meta name="keywords" content="<?= esc($keywords) ?>">
    <?php endif; ?>

    <?php
    // Cache busting: версия = время изменения файла
    $cssVersion = filemtime(FCPATH . 'css/site.css');
    $jsVersion = filemtime(FCPATH . 'js/site.js');
    ?>

    <link rel="stylesheet" href="/css/site.css?v=<?= $cssVersion ?>">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.css" />
</head>
<body>

<?= view('site/partials/header', ['menuPages' => $menuPages ?? [], 'activePage' => $activePage ?? '']) ?>

<main>
    <div class="container">
        <!-- Хлебные крошки -->
        <?php if (isset($currentPage) && !empty($currentPage)): ?>
            <nav class="breadcrumbs" aria-label="Хлебные крошки">
                <ul class="breadcrumbs-list">
                    <li class="breadcrumbs-item">
                        <a href="/" class="breadcrumbs-link">Главная</a>
                    </li>
                    <?php if (!empty($breadcrumbs)): ?>
                        <?php foreach ($breadcrumbs as $crumb): ?>
                            <li class="breadcrumbs-item">
                                <a href="<?= esc($crumb['url']) ?>" class="breadcrumbs-link">
                                    <?= esc($crumb['name']) ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    <li class="breadcrumbs-item">
                        <span class="breadcrumbs-current"><?= esc($currentPage) ?></span>
                    </li>
                </ul>
            </nav>
        <?php endif; ?>

        <?= $this->renderSection('content') ?>
    </div>
</main>

<?= view('site/partials/footer') ?>

<script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.umd.js"></script>
<script src="/js/site.js?v=<?= $jsVersion ?>"></script>
</body>
</html>