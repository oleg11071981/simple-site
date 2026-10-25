<?php if (!empty($files)): ?>
    <div class="gallery-section">
        <h2 class="gallery-title"><?= ($currentLang ?? 'ru') === 'en' ? 'Gallery' : 'Галерея' ?></h2>

        <!-- Swiper слайдер -->
        <div class="swiper gallery-swiper">
            <div class="swiper-wrapper">
                <?php foreach ($files as $file): ?>
                    <?php
                    // Подпись только из title / title_en (без fallback на name)
                    if (($currentLang ?? 'ru') === 'en') {
                        $caption = !empty($file['title_en'])
                            ? $file['title_en']
                            : ($file['title'] ?? '');
                    } else {
                        $caption = $file['title'] ?? '';
                    }
                    $caption = trim((string) $caption);
                    ?>
                    <div class="swiper-slide gallery-slide">
                        <?php if (in_array($file['file_type'], ['jpg', 'jpeg', 'png', 'gif', 'webp'])): ?>
                            <a href="/uploads/<?= esc($file['file_name']) ?>"
                               class="gallery-link bigfoto"
                               data-fancybox="gallery"
                               <?php if ($caption !== ''): ?>data-caption="<?= esc($caption) ?>"<?php endif; ?>>
                                <img src="/uploads/<?= esc($file['file_name']) ?>"
                                     alt="<?= esc($caption) ?>">
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
                        <?php if ($caption !== ''): ?>
                            <div class="gallery-caption"><?= esc($caption) ?></div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="swiper-pagination"></div>
            <div class="swiper-button-prev"></div>
            <div class="swiper-button-next"></div>
        </div>
    </div>
<?php endif; ?>