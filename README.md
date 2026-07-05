# simple-site

CMS для корпоративного сайта на **CodeIgniter 4**. Публичная часть + админ-панель для управления контентом.

## Возможности

- Страницы сайта с древовидной структурой
- Новости и категории новостей
- Проекты и события (галереи, мультиязычность RU/EN)
- Файловый менеджер и загрузка через CKEditor
- Настройки сайта (контакты, SEO, соцсети)
- Двуязычность (русский / английский)

## Стек

| Компонент | Версия |
|-----------|--------|
| PHP | 8.2+ |
| CodeIgniter | 4.4.8 |
| MySQL | 8.0 |
| Docker | nginx + PHP-FPM + MySQL (локально) |

## Структура проекта

```
simple-site/
├── app/                    # Приложение CodeIgniter 4
│   ├── app/                # Контроллеры, модели, views, миграции
│   ├── public/             # Document root (index.php, css, js)
│   ├── writable/           # Логи, сессии, кэш, загрузки
│   └── .env                # Конфигурация окружения (не в git)
├── configs/nginx/          # Конфиг nginx для Docker
├── php/                    # Dockerfile PHP-FPM
├── docker-compose.yml
└── databases/mysql/        # Данные MySQL (Docker volume)
```

## Локальная разработка

### Требования

- Docker Desktop
- Git

### Запуск

```bash
docker compose build php
docker compose up -d
```

### Адреса

| Сервис | URL |
|--------|-----|
| Сайт | http://127.0.0.1/ |
| Админ-панель | http://127.0.0.1/admin-panel/login |
| phpMyAdmin | http://127.0.0.1:8081/ |

### Первый запуск (миграции и сиды)

```bash
docker compose exec php php spark migrate
docker compose exec php php spark db:seed SuperAdministratorSeeder
docker compose exec php php spark db:seed SiteconfigSeeder
```

Файл `.env` уже настроен для Docker (`hostname = mysql`, `baseURL = http://localhost/`).

### Остановка

```bash
docker compose down
```

## Админ-панель

URL: `/admin-panel/login`

Разделы: страницы, новости, проекты, события, файлы, категории, настройки.

> После первого входа смените пароль администратора. Дефолтные учётные данные задаются в сидере `SuperAdministratorSeeder` — используйте только для локальной разработки.

## Деплой на Reg.ru

На shared-хостинге Reg.ru используется **Apache**, не Docker.

### Чеклист

1. **PHP 8.2** — выбрать в панели хостинга (ISPmanager / cPanel)
2. **Document root** — указать на папку `app/public/`
3. **SSL** — включить сертификат в панели Reg.ru
4. **`.env`** на сервере:

```env
CI_ENVIRONMENT = production
app.baseURL = 'https://ваш-домен.ru/'
app.forceGlobalSecureRequests = true
cookie.secure = true

database.default.hostname = localhost
database.default.database = ...
database.default.username = ...
database.default.password = ...
```

5. **Права** — `writable/` доступна для записи (755–775)
6. **Зависимости** — `composer install --no-dev` (при наличии SSH)
7. **Миграции** — `php spark migrate` (при наличии SSH)

Редирект HTTP → HTTPS можно настроить в панели Reg.ru или через `.htaccess` в `public/`.

## Безопасность

- CSRF-защита форм и AJAX-запросов
- Удаление, переключение статуса и logout — только через POST
- Rate limiting входа в админку (5 попыток / 15 мин)
- MIME-валидация загружаемых файлов
- Сессии в БД (`n_user_sessions`)
- В production: отключён Debug Toolbar, включены Secure Headers (HSTS и др.)

## Полезные команды

```bash
# Миграции
docker compose exec php php spark migrate

# Откат последней миграции
docker compose exec php php spark migrate:rollback

# Список маршрутов
docker compose exec php php spark routes

# Версия PHP
docker compose exec php php -v
```

## Лицензия

MIT (CodeIgniter 4 App Starter)
