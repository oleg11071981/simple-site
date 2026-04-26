<?php foreach ($pages as $subpage): ?>
    <li class="subpages-item">
        <a href="/<?= esc($subpage['full_path']) ?>" class="subpages-link">
            <?= esc($subpage['name']) ?>
        </a>
    </li>
<?php endforeach; ?>