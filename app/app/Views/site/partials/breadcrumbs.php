<?php if (isset($currentPage) && !empty($currentPage)): ?>
    <nav class="breadcrumbs" aria-label="Хлебные крошки">
        <ul class="breadcrumbs-list">
            <li class="breadcrumbs-item">
                <a href="/" class="breadcrumbs-link">
                    <?= ($currentLang ?? 'ru') === 'en' ? 'Home' : 'Главная' ?>
                </a>
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