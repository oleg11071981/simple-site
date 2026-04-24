<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <title>Вход в систему | Админ-панель</title>
    <link rel="stylesheet" href="/admin/css/login.css">
</head>
<body>
<div class="login-container">
    <div class="login-box">
        <div class="login-header">
            <h1>Вход в систему</h1>
            <p>Введите свои данные для входа</p>
        </div>

        <!-- Вывод flash сообщений -->
        <?php if (session()->getFlashdata('success')): ?>
            <div class="success-message">
                <?= esc(session()->getFlashdata('success')) ?>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="error-message">
                <?= esc(session()->getFlashdata('error')) ?>
            </div>
        <?php endif; ?>

        <!-- Вывод ошибок валидации -->
        <?php if (isset($validation)): ?>
            <div class="error-message">
                <?= $validation->listErrors() ?>
            </div>
        <?php endif; ?>

        <form action="/admin-panel/auth/authenticate" method="post">
            <!-- CSRF защита -->
            <?= csrf_field() ?>

            <div class="form-group">
                <label for="login">Логин</label>
                <input type="text"
                       id="login"
                       name="login"
                       placeholder="Введите ваш логин"
                       value="<?= old('login') ?>"
                       autocomplete="username"
                       autofocus>
            </div>

            <div class="form-group">
                <label for="password">Пароль</label>
                <input type="password"
                       id="password"
                       name="password"
                       placeholder="Введите ваш пароль"
                       autocomplete="current-password">
            </div>

            <button type="submit" class="btn-login">Войти</button>
        </form>
    </div>
</div>
</body>
</html>