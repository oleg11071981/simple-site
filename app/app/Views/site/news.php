<?= $this->extend('site/layouts/base') ?>

<?= $this->section('content') ?>

    <!-- Заголовок на сером фоне -->
    <div class="page-header">
        <h1 class="page-title">Новости</h1>
    </div>

    <!-- Фильтр по категориям -->
    <div class="news-filter">
        <a href="/news" class="filter-btn <?= ($activeCategory ?? 0) == 0 ? 'active' : '' ?>">Все новости</a>
        <a href="/news?category=1" class="filter-btn <?= ($activeCategory ?? 0) == 1 ? 'active' : '' ?>">📋 Новости комитета</a>
        <a href="/news?category=2" class="filter-btn <?= ($activeCategory ?? 0) == 2 ? 'active' : '' ?>">🌍 Новости в РФ и мире</a>
    </div>

    <!-- Фильтр по дате -->
    <div class="date-filter">
        <form method="get" action="/news" class="date-filter-form">
            <!-- Сохраняем текущую категорию -->
            <?php if (($activeCategory ?? 0) > 0): ?>
                <input type="hidden" name="category" value="<?= $activeCategory ?>">
            <?php endif; ?>

            <div class="date-inputs">
                <input type="date"
                       name="date_from"
                       value="<?= esc($_GET['date_from'] ?? '') ?>"
                       placeholder="с"
                       class="date-input">
                <span class="date-separator">—</span>
                <input type="date"
                       name="date_to"
                       value="<?= esc($_GET['date_to'] ?? '') ?>"
                       placeholder="по"
                       class="date-input">
                <button type="submit" class="filter-apply-btn">Применить</button>
                <a href="/news?<?= ($activeCategory ?? 0) > 0 ? 'category=' . $activeCategory : '' ?>" class="filter-clear-btn">Сбросить</a>
            </div>
        </form>
    </div>

    <!-- Список новостей (карточки на белом фоне) -->
    <div class="news-grid">
        <?php if (!empty($news) && is_array($news)): ?>
            <?php foreach ($news as $item): ?>
                <article class="news-card">
                    <?php if (!empty($item['foto_file'])): ?>
                        <div class="news-image">
                            <img src="/uploads/<?= $item['foto_file'] ?>" alt="<?= esc($item['name']) ?>">
                        </div>
                    <?php else: ?>
                        <div class="news-image">📰</div>
                    <?php endif; ?>
                    <div class="news-content">
                        <div class="news-meta">
                            <span class="news-date"><?= date('d.m.Y', strtotime($item['date'])) ?></span>
                            <?php if ($item['category_news'] == 1): ?>
                                <span class="news-category committee">📋 Новости комитета</span>
                            <?php elseif ($item['category_news'] == 2): ?>
                                <span class="news-category world">🌍 Новости в РФ и мире</span>
                            <?php endif; ?>
                        </div>
                        <h3 class="news-title"><?= esc($item['name']) ?></h3>
                        <p class="news-excerpt"><?= esc(substr(strip_tags($item['anons_text']), 0, 150)) ?>...</p>
                        <a href="/news/<?= esc($item['path']) ?>" class="read-more">Читать подробнее →</a>
                    </div>
                </article>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="empty-news">
                <p>Новости не найдены</p>
            </div>
        <?php endif; ?>
    </div>

    <!-- Пагинация -->
<?php if (isset($pager) && $pager->getPageCount() > 1): ?>
    <div class="pagination">
        <?= $pager->links() ?>
    </div>
<?php endif; ?>

<?= $this->endSection() ?>