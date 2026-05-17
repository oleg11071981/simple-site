<?php if (!empty($pages)): ?>
    <ul class="subpages-list">
        <?php foreach ($pages as $subpage): ?>
            <li class="subpages-item">
                <a href="/<?= esc($subpage['full_path']) ?>" class="subpages-link">
                    <?= ($currentLang ?? 'ru') === 'en' && !empty($subpage['name_en']) ? esc($subpage['name_en']) : esc($subpage['name']) ?>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>