<?= $this->extend('site/layouts/base') ?>

<?= $this->section('content') ?>

    <div class="page-header">
        <h1 class="page-title"><?= ($currentLang ?? 'ru') === 'en' ? 'Projects' : 'Проекты' ?></h1>
    </div>

    <!-- Активные проекты (без заголовка) -->
<?php if (!empty($activeProjects)): ?>
    <div class="projects-grid">
        <?php foreach ($activeProjects as $project): ?>
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
<?php endif; ?>

    <!-- Завершённые проекты -->
<?php if (!empty($completedProjects)): ?>
    <section class="projects-section">
        <h2 class="section-title"><?= ($currentLang ?? 'ru') === 'en' ? 'Completed projects' : 'Завершённые проекты' ?></h2>
        <div class="projects-grid">
            <?php foreach ($completedProjects as $project): ?>
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
    </section>
<?php endif; ?>

    <!-- Если нет ни одного проекта -->
<?php if (empty($activeProjects) && empty($completedProjects)): ?>
    <div class="empty-projects">
        <div class="empty-projects-icon">📁</div>
        <h3 class="empty-projects-title"><?= ($currentLang ?? 'ru') === 'en' ? 'Projects not found' : 'Проекты не найдены' ?></h3>
        <p class="empty-projects-text"><?= ($currentLang ?? 'ru') === 'en' ? 'There are no projects at the moment' : 'В данный момент нет проектов' ?></p>
    </div>
<?php endif; ?>

<?= $this->endSection() ?>