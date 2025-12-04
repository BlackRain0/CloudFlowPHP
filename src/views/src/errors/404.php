<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Страница не найдена</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
</head>
<body class="bg-light">
    <div class="container d-flex justify-content-center align-items-center min-vh-100">
        <div class="text-center">
            <!-- Иконка -->
            <div class="mb-4">
                <i class="bi bi-exclamation-triangle text-warning" style="font-size: 5rem;"></i>
            </div>
            
            <!-- Заголовок -->
            <h1 class="display-1 fw-bold text-muted">404</h1>
            <h2 class="mb-4">Страница не найдена</h2>
            
            <!-- Сообщение -->
            <p class="text-muted mb-4">
                Запрашиваемая страница не существует или была перемещена.
            </p>
            
            <!-- Кнопки действий -->
            <div class="d-flex gap-3 justify-content-center">
                <a href="/" class="btn btn-primary px-4">
                    <i class="bi bi-house me-2"></i>На главную
                </a>
                <button onclick="history.back()" class="btn btn-outline-secondary px-4">
                    <i class="bi bi-arrow-left me-2"></i>Назад
                </button>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <style>
    .min-vh-100 {
        min-height: 100vh;
    }
    </style>
</body>
</html>