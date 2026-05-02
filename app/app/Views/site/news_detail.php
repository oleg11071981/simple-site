<?= $this->extend('site/layouts/base') ?>

<?= $this->section('content') ?>

    <article class="news-detail">
        <div class="news-detail-header">
            <h1 class="news-detail-title"><?= esc($news['name']) ?></h1>
            <div class="news-detail-meta">
                <span class="news-detail-date">📅 <?= date('d.m.Y', strtotime($news['date'])) ?></span>
                <?php if (!empty($news['author'])): ?>
                    <span class="news-detail-author">✍️ <?= esc($news['author']) ?></span>
                <?php endif; ?>
            </div>
        </div>

        <?php if (!empty($news['foto_file'])): ?>
            <div class="news-detail-image">
                <img src="/uploads/<?= $news['foto_file'] ?>" alt="<?= esc($news['name']) ?>">
            </div>
        <?php endif; ?>

        <div class="news-detail-card">
            <div class="news-detail-content">
                <?= $news['more_info'] ?>
            </div>
        </div>

        <!-- Галерея -->
        <?= view('site/partials/gallery', ['files' => $galleryFiles ?? []]) ?>

        <!-- Другие новости -->
        <?php if (!empty($otherNews)): ?>
            <div class="other-news">
                <h2 class="other-news-title">Читайте также</h2>
                <div class="other-news-grid">
                    <?php foreach ($otherNews as $item): ?>
                        <a href="/news/<?= esc($item['path']) ?>" class="other-news-card">
                            <div class="other-news-image">
                                <?php if (!empty($item['foto_file'])): ?>
                                    <img src="/uploads/<?= $item['foto_file'] ?>" style="width: 50px; height: 50px; object-fit: cover; border-radius: 8px;">
                                <?php else: ?>
                                    📰
                                <?php endif; ?>
                            </div>
                            <div>
                                <div class="other-news-date"><?= date('d.m.Y', strtotime($item['date'])) ?></div>
                                <div class="other-news-name"><?= esc($item['name']) ?></div>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </article>

<?= $this->endSection() ?>