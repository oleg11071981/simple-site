<?php
/** @var string $url */
/** @var bool $published */
/** @var string|null $buttonTitle */
/** @var array<string, scalar|null> $hidden */
$buttonTitle = $buttonTitle ?? ($published ? 'Снять с публикации' : 'Опубликовать');
$hidden = $hidden ?? [];
?>
<form action="<?= esc($url) ?>" method="post" class="post-action-form">
    <?= csrf_field() ?>
    <?php foreach ($hidden as $name => $value): ?>
        <input type="hidden" name="<?= esc($name) ?>" value="<?= esc((string) $value) ?>">
    <?php endforeach; ?>
    <button type="submit" class="btn-icon btn-icon-toggle" title="<?= esc($buttonTitle) ?>" aria-label="<?= esc($buttonTitle) ?>">
        <?php if ($published): ?>
            <span class="icon-eye" aria-hidden="true">👁️</span>
        <?php else: ?>
            <span class="icon-eye-off" aria-hidden="true">👁️‍🗨️</span>
        <?php endif; ?>
    </button>
</form>
