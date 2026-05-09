<?= $this->extend('site/layouts/base') ?>

<?= $this->section('content') ?>

    <article class="project-detail">
        <div class="page-header">
            <h1 class="page-title"><?= esc($project['name']) ?></h1>
            <?php if (!empty($project['anons_text'])): ?>
                <p class="page-description"><?= esc($project['anons_text']) ?></p>
            <?php endif; ?>
        </div>

        <?php if (!empty($project['foto_file'])): ?>
            <div class="project-detail-image">
                <img src="/uploads/<?= $project['foto_file'] ?>" alt="<?= esc($project['name']) ?>">
            </div>
        <?php endif; ?>

        <!-- Информационные блоки -->
        <div class="project-info-grid">
            <?php if (!empty($project['organizing_committee'])): ?>
                <div class="info-card">
                    <h3 class="info-card-title">👥 Оргкомитет</h3>
                    <div class="info-card-content">
                        <?= nl2br(esc($project['organizing_committee'])) ?>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (!empty($project['supported_by'])): ?>
                <div class="info-card">
                    <h3 class="info-card-title">🤝 Проводится при поддержке</h3>
                    <div class="info-card-content">
                        <?= nl2br(esc($project['supported_by'])) ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Мероприятия проекта (сетка 3 колонки) -->
        <?php if (!empty($events)): ?>
            <div class="project-events-section">
                <h2 class="section-title">Мероприятия проекта</h2>
                <div class="events-grid">
                    <?php foreach ($events as $event): ?>
                        <div class="event-card">
                            <?php if (!empty($event['foto_file'])): ?>
                                <div class="event-card-image">
                                    <img src="/uploads/<?= $event['foto_file'] ?>" alt="<?= esc($event['name']) ?>">
                                </div>
                            <?php else: ?>
                                <div class="event-card-image event-card-image-placeholder">
                                    <span>📅</span>
                                </div>
                            <?php endif; ?>
                            <div class="event-card-content">
                                <div class="event-card-date">
                                    <?= date('d.m.Y', strtotime($event['date_start'])) ?>
                                    <?php if (!empty($event['date_end']) && $event['date_end'] != $event['date_start']): ?>
                                        – <?= date('d.m.Y', strtotime($event['date_end'])) ?>
                                    <?php endif; ?>
                                </div>
                                <h3 class="event-card-title">
                                    <a href="/projects/<?= esc($project['path']) ?>/<?= esc($event['path']) ?>">
                                        <?= esc($event['name']) ?>
                                    </a>
                                </h3>
                                <?php if (!empty($event['location'])): ?>
                                    <div class="event-card-location">📍 <?= esc($event['location']) ?></div>
                                <?php endif; ?>
                                <?php if (!empty($event['anons_text'])): ?>
                                    <p class="event-card-excerpt"><?= esc(substr(strip_tags($event['anons_text']), 0, 100)) ?>...</p>
                                <?php endif; ?>
                                <a href="/projects/<?= esc($project['path']) ?>/<?= esc($event['path']) ?>" class="read-more">
                                    Подробнее →
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Галерея проекта -->
        <?= view('site/partials/gallery', ['files' => $galleryFiles ?? []]) ?>
    </article>

<?= $this->endSection() ?>