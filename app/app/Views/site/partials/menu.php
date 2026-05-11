<?php if ($type === 'desktop'): ?>
    <div class="nav-menu">
        <a href="/" class="nav-link <?= $activePage === 'home' ? 'active' : '' ?>">
            <?= ($currentLang ?? 'ru') === 'en' ? 'Home' : 'Главная' ?>
        </a>
        <a href="/news" class="nav-link <?= $activePage === 'news' ? 'active' : '' ?>">
            <?= ($currentLang ?? 'ru') === 'en' ? 'News' : 'Новости' ?>
        </a>
        <a href="/projects" class="nav-link <?= $activePage === 'projects' ? 'active' : '' ?>">
            <?= ($currentLang ?? 'ru') === 'en' ? 'Projects' : 'Проекты' ?>
        </a>
        <?php foreach ($menuPages as $menuPage): ?>
            <a href="/<?= esc($menuPage['path']) ?>" class="nav-link <?= $activePage === 'page_' . $menuPage['id'] ? 'active' : '' ?>">
                <?= ($currentLang ?? 'ru') === 'en' && !empty($menuPage['name_en']) ? esc($menuPage['name_en']) : esc($menuPage['name']) ?>
            </a>
        <?php endforeach; ?>
        <a href="/contacts" class="nav-link <?= $activePage === 'contacts' ? 'active' : '' ?>">
            <?= ($currentLang ?? 'ru') === 'en' ? 'Contacts' : 'Контакты' ?>
        </a>

        <!-- Переключатель языка -->
        <div class="language-switcher">
            <?php if (($currentLang ?? 'ru') === 'ru'): ?>
                <a href="/lang/en" class="lang-link">RU</a>
                <span class="lang-separator">|</span>
                <a href="/lang/en" class="lang-link active">EN</a>
            <?php else: ?>
                <a href="/lang/ru" class="lang-link active">RU</a>
                <span class="lang-separator">|</span>
                <a href="/lang/ru" class="lang-link">EN</a>
            <?php endif; ?>
        </div>
    </div>
<?php else: ?>
    <div class="mobile-nav" id="mobileMenu">
        <a href="/" class="nav-link <?= $activePage === 'home' ? 'active' : '' ?>">
            <?= ($currentLang ?? 'ru') === 'en' ? 'Home' : 'Главная' ?>
        </a>
        <a href="/news" class="nav-link <?= $activePage === 'news' ? 'active' : '' ?>">
            <?= ($currentLang ?? 'ru') === 'en' ? 'News' : 'Новости' ?>
        </a>
        <a href="/projects" class="nav-link <?= $activePage === 'projects' ? 'active' : '' ?>">
            <?= ($currentLang ?? 'ru') === 'en' ? 'Projects' : 'Проекты' ?>
        </a>
        <?php foreach ($menuPages as $menuPage): ?>
            <a href="/<?= esc($menuPage['path']) ?>" class="nav-link <?= $activePage === 'page_' . $menuPage['id'] ? 'active' : '' ?>">
                <?= ($currentLang ?? 'ru') === 'en' && !empty($menuPage['name_en']) ? esc($menuPage['name_en']) : esc($menuPage['name']) ?>
            </a>
        <?php endforeach; ?>
        <a href="/contacts" class="nav-link <?= $activePage === 'contacts' ? 'active' : '' ?>">
            <?= ($currentLang ?? 'ru') === 'en' ? 'Contacts' : 'Контакты' ?>
        </a>

        <!-- Переключатель языка в мобильном меню -->
        <div class="mobile-language-switcher">
            <?php if (($currentLang ?? 'ru') === 'ru'): ?>
                <a href="/lang/en" class="lang-link">RU</a>
                <span class="lang-separator">|</span>
                <a href="/lang/en" class="lang-link active">EN</a>
            <?php else: ?>
                <a href="/lang/ru" class="lang-link active">RU</a>
                <span class="lang-separator">|</span>
                <a href="/lang/ru" class="lang-link">EN</a>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>