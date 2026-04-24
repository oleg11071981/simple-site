/**
 * Скрипты для публичной части сайта
 */

document.addEventListener('DOMContentLoaded', function() {
    // Бургер-меню
    const burgerBtn = document.getElementById('burgerBtn');
    const mobileMenu = document.getElementById('mobileMenu');

    if (burgerBtn && mobileMenu) {
        burgerBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            this.classList.toggle('active');
            mobileMenu.classList.toggle('active');

            // Блокируем прокрутку body при открытом меню
            if (mobileMenu.classList.contains('active')) {
                document.body.style.overflow = 'hidden';
            } else {
                document.body.style.overflow = '';
            }
        });

        // Закрываем меню при клике вне его
        document.addEventListener('click', function(event) {
            if (mobileMenu.classList.contains('active') &&
                !burgerBtn.contains(event.target) &&
                !mobileMenu.contains(event.target)) {
                burgerBtn.classList.remove('active');
                mobileMenu.classList.remove('active');
                document.body.style.overflow = '';
            }
        });

        // Закрываем меню при ресайзе окна (если стало десктопом)
        window.addEventListener('resize', function() {
            if (window.innerWidth > 768 && mobileMenu.classList.contains('active')) {
                burgerBtn.classList.remove('active');
                mobileMenu.classList.remove('active');
                document.body.style.overflow = '';
            }
        });
    }
});