<?= $this->extend('site/layouts/base') ?>

<?= $this->section('content') ?>

    <div class="page-header">
        <h1 class="page-title">Контакты</h1>
        <p class="page-description">
            Свяжитесь с нами удобным для вас способом
        </p>
    </div>

    <!-- Контактная информация и карта -->
    <div class="contacts-grid">
        <!-- Блок с контактной информацией -->
        <div class="contacts-info-card">
            <h2 class="contacts-info-title">📋 Контактная информация</h2>

            <div class="contacts-info-list">
                <?php if (!empty($address)): ?>
                    <div class="contacts-info-item">
                        <div class="contacts-info-icon">📍</div>
                        <div class="contacts-info-content">
                            <div class="contacts-info-label">Адрес:</div>
                            <div class="contacts-info-value"><?= esc($address) ?></div>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (!empty($phone)): ?>
                    <div class="contacts-info-item">
                        <div class="contacts-info-icon">📞</div>
                        <div class="contacts-info-content">
                            <div class="contacts-info-label">Телефон:</div>
                            <div class="contacts-info-value"><?= esc($phone) ?></div>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (!empty($email)): ?>
                    <div class="contacts-info-item">
                        <div class="contacts-info-icon">✉️</div>
                        <div class="contacts-info-content">
                            <div class="contacts-info-label">Email:</div>
                            <div class="contacts-info-value">
                                <a href="mailto:<?= esc($email) ?>" class="contacts-link"><?= esc($email) ?></a>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (!empty($adminEmail)): ?>
                    <div class="contacts-info-item">
                        <div class="contacts-info-icon">👨‍💼</div>
                        <div class="contacts-info-content">
                            <div class="contacts-info-label">Email администратора:</div>
                            <div class="contacts-info-value">
                                <a href="mailto:<?= esc($adminEmail) ?>" class="contacts-link"><?= esc($adminEmail) ?></a>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (!empty($workSchedule)): ?>
                    <div class="contacts-info-item">
                        <div class="contacts-info-icon">🕐</div>
                        <div class="contacts-info-content">
                            <div class="contacts-info-label">Режим работы:</div>
                            <div class="contacts-info-value"><?= esc($workSchedule) ?></div>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (!empty($additional_field1)): ?>
                    <div class="contacts-info-item">
                        <div class="contacts-info-icon">📌</div>
                        <div class="contacts-info-content">
                            <div class="contacts-info-value"><?= esc($additional_field1) ?></div>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (!empty($additional_field2)): ?>
                    <div class="contacts-info-item">
                        <div class="contacts-info-icon">📌</div>
                        <div class="contacts-info-content">
                            <div class="contacts-info-value"><?= esc($additional_field2) ?></div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Блок с картой -->
        <div class="contacts-map-card">
            <h2 class="contacts-info-title">🗺️ Схема проезда</h2>
            <div class="map-container">
                <iframe
                    src="https://yandex.ru/map-widget/v1/?um=constructor%3Ae3b5f8f9a6b5c4d3e2f1a0b9c8d7e6f5&source=constructor"
                    width="100%"
                    height="400"
                    frameborder="0"
                    allowfullscreen>
                </iframe>
            </div>
            <p class="map-note">
                <a href="#" class="map-link" id="openMapLink">Открыть карту в новом окне →</a>
            </p>
        </div>
    </div>

    <script>
        // Открыть карту в новом окне
        document.getElementById('openMapLink')?.addEventListener('click', function(e) {
            e.preventDefault();
            window.open('https://yandex.ru/maps/?text=Москва, Красная площадь', '_blank');
        });
    </script>

<?= $this->endSection() ?>