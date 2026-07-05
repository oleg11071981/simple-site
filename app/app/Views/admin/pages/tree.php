<?php foreach ($pages as $page): ?>
    <tr class="page-row level-<?= $level ?? 0 ?> <?= $page['publish'] == 0 ? 'draft-row' : '' ?>">
        <td class="text-center">
            <input type="checkbox" name="selected_ids[]" form="bulkForm" value="<?= $page['id'] ?>">
        </td>
        <td class="text-center"><?= esc($page['id']) ?></td>
        <td>
            <div class="page-name">
                <?php if (($level ?? 0) > 0): ?>
                    <span class="page-indent">
                        <?= str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;', ($level ?? 0)) ?>
                        └─
                    </span>
                <?php endif; ?>
                <!-- Клик по названию - переход внутрь раздела (просмотр дочерних страниц) -->
                <a href="/admin-panel/pages?parent=<?= $page['id'] ?>" class="page-link">
                    <?= esc($page['name']) ?>
                </a>
            </div>
            <?php if (!empty($page['path'])): ?>
                <div class="page-path">
                    <small>/<?= esc($page['path']) ?></small>
                </div>
            <?php endif; ?>
            <?php if (!empty($page['children'])): ?>
                <div class="page-children-count">
                    <small>📁 <?= count($page['children']) ?> подразделов</small>
                </div>
            <?php endif; ?>
        </td>
        <td>
            <?php if ($page['publish'] == 1): ?>
                <span class="badge badge-success">Опубликовано</span>
            <?php else: ?>
                <span class="badge badge-warning">Черновик</span>
            <?php endif; ?>
        </td>
        <td class="date-cell"><?= date('d.m.Y H:i', strtotime($page['create'])) ?></td>
        <td class="date-cell"><?= date('d.m.Y H:i', strtotime($page['modify'])) ?></td>
        <td class="actions">
            <!-- Иконка публикации -->
            <?= view('admin/partials/toggle_button', [
                'url'       => '/admin-panel/pages/toggle/' . $page['id'],
                'published' => $page['publish'] == 1,
            ]) ?>
            <!-- Иконка редактирования -->
            <a href="/admin-panel/pages/edit/<?= $page['id'] ?>"
               class="btn-icon" title="Редактировать">
                <span class="icon-edit">✏️</span>
            </a>
            <!-- Иконка удаления -->
            <?= view('admin/partials/delete_button', [
                'url'     => '/admin-panel/pages/delete/' . $page['id'],
                'confirm' => 'Удалить страницу «' . esc($page['name']) . '»?',
            ]) ?>
        </td>
    </tr>
    <?php if (!empty($page['children'])): ?>
        <?= view('admin/pages/tree', ['pages' => $page['children'], 'level' => ($level ?? 0) + 1]) ?>
    <?php endif; ?>
<?php endforeach; ?>