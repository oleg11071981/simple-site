<?= $this->extend('site/layouts/base') ?>

<?= $this->section('content') ?>

    <article class="event-detail">
        <div class="page-header">
            <h1 class="page-title"><?= esc($event['name']) ?></h1>
            <?php if (!empty($event['anons_text'])): ?>
                <p class="page-description"><?= esc($event['anons_text']) ?></p>
            <?php endif; ?>
        </div>

        <!-- Информация о мероприятии -->
        <div class="event-info-bar">
            <div class="event-info-item">
                <span class="event-info-icon">📅</span>
                <div class="event-info-content">
                    <div class="event-info-label">Дата проведения</div>
                    <div class="event-info-value">
                        <?= date('d.m.Y', strtotime($event['date_start'])) ?>
                        <?php if (!empty($event['date_end']) && $event['date_end'] != $event['date_start']): ?>
                            – <?= date('d.m.Y', strtotime($event['date_end'])) ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <?php if (!empty($event['location'])): ?>
                <div class="event-info-item">
                    <span class="event-info-icon">📍</span>
                    <div class="event-info-content">
                        <div class="event-info-label">Место проведения</div>
                        <div class="event-info-value"><?= esc($event['location']) ?></div>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (!empty($event['link'])): ?>
                <div class="event-info-item">
                    <span class="event-info-icon">🔗</span>
                    <div class="event-info-content">
                        <div class="event-info-label">Ссылка</div>
                        <div class="event-info-value">
                            <a href="<?= esc($event['link']) ?>" target="_blank" rel="noopener noreferrer">
                                <?= esc($event['link']) ?>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <?php if (!empty($event['foto_file'])): ?>
            <div class="event-detail-image">
                <img src="/uploads/<?= $event['foto_file'] ?>" alt="<?= esc($event['name']) ?>">
            </div>
        <?php endif; ?>

        <!-- Полное описание -->
        <?php if (!empty($event['description'])): ?>
            <div class="event-card">
                <div class="event-description">
                    <?= $event['description'] ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Галерея мероприятия -->
        <?= view('site/partials/gallery', ['files' => $galleryFiles ?? []]) ?>

        <!-- Другие мероприятия проекта -->
        <?php if (!empty($otherEvents)): ?>
            <div class="other-events">
                <h2 class="other-events-title">Другие мероприятия проекта «<?= esc($project['name']) ?>»</h2>
                <div class="other-events-grid">
                    <?php foreach ($otherEvents as $item): ?>
                        <a href="/projects/<?= esc($project['path']) ?>/<?= esc($item['path']) ?>" class="other-event-card">
                            <?php if (!empty($item['foto_file'])): ?>
                                <div class="other-event-image">
                                    <img src="/uploads/<?= $item['foto_file'] ?>" alt="<?= esc($item['name']) ?>">
                                </div>
                            <?php else: ?>
                                <div class="other-event-image-placeholder">📅</div>
                            <?php endif; ?>
                            <div class="other-event-content">
                                <div class="other-event-date"><?= date('d.m.Y', strtotime($item['date_start'])) ?></div>
                                <div class="other-event-name"><?= esc($item['name']) ?></div>
                                <?php if (!empty($item['location'])): ?>
                                    <div class="other-event-location">📍 <?= esc($item['location']) ?></div>
                                <?php endif; ?>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Ссылка назад к проекту -->
        <div class="back-to-project">
            <a href="/projects/<?= esc($project['path']) ?>" class="back-link">
                ← Вернуться к проекту «<?= esc($project['name']) ?>»
            </a>
        </div>
    </article>

<?= $this->endSection() ?>