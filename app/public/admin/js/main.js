/**
 * Основные скрипты для админ-панели
 * Путь: public/admin/js/main.js
 */

(function() {
    'use strict';

    // ========================================
    // БУРГЕР-МЕНЮ (общий для всех страниц)
    // ========================================

    function initBurgerMenu() {
        const burgerMenu = document.getElementById('burgerMenu');
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('overlay');

        if (!burgerMenu || !sidebar || !overlay) {
            return;
        }

        function toggleMenu() {
            sidebar.classList.toggle('active');
            overlay.classList.toggle('active');

            if (sidebar.classList.contains('active')) {
                document.body.style.overflow = 'hidden';
            } else {
                document.body.style.overflow = '';
            }
        }

        function closeMenu() {
            sidebar.classList.remove('active');
            overlay.classList.remove('active');
            document.body.style.overflow = '';
        }

        burgerMenu.addEventListener('click', toggleMenu);
        overlay.addEventListener('click', closeMenu);

        window.addEventListener('resize', function() {
            if (window.innerWidth > 768 && sidebar.classList.contains('active')) {
                closeMenu();
            }
        });

        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape' && sidebar.classList.contains('active')) {
                closeMenu();
            }
        });
    }

    // ========================================
    // УВЕДОМЛЕНИЯ (автоматическое скрытие)
    // ========================================

    function initAlerts() {
        const successAlert = document.getElementById('successAlert');
        const errorAlert = document.getElementById('errorAlert');

        function hideAlert(alertElement) {
            if (alertElement) {
                alertElement.classList.add('alert-hide');
                setTimeout(function() {
                    alertElement.style.display = 'none';
                }, 300);
            }
        }

        if (successAlert) {
            setTimeout(function() {
                hideAlert(successAlert);
            }, 3000);
        }

        if (errorAlert) {
            setTimeout(function() {
                hideAlert(errorAlert);
            }, 5000);
        }

        const closeButtons = document.querySelectorAll('.alert-close');
        closeButtons.forEach(function(button) {
            button.addEventListener('click', function() {
                const alert = this.closest('.alert');
                hideAlert(alert);
            });
        });
    }

    // ========================================
    // ТРАНСЛИТЕРАЦИЯ (общая для всех форм)
    // ========================================

    window.rusToTranslit = function(field, sourceField) {
        var rus = "абвгдеёжзийклмнопрстуфхцчшщъыьэюя";
        var eng = "abvgdeejziyklmnoprstufhccss_yaeuya";

        var text = sourceField.value;
        var result = "";

        for (var i = 0; i < text.length; i++) {
            var char = text[i].toLowerCase();
            var index = rus.indexOf(char);

            if (index >= 0) {
                result += eng[index];
            } else if (char.match(/[a-z0-9]/)) {
                result += char;
            } else if (char.match(/\s/)) {
                result += "-";
            }
        }

        document.getElementById(field).value = result;
    };

    // ========================================
    // МАССОВЫЕ ДЕЙСТВИЯ (общие)
    // ========================================

    window.toggleAll = function(source) {
        var checkboxes = document.querySelectorAll('input[name="selected_ids[]"]');
        for (var i = 0; i < checkboxes.length; i++) {
            checkboxes[i].checked = source.checked;
        }
    };

    window.confirmBulkAction = function(formId, actionSelectId) {
        var actionSelect = document.getElementById(actionSelectId) || document.querySelector('select[name="bulk_action"]');
        var action = actionSelect ? actionSelect.value : '';

        if (action === '') {
            alert('Пожалуйста, выберите действие');
            return;
        }

        var checkboxes = document.querySelectorAll('input[name="selected_ids[]"]:checked');
        if (checkboxes.length === 0) {
            alert('Пожалуйста, выберите хотя бы один элемент');
            return;
        }

        var message = '';
        if (action === 'delete') {
            message = 'Вы действительно хотите удалить выбранные элементы?';
        } else if (action === 'publish') {
            message = 'Опубликовать выбранные элементы?';
        } else if (action === 'unpublish') {
            message = 'Снять с публикации выбранные элементы?';
        }

        if (confirm(message)) {
            document.getElementById(formId || 'bulkForm').submit();
        }
    };

    // ========================================
    // CKEDITOR ИНИЦИАЛИЗАЦИЯ (общая)
    // ========================================

    function initCKEditor(selector, customConfig) {
        if (typeof CKEDITOR === 'undefined') {
            return;
        }

        const element = typeof selector === 'string' ? document.getElementById(selector) : selector;
        if (!element) {
            return;
        }

        const defaultConfig = {
            language: 'ru',
            height: 400,
            toolbar: [
                ['Source', '-', 'NewPage', 'Preview'],
                ['Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord'],
                ['Undo', 'Redo', '-', 'Find', 'Replace', '-', 'SelectAll', 'RemoveFormat'],
                ['Bold', 'Italic', 'Underline', 'Strike'],
                ['NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', 'Blockquote'],
                ['JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock'],
                ['Link', 'Unlink', 'Anchor'],
                ['Image', 'Table', 'HorizontalRule', 'SpecialChar'],
                ['Styles', 'Format', 'Font', 'FontSize'],
                ['TextColor', 'BGColor'],
                ['Maximize', 'ShowBlocks']
            ]
        };

        const config = customConfig ? Object.assign({}, defaultConfig, customConfig) : defaultConfig;
        CKEDITOR.replace(element.id || selector, config);
    }

    // Объявляем глобальную функцию для использования в других скриптах
    window.initCKEditor = initCKEditor;

    // ========================================
    // ИНИЦИАЛИЗАЦИЯ ПРИ ЗАГРУЗКЕ
    // ========================================

    document.addEventListener('DOMContentLoaded', function() {
        // Бургер-меню
        initBurgerMenu();

        // Уведомления
        initAlerts();

        // CKEditor для MainText (если есть)
        if (document.getElementById('MainText') && typeof CKEDITOR !== 'undefined') {
            initCKEditor('MainText');
        }

        // Генерация пути из названия для форм (общая логика)
        const nameInput = document.getElementById('name');
        const pathInput = document.getElementById('path');

        if (nameInput && pathInput) {
            nameInput.addEventListener('blur', function() {
                if (pathInput.value === '' && window.rusToTranslit) {
                    window.rusToTranslit('path', nameInput);
                }
            });
        }

        // Показ/скрытие поля внешней ссылки (для страниц)
        const typeSelect = document.getElementById('type');
        const hrefField = document.getElementById('href-field');
        const targetField = document.getElementById('target-field');

        if (typeSelect && hrefField && targetField) {
            function toggleHrefField() {
                if (typeSelect.value === '1') {
                    hrefField.style.display = 'block';
                    targetField.style.display = 'block';
                } else {
                    hrefField.style.display = 'none';
                    targetField.style.display = 'none';
                }
            }
            typeSelect.addEventListener('change', toggleHrefField);
            toggleHrefField();
        }

        // Счётчики символов для SEO (для страниц)
        const keywordsTextarea = document.getElementById('keywords');
        const descriptionTextarea = document.getElementById('description');
        const keywordsCountSpan = document.getElementById('keywordsCount');
        const descriptionCountSpan = document.getElementById('descriptionCount');

        function updateCharCounters() {
            if (keywordsTextarea && keywordsCountSpan) {
                keywordsCountSpan.textContent = keywordsTextarea.value.length;
            }

            if (descriptionTextarea && descriptionCountSpan) {
                var length = descriptionTextarea.value.length;
                descriptionCountSpan.textContent = length;

                if (length > 160) {
                    descriptionCountSpan.style.color = '#dc3545';
                    descriptionCountSpan.style.fontWeight = 'bold';
                } else if (length > 140) {
                    descriptionCountSpan.style.color = '#ffc107';
                } else {
                    descriptionCountSpan.style.color = '#28a745';
                }
            }
        }

        if (keywordsTextarea) {
            keywordsTextarea.addEventListener('input', updateCharCounters);
            updateCharCounters();
        }

        if (descriptionTextarea) {
            descriptionTextarea.addEventListener('input', updateCharCounters);
            updateCharCounters();
        }

        // CKEditor для more_info (для страниц)
        if (document.getElementById('more_info') && typeof CKEDITOR !== 'undefined') {
            initCKEditor('more_info');
        }
    });
})();