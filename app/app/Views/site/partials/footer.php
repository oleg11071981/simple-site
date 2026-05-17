<footer class="footer">
    <div class="container">
        <div class="footer-inner">
            <!-- Колонка 1: Копирайт -->
            <div class="footer-column">
                <div class="footer-title">© <?= date('Y') ?> n-cms</div>
                <p style="margin-top: 0.5rem; font-size: 0.8rem; opacity: 0.8;">
                    <?= ($currentLang ?? 'ru') === 'en' ? 'All rights reserved.' : 'Все права защищены.' ?>
                </p>
            </div>

            <!-- Колонка 2: Меню с иконками -->
            <div class="footer-column">
                <div class="footer-title"><?= ($currentLang ?? 'ru') === 'en' ? 'Menu' : 'Меню' ?></div>
                <ul class="footer-menu">
                    <li><a href="/">→ <?= ($currentLang ?? 'ru') === 'en' ? 'Home' : 'Главная' ?></a></li>
                    <li><a href="/news">→ <?= ($currentLang ?? 'ru') === 'en' ? 'News' : 'Новости' ?></a></li>
                    <?php foreach ($menuPages as $menuPage): ?>
                        <li>
                            <a href="/<?= esc($menuPage['path']) ?>">
                                → <?= ($currentLang ?? 'ru') === 'en' && !empty($menuPage['name_en']) ? esc($menuPage['name_en']) : esc($menuPage['name']) ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                    <li><a href="/contacts">→ <?= ($currentLang ?? 'ru') === 'en' ? 'Contacts' : 'Контакты' ?></a></li>
                </ul>
            </div>

            <!-- Колонка 3: Контакты -->
            <div class="footer-column">
                <div class="footer-title"><?= ($currentLang ?? 'ru') === 'en' ? 'Contacts' : 'Контакты' ?></div>
                <ul class="footer-contacts">
                    <?php if (!empty($email)): ?>
                        <li>
                            <span class="contact-icon">✉️</span>
                            <a href="mailto:<?= esc($email) ?>"><?= esc($email) ?></a>
                        </li>
                    <?php endif; ?>
                    <?php if (!empty($phone)): ?>
                        <li>
                            <span class="contact-icon">📞</span>
                            <a href="tel:<?= esc($phone) ?>"><?= esc($phone) ?></a>
                        </li>
                    <?php endif; ?>
                    <?php if (!empty($address)): ?>
                        <li>
                            <span class="contact-icon">📍</span>
                            <span><?= esc($address) ?></span>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>
</footer>