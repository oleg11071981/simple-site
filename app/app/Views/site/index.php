<?= $this->extend('site/layouts/base') ?>

<?= $this->section('content') ?>

    <!-- Основной контент в белой карточке -->
    <!--div class="home-card">
        <div class="home-text">
            <?= $mainText ?>
        </div>
    </div-->

    <!-- Блок последних новостей -->
<?php if (!empty($latestNews)): ?>
    <section class="news-section">
        <h2 class="section-title">Последние новости</h2>
        <div class="news-grid">
            <?php foreach ($latestNews as $item): ?>
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
                            <?php if (!empty($item['category_name'])): ?>
                                <?php
                                $categoryClass = '';
                                if ($item['category_news'] == 1) {
                                    $categoryClass = 'committee';
                                } elseif ($item['category_news'] == 2) {
                                    $categoryClass = 'world';
                                }
                                ?>
                                <span class="news-category <?= $categoryClass ?>">
                                    <?= esc($item['category_name']) ?>
                                </span>
                            <?php endif; ?>
                        </div>
                        <h3 class="news-title"><?= esc($item['name']) ?></h3>
                        <p class="news-excerpt"><?= esc(substr(strip_tags($item['anons_text']), 0, 120)) ?>...</p>
                        <a href="/news/<?= esc($item['path']) ?>" class="read-more">Подробнее →</a>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </section>
<?php endif; ?>

<?= $this->endSection() ?>