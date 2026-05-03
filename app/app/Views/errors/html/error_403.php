<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>403 - Доступ запрещён</title>
    <meta name="description" content="У вас нет доступа к этой странице">
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

        .error-403-container {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .error-403-content {
            text-align: center;
            max-width: 600px;
            margin: 0 auto;
            background: white;
            padding: 3rem 2rem;
            border-radius: 1.5rem;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }

        .error-403-code {
            font-size: 8rem;
            font-weight: 900;
            color: #dc3545;
            line-height: 1;
            margin-bottom: 1rem;
            text-shadow: 4px 4px 0 rgba(220, 53, 69, 0.1);
        }

        .error-403-title {
            font-size: 1.8rem;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 1rem;
        }

        .error-403-text {
            font-size: 1rem;
            color: #6b7280;
            margin-bottom: 2rem;
            line-height: 1.6;
        }

        .error-403-actions {
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
            .error-403-content {
                padding: 2rem 1.5rem;
            }

            .error-403-code {
                font-size: 5rem;
            }

            .error-403-title {
                font-size: 1.4rem;
            }

            .error-403-actions {
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
<div class="error-403-container">
    <div class="error-403-content">
        <div class="error-403-code">403</div>
        <h1 class="error-403-title">Доступ запрещён</h1>
        <p class="error-403-text">
            У вас нет прав для доступа к этой странице.<br>
            Пожалуйста, войдите в систему или вернитесь на главную.
        </p>

        <div class="error-403-actions">
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