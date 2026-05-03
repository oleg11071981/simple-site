<?= $this->extend('site/layouts/base') ?>

<?= $this->section('content') ?>

    <!-- Заголовок на сером фоне -->
    <div class="page-header">
        <h1 class="page-title">Новости</h1>
    </div>

    <!-- Кнопка поиска (по левому краю) -->
    <div class="search-toggle">
        <button type="button" id="toggleFiltersBtn" class="search-toggle-btn">🔍 Поиск</button>
    </div>

    <!-- Фильтры (скрыты по умолчанию) -->
    <div id="filtersPanel" class="filters-panel" style="display: none;">
        <form method="get" action="/news" class="filters-form">
            <!-- Категория (селект) -->
            <div class="filter-group">
                <label for="category">Категория</label>
                <select name="category" id="category" class="filter-select">
                    <option value="0">Все категории</option>
                    <?php if (!empty($allCategories)): ?>
                        <?php foreach ($allCategories as $cat): ?>
                            <option value="<?= $cat['id'] ?>" <?= ($activeCategory ?? 0) == $cat['id'] ? 'selected' : '' ?>>
                                <?= str_repeat('—', $cat['level'] ?? 0) ?> <?= esc($cat['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>

            <!-- Дата с -->
            <div class="filter-group">
                <label for="date_from">Дата с</label>
                <input type="date" name="date_from" id="date_from" class="filter-input" value="<?= esc($date_from ?? '') ?>">
            </div>

            <!-- Дата по -->
            <div class="filter-group">
                <label for="date_to">Дата по</label>
                <input type="date" name="date_to" id="date_to" class="filter-input" value="<?= esc($date_to ?? '') ?>">
            </div>

            <!-- Кнопки -->
            <div class="filter-actions">
                <button type="submit" class="filter-apply-btn">Применить</button>
                <a href="/news" class="filter-reset-btn">Сбросить</a>
            </div>
        </form>
    </div>

    <!-- Список новостей -->
    <!-- Список новостей -->
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
                        <p class="news-excerpt"><?= esc(substr(strip_tags($item['anons_text']), 0, 150)) ?>...</p>
                        <a href="/news/<?= esc($item['path']) ?>" class="read-more">Читать подробнее →</a>
                    </div>
                </article>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="empty-news-card">
                <h3 class="empty-news-title">Новости не найдены</h3>
                <p class="empty-news-text">Попробуйте изменить параметры фильтра или вернуться позже.</p>
            </div>
        <?php endif; ?>
    </div>

    <!-- Пагинация -->
<?php if (isset($pager) && $pager->getPageCount() > 1): ?>
    <div class="pagination">
        <?= $pager->links() ?>
    </div>
<?php endif; ?>

    <script>
        // Переключение фильтров
        document.addEventListener('DOMContentLoaded', function() {
            const toggleBtn = document.getElementById('toggleFiltersBtn');
            const filtersPanel = document.getElementById('filtersPanel');

            // Проверяем, есть ли активные фильтры
            const urlParams = new URLSearchParams(window.location.search);
            const hasActiveFilters = urlParams.has('category') || urlParams.has('date_from') || urlParams.has('date_to');

            // Если есть активные фильтры - показываем панель и меняем кнопку
            if (hasActiveFilters) {
                filtersPanel.style.display = 'block';
                toggleBtn.textContent = '✕ Скрыть фильтры';
                toggleBtn.classList.add('close-btn');  // Добавляем класс для серого цвета
            }

            toggleBtn.addEventListener('click', function() {
                if (filtersPanel.style.display === 'none') {
                    filtersPanel.style.display = 'block';
                    toggleBtn.textContent = '✕ Скрыть фильтры';
                    toggleBtn.classList.add('close-btn');
                } else {
                    filtersPanel.style.display = 'none';
                    toggleBtn.textContent = '🔍 Поиск';
                    toggleBtn.classList.remove('close-btn');
                }
            });
        });
    </script>

<?= $this->endSection() ?>