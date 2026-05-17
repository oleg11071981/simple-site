<header class="header">
    <div class="container navbar">
        <a href="/" class="logo">
            <?php
            // Получаем название сайта из настроек с учетом языка
            $settingsModel = new \App\Models\NSiteconfigModel();
            $lang = $currentLang ?? 'ru';

            if ($lang === 'en') {
                $siteName = $settingsModel->get('SiteName_en');
                if (empty($siteName)) {
                    $siteName = $settingsModel->get('SiteName');
                }
            } else {
                $siteName = $settingsModel->get('SiteName');
            }

            echo esc($siteName ?? 'n-cms');
            ?>
        </a>

        <!-- Десктопное меню -->
        <?= view('site/partials/menu', ['menuPages' => $menuPages, 'activePage' => $activePage, 'type' => 'desktop', 'currentLang' => $currentLang ?? 'ru']) ?>

        <button class="burger" id="burgerBtn" aria-label="Меню">
            <span></span>
            <span></span>
            <span></span>
        </button>
    </div>

    <!-- Мобильное меню -->
    <?= view('site/partials/menu', ['menuPages' => $menuPages, 'activePage' => $activePage, 'type' => 'mobile', 'currentLang' => $currentLang ?? 'ru']) ?>
</header>