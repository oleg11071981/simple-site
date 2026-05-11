<?php if ($type === 'desktop'): ?>
    <div class="nav-menu">
        <a href="/" class="nav-link <?= $activePage === 'home' ? 'active' : '' ?>">Главная</a>
        <a href="/news" class="nav-link <?= $activePage === 'news' ? 'active' : '' ?>">Новости</a>
        <a href="/projects" class="nav-link <?= $activePage === 'projects' ? 'active' : '' ?>">Проекты</a>
        <?php foreach ($menuPages as $menuPage): ?>
            <a href="/<?= esc($menuPage['path']) ?>" class="nav-link <?= $activePage === 'page_' . $menuPage['id'] ? 'active' : '' ?>">
                <?= esc($menuPage['name']) ?>
            </a>
        <?php endforeach; ?>
        <a href="/contacts" class="nav-link <?= $activePage === 'contacts' ? 'active' : '' ?>">Контакты</a>

        <!-- Переключатель языка -->
        <div class="language-switcher">
            <?php if ($currentLang === 'ru'): ?>
                <a href="/lang/en" class="lang-link active">RU</a>
                <span class="lang-separator">|</span>
                <a href="/lang/en" class="lang-link">EN</a>
            <?php else: ?>
                <a href="/lang/ru" class="lang-link">RU</a>
                <span class="lang-separator">|</span>
                <a href="/lang/ru" class="lang-link active">EN</a>
            <?php endif; ?>
        </div>
    </div>
<?php else: ?>
    <div class="mobile-nav" id="mobileMenu">
        <a href="/" class="nav-link <?= $activePage === 'home' ? 'active' : '' ?>">Главная</a>
        <a href="/news" class="nav-link <?= $activePage === 'news' ? 'active' : '' ?>">Новости</a>
        <a href="/projects" class="nav-link <?= $activePage === 'projects' ? 'active' : '' ?>">Проекты</a>
        <?php foreach ($menuPages as $menuPage): ?>
            <a href="/<?= esc($menuPage['path']) ?>" class="nav-link <?= $activePage === 'page_' . $menuPage['id'] ? 'active' : '' ?>">
                <?= esc($menuPage['name']) ?>
            </a>
        <?php endforeach; ?>
        <a href="/contacts" class="nav-link <?= $activePage === 'contacts' ? 'active' : '' ?>">Контакты</a>

        <!-- Переключатель языка в мобильном меню -->
        <div class="mobile-language-switcher">
            <?php if ($currentLang === 'ru'): ?>
                <a href="/lang/en" class="lang-link active">RU</a>
                <span class="lang-separator">|</span>
                <a href="/lang/en" class="lang-link">EN</a>
            <?php else: ?>
                <a href="/lang/ru" class="lang-link">RU</a>
                <span class="lang-separator">|</span>
                <a href="/lang/ru" class="lang-link active">EN</a>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>