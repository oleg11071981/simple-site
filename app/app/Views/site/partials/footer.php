<footer class="footer">
    <div class="container">
        <div class="footer-inner">
            <!-- Колонка 1: Копирайт -->
            <div class="footer-column">
                <div class="footer-title">© <?= date('Y') ?> n-cms</div>
                <p style="margin-top: 0.5rem; font-size: 0.8rem; opacity: 0.8;">
                    Все права защищены.
                </p>
            </div>

            <!-- Колонка 2: Меню с иконками -->
            <div class="footer-column">
                <div class="footer-title">Меню</div>
                <ul class="footer-menu">
                    <li><a href="/">→ Главная</a></li>
                    <li><a href="/news">→ Новости</a></li>
                    <?php foreach ($menuPages as $menuPage): ?>
                        <li><a href="/<?= esc($menuPage['path']) ?>">→ <?= esc($menuPage['name']) ?></a></li>
                    <?php endforeach; ?>
                    <li><a href="/contacts">→ Контакты</a></li>
                </ul>
            </div>

            <!-- Колонка 3: Контакты -->
            <div class="footer-column">
                <div class="footer-title">Контакты</div>
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
        <!--div class="footer-copyright">
            © <?= date('Y') ?> n-cms. Все права защищены.
        </div-->
    </div>
</footer>