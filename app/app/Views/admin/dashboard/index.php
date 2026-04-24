<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>

    <!-- Flash сообщения -->
<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success" id="successAlert">
        <span class="alert-icon">✓</span>
        <span class="alert-message"><?= esc(session()->getFlashdata('success')) ?></span>
        <button class="alert-close" onclick="this.parentElement.style.display='none'">×</button>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-error" id="errorAlert">
        <span class="alert-icon">⚠</span>
        <span class="alert-message"><?= esc(session()->getFlashdata('error')) ?></span>
        <button class="alert-close" onclick="this.parentElement.style.display='none'">×</button>
    </div>
<?php endif; ?>

    <!-- Карточка приветствия -->
    <div class="welcome-card">
        <h1>Добро пожаловать, <?= esc($user['name'] ?: $user['login']) ?>!</h1>
        <p>Вы успешно вошли в панель управления сайтом.</p>
    </div>

    <!-- Информация о пользователе -->
    <div class="info-panel">
        <h2>Информация о пользователе</h2>
        <div class="info-item">
            <div class="info-label">ID пользователя</div>
            <div class="info-value"><?= esc($user['id']) ?></div>
        </div>
        <div class="info-item">
            <div class="info-label">Логин</div>
            <div class="info-value"><?= esc($user['login']) ?></div>
        </div>
        <div class="info-item">
            <div class="info-label">Имя</div>
            <div class="info-value"><?= esc($user['name'] ?: 'Не указано') ?></div>
        </div>
        <div class="info-item">
            <div class="info-label">Email</div>
            <div class="info-value"><?= esc($user['email'] ?: 'Не указан') ?></div>
        </div>
        <div class="info-item">
            <div class="info-label">Тип</div>
            <div class="info-value">
                <?php
                switch ($user['type']) {
                    case 1:
                        echo 'Администратор (полные права)';
                        break;
                    case 2:
                        echo 'Модератор';
                        break;
                    default:
                        echo 'Обычный пользователь';
                }
                ?>
            </div>
        </div>
        <div class="info-item">
            <div class="info-label">Время входа</div>
            <div class="info-value"><?= esc($logged_in_at) ?></div>
        </div>
    </div>

<?= $this->endSection() ?>