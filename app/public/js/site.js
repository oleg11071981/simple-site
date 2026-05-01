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

            if (mobileMenu.classList.contains('active')) {
                document.body.style.overflow = 'hidden';
            } else {
                document.body.style.overflow = '';
            }
        });

        document.addEventListener('click', function(event) {
            if (mobileMenu.classList.contains('active') &&
                !burgerBtn.contains(event.target) &&
                !mobileMenu.contains(event.target)) {
                burgerBtn.classList.remove('active');
                mobileMenu.classList.remove('active');
                document.body.style.overflow = '';
            }
        });

        window.addEventListener('resize', function() {
            if (window.innerWidth > 768 && mobileMenu.classList.contains('active')) {
                burgerBtn.classList.remove('active');
                mobileMenu.classList.remove('active');
                document.body.style.overflow = '';
            }
        });
    }

    // FancyBox для изображений с классом bigfoto
    if (typeof Fancybox !== 'undefined') {
        Fancybox.bind('.bigfoto', {
            Toolbar: {
                display: {
                    left: ['infobar'],
                    middle: [],
                    right: ['zoom', 'thumbs', 'close'],
                },
            },
            closeOnOutsideClick: true,
            // Правильный способ задания подписи в FancyBox v5
            caption: function(instance, slide) {
                const caption = slide.triggerEl?.getAttribute('data-caption') ||
                    slide.triggerEl?.getAttribute('title') ||
                    '';
                return caption;
            },
        });
    }
});