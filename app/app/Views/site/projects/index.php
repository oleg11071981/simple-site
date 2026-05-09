<?= $this->extend('site/layouts/base') ?>

<?= $this->section('content') ?>

    <div class="page-header">
        <h1 class="page-title">Проекты</h1>
    </div>

<?php if (!empty($projects)): ?>
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
                        <p class="project-excerpt"><?= esc(substr(strip_tags($project['anons_text']), 0, 120)) ?>...</p>
                    <?php endif; ?>

                    <div class="project-meta">
                        <?php if (!empty($project['date_start'])): ?>
                            <span class="project-date">
                                📅 <?= date('d.m.Y', strtotime($project['date_start'])) ?>
                                <?php if (!empty($project['date_end']) && $project['date_end'] != $project['date_start']): ?>
                                    – <?= date('d.m.Y', strtotime($project['date_end'])) ?>
                                <?php endif; ?>
                            </span>
                        <?php endif; ?>

                        <?php if ($project['events_count'] > 0): ?>
                            <span class="project-events-count">
                                📋 <?= $project['events_count'] ?> <?= declension($project['events_count'], 'мероприятие', 'мероприятия', 'мероприятий') ?>
                            </span>
                        <?php endif; ?>
                    </div>

                    <a href="/projects/<?= esc($project['path']) ?>" class="read-more">Подробнее о проекте →</a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <div class="empty-projects">
        <div class="empty-projects-icon">📁</div>
        <h3 class="empty-projects-title">Проекты не найдены</h3>
        <p class="empty-projects-text">В данный момент нет активных проектов</p>
    </div>
<?php endif; ?>

<?= $this->endSection() ?>