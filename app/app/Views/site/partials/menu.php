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
    </div>
<?php endif; ?>