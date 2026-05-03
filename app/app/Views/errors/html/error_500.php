<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>500 - Ошибка сервера</title>
    <meta name="robots" content="noindex, nofollow">

    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&display=swap" rel="stylesheet">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Roboto', system-ui, -apple-system, 'Segoe UI', Arial, sans-serif;
            background: #f5f5f5;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .error-container {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .error-content {
            text-align: center;
            max-width: 600px;
            margin: 0 auto;
            background: white;
            padding: 3rem 2rem;
            border-radius: 1.5rem;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }

        .error-code {
            font-size: 8rem;
            font-weight: 900;
            color: #dc3545;
            line-height: 1;
            margin-bottom: 1rem;
            text-shadow: 4px 4px 0 rgba(220, 53, 69, 0.1);
        }

        .error-title {
            font-size: 1.8rem;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 1rem;
        }

        .error-text {
            font-size: 1rem;
            color: #6b7280;
            margin-bottom: 2rem;
            line-height: 1.6;
        }

        .error-actions {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn-primary {
            background: #001e78;
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 0.5rem;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-primary:hover {
            background: #6476be;
            transform: translateY(-2px);
        }

        .btn-secondary {
            background: #e5e7eb;
            color: #374151;
            padding: 0.75rem 1.5rem;
            border-radius: 0.5rem;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-secondary:hover {
            background: #d1d5db;
            transform: translateY(-2px);
        }

        @media (max-width: 768px) {
            .error-content {
                padding: 2rem 1.5rem;
            }

            .error-code {
                font-size: 5rem;
            }

            .error-title {
                font-size: 1.4rem;
            }

            .error-actions {
                flex-direction: column;
                align-items: center;
            }

            .btn-primary, .btn-secondary {
                width: 200px;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
<div class="error-container">
    <div class="error-content">
        <div class="error-code">500</div>
        <h1 class="error-title">Внутренняя ошибка сервера</h1>
        <p class="error-text">
            Что-то пошло не так. Мы уже работаем над исправлением проблемы.<br>
            Пожалуйста, попробуйте позже.
        </p>

        <div class="error-actions">
            <a href="/" class="btn-primary">
                🏠 На главную
            </a>
            <a href="javascript:history.back()" class="btn-secondary">
                ◀ Вернуться назад
            </a>
        </div>
    </div>
</div>
</body>
</html>