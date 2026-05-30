<?= $this->extend('site/layouts/base') ?>

<?= $this->section('content') ?>

    <!-- Блок последних новостей (сверху) -->
<?php if (!empty($latestNews)): ?>
    <section class="news-section">
        <h2 class="section-title"><?= ($currentLang ?? 'ru') === 'en' ? 'Latest news' : 'Последние новости' ?></h2>
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
                        <a href="/news/<?= esc($item['path']) ?>" class="read-more">
                            <?= ($currentLang ?? 'ru') === 'en' ? 'Details →' : 'Подробнее →' ?>
                        </a>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
        <div class="section-footer">
            <a href="/news" class="all-link"><?= ($currentLang ?? 'ru') === 'en' ? 'All news →' : 'Все новости →' ?></a>
        </div>
    </section>
<?php endif; ?>

    <!-- Блок проектов (снизу) -->
<?php if (!empty($projects)): ?>
    <section class="projects-section">
        <h2 class="section-title"><?= ($currentLang ?? 'ru') === 'en' ? 'Our projects' : 'Наши проекты' ?></h2>
        <div class="projects-grid">
            <?php foreach ($projects as $project): ?>
                <div class="project-card">
                    <?php if (!empty($project['foto_file'])): ?>
                        <div class="project-image">
                            <img src="/uploads/<?= $project['foto_file'] ?>" alt="<?= esc($project['name']) ?>">
                        </div>
                    <?php else: ?>
                        <div class="project-image project-image-placeholder">
                            <span>📁</span>
                        </div>
                    <?php endif; ?>

                    <div class="project-content">
                        <h3 class="project-title">
                            <a href="/projects/<?= esc($project['path']) ?>"><?= esc($project['name']) ?></a>
                        </h3>

                        <?php if (!empty($project['anons_text'])): ?>
                            <p class="project-excerpt"><?= esc(substr(strip_tags($project['anons_text']), 0, 100)) ?>...</p>
                        <?php endif; ?>

                        <div class="project-meta">
                            <?php if ($project['events_count'] > 0): ?>
                                <span class="project-events-count">
                                        📋 <?= $project['events_count'] ?>
                                    <?php if (($currentLang ?? 'ru') === 'en'): ?>
                                        <?= $project['events_count'] == 1 ? 'event' : 'events' ?>
                                    <?php else: ?>
                                        <?= declension($project['events_count'], 'мероприятие', 'мероприятия', 'мероприятий') ?>
                                    <?php endif; ?>
                                    </span>
                            <?php endif; ?>
                        </div>

                        <a href="/projects/<?= esc($project['path']) ?>" class="read-more">
                            <?= ($currentLang ?? 'ru') === 'en' ? 'More about project →' : 'Подробнее о проекте →' ?>
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="section-footer">
            <a href="/projects" class="all-link"><?= ($currentLang ?? 'ru') === 'en' ? 'All projects →' : 'Все проекты →' ?></a>
        </div>
    </section>
<?php endif; ?>

<?= $this->endSection() ?>