<?php
/** @var string $url */
/** @var string $confirm */
/** @var string $title */
?>
<form action="<?= esc($url) ?>" method="post" class="delete-form" onsubmit="return confirm('<?= esc($confirm, 'attr') ?>')">
    <?= csrf_field() ?>
    <button type="submit" class="btn-icon" title="<?= esc($title ?? 'Удалить') ?>">
        <span class="icon-delete">🗑️</span>
    </button>
</form>
