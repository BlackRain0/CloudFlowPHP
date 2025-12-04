<?php
include("src/views/src/includes/header.php");
?>
<?php

use app\controllers\Group;
use app\controllers\Task;
use app\controllers\User;

// Получаем данные текущего пользователя
$currentUser = User::getUserById($_SESSION['id'] ?? 0);
$tasks = Task::getTasksByUser($_SESSION['id'] ?? 0);
$groups = Group::getGroupByUser(['userId' => $currentUser['id']]);

// Подсчет
$countTasks = is_array($tasks) ? count($tasks) : 0;
$countGroups = is_array($groups) ? count($groups) : 0;

// Если пользователь не авторизован
if (!$currentUser) {
    header("Location: /login");
    exit;
}
?>
    <div class="container py-5">
        <!-- Заголовок -->
        <div class="row mb-5">
            <div class="col-12">
                <h1 class="display-5 fw-bold text-primary">
                    <i class="bi bi-person-circle me-2"></i>Мой профиль
                </h1>
                <p class="text-muted">Управление вашей учетной записью</p>
            </div>
        </div>

        <!-- Основное содержимое -->
        <div class="row">
            <!-- Левая колонка: Информация профиля -->
            <div class="col-lg-4 col-md-5 mb-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-primary text-white py-3">
                        <h5 class="mb-0"><i class="bi bi-person-badge me-2"></i>Личные данные</h5>
                    </div>
                    <div class="card-body text-center p-4">
                        <!-- Аватар -->
                        <div class="mb-4">
                            <img src="<?= htmlspecialchars($currentUser['photo'] ?? 'img/default-avatar.png') ?>" 
                                 alt="Аватар" 
                                 class="rounded-circle border border-3 border-primary"
                                 style="width: 150px; height: 150px; object-fit: cover;">
                            <div class="mt-3">
                                <span class="badge bg-primary fs-6 px-3 py-2">ID: <?= $currentUser['id'] ?></span>
                            </div>
                        </div>
                        
                        <!-- Основная информация -->
                        <h4 class="mb-3"><?= htmlspecialchars($currentUser['name']) ?></h4>
                        <p class="text-muted mb-4">
                            <i class="bi bi-envelope me-2"></i>
                            <?= htmlspecialchars($currentUser['mail']) ?>
                        </p>
                        
                        <!-- Статистика -->
                        <div class="border-top pt-4">
                            <h6 class="text-uppercase text-muted mb-3">Активность</h6>
                            <div class="row text-center">
                                <div class="col-6">
                                    <div class="p-3 bg-light rounded">
                                        <h4 class="text-primary mb-1"><?= htmlspecialchars($countTasks) ?></h4>
                                        <small class="text-muted">Задач</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="p-3 bg-light rounded">
                                        <h4 class="text-primary mb-1"><?= htmlspecialchars($countGroups) ?></h4>
                                        <small class="text-muted">Групп</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Правая колонка: Редактирование профиля -->
            <div class="col-lg-8 col-md-7">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-primary text-white py-3">
                        <h5 class="mb-0"><i class="bi bi-pencil-square me-2"></i>Редактировать профиль</h5>
                    </div>
                    <div class="card-body p-4">
                        <?php if(isset($_GET['success'])): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="bi bi-check-circle me-2"></i><?= htmlspecialchars($_GET['success']) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        <?php endif; ?>

                        <?php if(isset($_GET['error'])): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-triangle me-2"></i><?= htmlspecialchars($_GET['error']) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        <?php endif; ?>

                        <form action="/user/update" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="id" value="<?= $currentUser['id'] ?>">
                            
                            <!-- Имя -->
                            <div class="mb-4">
                                <label class="form-label fw-bold">
                                    <i class="bi bi-person me-2"></i>Имя пользователя
                                </label>
                                <input type="text" 
                                       class="form-control form-control-lg" 
                                       name="name" 
                                       value="<?= htmlspecialchars($currentUser['name']) ?>"
                                       required
                                       placeholder="Введите ваше имя">
                                <div class="form-text">Имя, которое видят другие пользователи</div>
                            </div>

                            <!-- Email -->
                            <div class="mb-4">
                                <label class="form-label fw-bold">
                                    <i class="bi bi-envelope me-2"></i>Email адрес
                                </label>
                                <input type="email" 
                                       class="form-control form-control-lg" 
                                       name="mail" 
                                       value="<?= htmlspecialchars($currentUser['mail']) ?>"
                                       required
                                       placeholder="example@mail.ru">
                                <div class="form-text">Ваш email для входа в систему</div>
                            </div>

                            <!-- Смена аватара -->
                            <div class="mb-5">
                                <label class="form-label fw-bold">
                                    <i class="bi bi-image me-2"></i>Смена аватара
                                </label>
                                <div class="input-group mb-3">
                                    <input input type="file" class="form-control" id="photo" placeholder="Фото" name="photo">
                                    <label class="input-group-text" for="photoInput">
                                        <i class="bi bi-upload"></i>
                                    </label>
                                </div>
                                <div class="form-text">
                                    Загрузите изображение для вашего профиля. Поддерживаются JPG, PNG, GIF.
                                </div>
                                
                                <!-- Предпросмотр аватара -->
                                <div class="mt-3" id="avatarPreview" style="display: none;">
                                    <p class="text-muted mb-2">Предпросмотр:</p>
                                    <img id="previewImage" 
                                         src="" 
                                         alt="Предпросмотр"
                                         class="rounded-circle border"
                                         style="width: 100px; height: 100px; object-fit: cover;">
                                </div>
                            </div>

                            <div class="d-flex gap-3">
                                <button type="submit" class="btn btn-primary btn-lg px-4">
                                    <i class="bi bi-save me-2"></i> Сохранить изменения
                                </button>
                                <button type="button" class="btn btn-outline-secondary btn-lg px-4" onclick="resetForm()">
                                    <i class="bi bi-arrow-counterclockwise me-2"></i> Отмена
                                </button>
                            </div>
                        </form>

                        <!-- Безопасность -->
                        <div class="mt-5 pt-4 border-top">
                            <h5 class="fw-bold mb-4">
                                <i class="bi bi-shield-lock me-2"></i>Безопасность
                            </h5>
                            <div class="alert alert-warning">
                                <i class="bi bi-info-circle me-2"></i>
                                Для смены пароля или других настроек безопасности обратитесь к администратору.
                            </div>
                        </div>

                        <!-- Дополнительные действия -->
                        <div class="mt-5 pt-4 border-top">
                            <h5 class="fw-bold mb-4">
                                <i class="bi bi-gear me-2"></i>Дополнительные действия
                            </h5>
                            <div class="d-flex gap-3">
                                <a href="/" class="btn btn-outline-primary">
                                    <i class="bi bi-house me-2"></i> На главную
                                </a>
                                            <form action="/user/logout" method="POST">
                                                <input type="submit" value="Выйти" class="btn btn-danger">
                                            </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <script>
    // Предпросмотр аватара
    document.getElementById('photoInput').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById('previewImage');
                preview.src = e.target.result;
                document.getElementById('avatarPreview').style.display = 'block';
            }
            reader.readAsDataURL(file);
        }
    });

    // Сброс формы
    function resetForm() {
        document.querySelector('form').reset();
        document.getElementById('avatarPreview').style.display = 'none';
    }

    // Автоматическое скрытие уведомлений через 5 секунд
    setTimeout(() => {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, 5000);
    </script>

    <style>
    body {
        min-height: 100vh;
    }
    
    .card {
        border-radius: 15px;
        overflow: hidden;
        transition: transform 0.3s ease;
    }
    
    .card:hover {
        transform: translateY(-5px);
    }
    
    .form-control:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }
    
    .btn-primary {
        background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
        border: none;
    }
    
    .btn-primary:hover {
        background: linear-gradient(135deg, #0a58ca 0%, #084298 100%);
    }
    
    @media (max-width: 768px) {
        .container {
            padding-top: 2rem !important;
            padding-bottom: 2rem !important;
        }
        
        .card-body {
            padding: 1.5rem !important;
        }
    }
    </style>


<?php
include("src/views/src/includes/footer.php");
?>