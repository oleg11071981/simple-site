<?php if (!empty($files)): ?>
    <div class="gallery-section">
        <h2 class="gallery-title">Галерея</h2>
        <div class="gallery-grid">
            <?php foreach ($files as $file): ?>
                <div class="gallery-item">
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
                               title="Скачать файл">
                                Скачать
                            </a>
                        </div>
                    <?php endif; ?>
                    <div class="gallery-caption"><?= esc($file['title'] ?: $file['name']) ?></div>
                    <!--div class="gallery-meta">
                        <?= strtoupper(esc($file['file_type'])) ?> •
                        <?= $file['size_formatted'] ?>
                    </div-->
                </div>
            <?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>