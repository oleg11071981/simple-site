<?php if (!empty($files)): ?>
    <div class="gallery-section">
        <h2 class="gallery-title"><?= ($currentLang ?? 'ru') === 'en' ? 'Gallery' : 'Галерея' ?></h2>

        <!-- Swiper слайдер -->
        <div class="swiper gallery-swiper">
            <div class="swiper-wrapper">
                <?php foreach ($files as $file): ?>
                    <div class="swiper-slide gallery-slide">
                        <?php if (in_array($file['file_type'], ['jpg', 'jpeg', 'png', 'gif', 'webp'])): ?>
                            <a href="/uploads/<?= esc($file['file_name']) ?>"
                               class="gallery-link bigfoto"
                               data-fancybox="gallery"
                               data-caption="<?= esc($file['title'] ?: $file['name']) ?>">
                                <img src="/uploads/<?= esc($file['file_name']) ?>"
                                     alt="<?= esc($file['title'] ?: $file['name']) ?>">
                            </a>
                        <?php else: ?>
                            <div class="gallery-file">
                                <div class="file-icon">
                                    <?php
                                    $icons = [
                                        'pdf' => '📄', 'doc' => '📝', 'docx' => '📝',
                                        'xls' => '📊', 'xlsx' => '📊', 'zip' => '📦',
                                        'rar' => '📦', 'txt' => '📃', 'default' => '📁'
                                    ];
                                    echo $icons[$file['file_type']] ?? $icons['default'];
                                    ?>
                                </div>
                                <a href="/uploads/<?= esc($file['file_name']) ?>"
                                   class="download-link"
                                   download
                                   title="<?= ($currentLang ?? 'ru') === 'en' ? 'Download file' : 'Скачать файл' ?>">
                                    <?= ($currentLang ?? 'ru') === 'en' ? 'Download' : 'Скачать' ?>
                                </a>
                            </div>
                        <?php endif; ?>
                        <div class="gallery-caption"><?= esc($file['title'] ?: $file['name']) ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
            <!-- Пагинация -->
            <div class="swiper-pagination"></div>
            <!-- Кнопки навигации -->
            <div class="swiper-button-prev"></div>
            <div class="swiper-button-next"></div>
        </div>
    </div>
<?php endif; ?>