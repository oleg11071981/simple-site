<?= $this->extend('site/layouts/base') ?>

<?= $this->section('content') ?>

    <!-- Заголовок на сером фоне -->
    <div class="page-header">
        <h1 class="page-title">Новости</h1>
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
                        <div class="news-date"><?= date('d.m.Y', strtotime($item['date'])) ?></div>

                        <!-- Категория новости -->
                        <?php if ($item['category_news'] == 1): ?>
                            <div class="news-category committee">📋 Новости комитета</div>
                        <?php elseif ($item['category_news'] == 2): ?>
                            <div class="news-category world">🌍 Новости в РФ и мире</div>
                        <?php endif; ?>

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