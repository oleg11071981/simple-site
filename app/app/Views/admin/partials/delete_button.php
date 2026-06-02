<?php
/** @var string $url */
/** @var string $confirm */
/** @var string|null $buttonTitle */
$buttonTitle = $buttonTitle ?? 'Удалить';
?>
<form action="<?= esc($url) ?>" method="post" class="delete-form" onsubmit="return confirm('<?= esc($confirm, 'attr') ?>')">
    <?= csrf_field() ?>
    <button type="submit" class="btn-icon btn-icon-delete" title="<?= esc($buttonTitle) ?>" aria-label="<?= esc($buttonTitle) ?>">
        <span class="icon-delete" aria-hidden="true">🗑️</span>
    </button>
</form>
