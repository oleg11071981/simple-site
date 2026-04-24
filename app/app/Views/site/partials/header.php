<header class="header">
    <div class="container navbar">
        <a href="/" class="logo">Д<span>емо сайт</span></a>

        <!-- Десктопное меню -->
        <?= view('site/partials/menu', ['menuPages' => $menuPages, 'activePage' => $activePage, 'type' => 'desktop']) ?>

        <button class="burger" id="burgerBtn" aria-label="Меню">
            <span></span>
            <span></span>
            <span></span>
        </button>
    </div>

    <!-- Мобильное меню -->
    <?= view('site/partials/menu', ['menuPages' => $menuPages, 'activePage' => $activePage, 'type' => 'mobile']) ?>
</header>