<form action="/admin-panel/logout" method="post" class="logout-form">
    <?= csrf_field() ?>
    <button type="submit" class="logout-link">🚪 Выйти</button>
</form>
