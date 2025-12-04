<?php
use app\controllers\Task;

$confirm_skip = $_GET['confirm_skip'] ?? null;

// Если есть подтверждение пропуска статуса
if($confirm_skip && isset($_SESSION['id']) && isset($groupAdmin['user_id']) && $_SESSION['id'] == $groupAdmin['user_id']) {
    $taskToSkip = Task::getTaskById($confirm_skip);
    if($taskToSkip && $taskToSkip['status'] == 0) {
        // Показываем подтверждение пропуска статуса
        ?>
        <div class="modal fade show" id="confirmSkipModal" tabindex="-1" aria-hidden="false" style="display: block; background: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <form action="/group/task/update" method="POST">
                        <input type="hidden" name="id" value="<?= $taskToSkip['id'] ?>">
                        <input type="hidden" name="status" value="2">
                        <input type="hidden" name="groupId" value="<?= $groupId ?>">
                        <input type="hidden" name="confirm_skip" value="1">
                        
                        <div class="modal-header bg-warning">
                            <h5 class="modal-title"><i class="bi bi-exclamation-triangle"></i> Внимание!</h5>
                            <button type="button" class="btn-close" onclick="window.location.href='/group?id=<?= $groupId ?>'" aria-label="Закрыть"></button>
                        </div>
                        <div class="modal-body">
                            <div class="alert alert-warning">
                                <h6>Пропуск этапа работы</h6>
                                <p>Вы собираетесь перевести задачу "<strong><?= htmlspecialchars($taskToSkip['title']) ?></strong>" из статуса <strong>"📋 Открытая"</strong> сразу в <strong>"✅ Завершенная"</strong>.</p>
                                <p class="mb-0">Это означает, что задача будет считаться выполненной без перехода в работу.</p>
                            </div>
                            
                            <div class="form-check mt-3">
                                <input class="form-check-input" type="checkbox" name="understand_skip" id="understandSkip" required>
                                <label class="form-check-label" for="understandSkip">
                                    Я понимаю, что пропускаю этап работы над задачей
                                </label>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" onclick="window.location.href='/group?id=<?= $groupId ?>'">Отмена</button>
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-check-circle"></i> Да, завершить сразу
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var modal = new bootstrap.Modal(document.getElementById('confirmSkipModal'));
                modal.show();
            });
        </script>
        <?php
    }
}
?>


<!-- Модальное окно для просмотра задачи -->
<div class="modal fade" id="modalTask" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTaskTitle">Задача</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
            </div>
            <div class="modal-body p-4">
                <form id="taskForm" method="POST" action="/group/task/update">
                    <input type="hidden" name="id" id="taskModalId">
                    <input type="hidden" name="groupId" value="<?= $groupId ?>">
                    
                    <!-- Информация о задаче -->
                    <div class="row g-4">
                        <!-- Левая колонка: Основная информация -->
                        <div class="col-lg-8">
                            <div class="mb-4">
                                <h4 id="taskTitle" class="text-primary mb-3"></h4>
                                <div>
                                    <h6 class="mb-2">Описание:</h6>
                                    <div id="taskDescription" class="border rounded p-4 bg-light min-h-100" style="min-height: 150px; max-height: 300px; overflow-y: auto;">
                                        <p class="text-muted mb-0">Нет описания</p>
                                    </div>
                                </div>
                            </div>
                            
  <!-- Блок изменения статуса (только для назначенного пользователя) -->
<div id="statusChangeSection" class="mt-4 border-top pt-4" style="display: none;">
    <h5 class="mb-3">📊 Изменить статус задачи</h5>
    <div class="alert alert-info mb-4">
        <i class="bi bi-info-circle"></i> Вы можете изменить статус этой задачи, так как она назначена вам.
    </div>
    
    <div class="row g-4">
        <!-- Открытая → Активная -->
        <div class="col-md-4" id="startWorkCard">
            <div class="card border-warning h-100">
                <div class="card-body text-center p-4 d-flex flex-column">
                    <h5 class="card-title mb-3">🕑 В работу</h5>
                    <p class="card-text small mb-4">Начать работу над задачей</p>
                    <button type="button" class="btn btn-warning w-100 mt-auto start-task-btn py-2">
                        <i class="bi bi-play-circle me-2"></i> Начать работу
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Активная → Завершенная -->
        <div class="col-md-4" id="completeCard">
            <div class="card border-success h-100">
                <div class="card-body text-center p-4 d-flex flex-column">
                    <h5 class="card-title mb-3">✅ Завершить</h5>
                    <p class="card-text small mb-4">Завершить выполнение задачи</p>
                    <button type="button" class="btn btn-success w-100 mt-auto complete-task-btn py-2">
                        <i class="bi bi-check-circle me-2"></i> Завершить
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Открытая → Завершенная (с подтверждением) -->
        <div class="col-md-4" id="skipStatusCard">
            <div class="card border-danger h-100">
                <div class="card-body text-center p-4 d-flex flex-column">
                    <h5 class="card-title mb-3">⚠️ Сразу завершить</h5>
                    <p class="card-text small mb-4">Пропустить статус "В работе"</p>
                    <button type="button" id="skipToCompletedBtn" class="btn btn-outline-danger w-100 mt-auto py-2">
                        <i class="bi bi-lightning me-2"></i> Сразу завершить
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
                            
                            <!-- Форма редактирования (только для админа) -->
                            <div id="adminEditSection" class="mt-4 border-top pt-4" style="display: none;">
                                <h5 class="mb-4"><i class="bi bi-pencil-square me-2"></i>Редактирование задачи</h5>
                                <div>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Название задачи</label>
                                        <input type="text" class="form-control form-control-lg" name="title" id="editTitle" required>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Описание задачи</label>
                                        <textarea class="form-control" name="description" id="editDescription" rows="5" style="resize: vertical;"></textarea>
                                    </div>
                                    
                                    <div class="row g-3 mb-4">
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold">Назначить пользователю</label>
                                            <select class="form-select" name="user_id" id="editUserId">
                                                <option value="">Не назначено</option>
                                                <?php foreach($groupUsers as $user): ?>
                                                    <option value="<?= $user['id'] ?>">
                                                        <?= htmlspecialchars($user['name']) ?> (<?= htmlspecialchars($user['email'] ?? '') ?>)
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold">Статус задачи</label>
                                            <select class="form-select" name="status" id="editStatus">
                                                <option value="0">📋 Открытая</option>
                                                <option value="1">🕑 Активная</option>
                                                <option value="2">✅ Завершенная</option>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div class="d-flex gap-3">
                                        <button type="submit" class="btn btn-primary btn-lg px-4">
                                            <i class="bi bi-save me-2"></i> Сохранить изменения
                                        </button>
                                        <button type="button" class="btn btn-outline-secondary btn-lg px-4" id="cancelEditBtn">
                                            Отмена
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Правая колонка: Детали задачи -->
                        <div class="col-lg-4">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="card-header bg-primary text-white py-3">
                                    <h6 class="mb-0"><i class="bi bi-card-checklist me-2"></i> Детали задачи</h6>
                                </div>
                                <div class="card-body p-4">
                                    <div class="mb-4">
                                        <small class="text-muted d-block mb-2">Статус</small>
                                        <div id="taskStatusBadge" class="mt-1"></div>
                                    </div>
                                    
                                    <div class="mb-4">
                                        <small class="text-muted d-block mb-2">Назначена</small>
                                        <div class="d-flex align-items-center mt-2">
                                            <div class="flex-shrink-0">
                                                <img id="taskUserAvatar" src="" alt="avatar" 
                                                     class="rounded-circle border" 
                                                     style="width: 50px; height: 50px; object-fit: cover;">
                                            </div>
                                            <div class="flex-grow-1 ms-3">
                                                <strong id="taskUserName" class="d-block fs-6"></strong>
                                                <small class="text-muted d-block mt-1" id="taskUserEmail" style="word-break: break-word;"></small>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-4">
                                        <small class="text-muted d-block mb-2">Создана</small>
                                        <div class="mt-2">
                                            <i class="bi bi-calendar-plus text-primary me-2"></i>
                                            <span id="taskCreated" class="fs-6"></span>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-4">
                                        <small class="text-muted d-block mb-2">Завершена</small>
                                        <div class="mt-2">
                                            <i class="bi bi-calendar-check text-success me-2"></i>
                                            <span id="taskClosed" class="fs-6">—</span>
                                        </div>
                                    </div>
                                    
                                    <div class="mt-5 pt-4 border-top">
                                        <small class="text-muted d-block mb-2">ID задачи</small>
                                        <code id="taskIdCode" class="fs-6 bg-light p-2 rounded d-block">#</code>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            
            <div class="modal-footer px-4 py-3">
                <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">
                    <i class="bi bi-x-lg me-2"></i> Закрыть
                </button>
                
                <!-- Кнопки действий -->
                <?php if(isset($_SESSION['id']) && isset($groupAdmin['user_id']) && $_SESSION['id'] == $groupAdmin['user_id']): ?>
                <button type="button" class="btn btn-danger px-4" id="deleteTaskBtn">
                    <i class="bi bi-trash me-2"></i> Удалить
                </button>
                
                <button type="button" class="btn btn-warning px-4" id="toggleEditBtn">
                    <i class="bi bi-pencil me-2"></i> Редактировать
                </button>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Форма для удаления задачи (скрытая) -->
<form id="deleteTaskForm" method="POST" action="/group/task/delete" style="display: none;">
    <input type="hidden" name="id" id="deleteTaskId">
    <input type="hidden" name="groupId" value="<?= $groupId ?>">
</form>

<!-- Модальное окно подтверждения начала задачи -->
<div class="modal fade" id="confirmStartTaskModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-play-circle text-warning me-2"></i> Начать работу над задачей</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="alert alert-info mb-4">
                    <i class="bi bi-info-circle me-2"></i>
                    <strong>Подтверждение начала работы</strong>
                </div>
                <p class="fs-5 mb-3">Вы уверены, что хотите начать работу над задачей?</p>
                <p class="text-muted mb-4">Задача перейдет в статус <span class="badge bg-warning text-dark">🕑 В работе</span></p>
                <div class="border rounded p-3 bg-light">
                    <strong>Задача:</strong> <span id="startTaskTitle"></span>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Отмена</button>
                <button type="button" class="btn btn-warning px-4" id="confirmStartTaskBtn">
                    <i class="bi bi-play-circle me-2"></i> Да, начать работу
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Модальное окно подтверждения завершения задачи -->
<div class="modal fade" id="confirmCompleteTaskModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-check-circle text-success me-2"></i> Завершить задачу</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="alert alert-success mb-4">
                    <i class="bi bi-check-circle me-2"></i>
                    <strong>Подтверждение завершения</strong>
                </div>
                <p class="fs-5 mb-3">Вы уверены, что хотите завершить эту задачу?</p>
                <p class="text-muted mb-4">Задача перейдет в статус <span class="badge bg-success">✅ Завершена</span></p>
                <div class="border rounded p-3 bg-light">
                    <strong>Задача:</strong> <span id="completeTaskTitle"></span>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Отмена</button>
                <button type="button" class="btn btn-success px-4" id="confirmCompleteTaskBtn">
                    <i class="bi bi-check-circle me-2"></i> Да, завершить
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Модальное окно подтверждения пропуска статуса -->
<div class="modal fade" id="confirmSkipStatusModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title"><i class="bi bi-exclamation-triangle me-2"></i> Внимание!</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="alert alert-warning mb-4">
                    <h6 class="alert-heading mb-2">Пропуск этапа работы</h6>
                    <p class="mb-0">Вы собираетесь перевести задачу из статуса <strong>"📋 Открытая"</strong> сразу в <strong>"✅ Завершенная"</strong>.</p>
                </div>
                
                <div class="border rounded p-4 mb-4 bg-light">
                    <strong>Задача:</strong> <span id="skipTaskTitle"></span>
                    <p class="text-muted mt-2 mb-0">Это означает, что задача будет считаться выполненной без перехода в работу.</p>
                </div>
                
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="confirmSkipCheckbox" style="width: 20px; height: 20px;">
                    <label class="form-check-label fs-6 ms-2" for="confirmSkipCheckbox">
                        Я понимаю, что пропускаю этап работы над задачей
                    </label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Отмена</button>
                <button type="button" class="btn btn-success px-4" id="confirmSkipStatusBtn" disabled>
                    <i class="bi bi-check-circle me-2"></i> Да, завершить сразу
                </button>
            </div>
        </div>
    </div>
</div>

<style>
.min-h-100 {
    min-height: 100px;
}

/* Статусы с иконками */
.status-badge {
    padding: 0.75rem 1.25rem;
    border-radius: 12px;
    font-weight: 600;
    display: inline-block;
    font-size: 1rem;
}

.status-open {
    background-color: #d1ecf1;
    color: #0c5460;
    border: 2px solid #bee5eb;
}

.status-active {
    background-color: #fff3cd;
    color: #856404;
    border: 2px solid #ffeaa7;
}

.status-completed {
    background-color: #d4edda;
    color: #155724;
    border: 2px solid #c3e6cb;
}

/* Улучшенные карточки */
.card {
    border-radius: 12px;
    transition: all 0.3s ease;
    border: 2px solid transparent;
}

.card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.1);
}

.card.border-warning {
    border-color: #ffc107;
}

.card.border-success {
    border-color: #198754;
}

.card.border-danger {
    border-color: #dc3545;
}

/* Кнопки */
.btn {
    border-radius: 8px;
    font-weight: 500;
    transition: all 0.2s ease;
}

.btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

/* Плавные переходы */
#statusChangeSection,
#adminEditSection {
    transition: all 0.3s ease;
}

/* Адаптивность */
@media (max-width: 992px) {
    .modal-xl {
        max-width: 95%;
        margin: 1rem auto;
    }
    
    .col-lg-8, .col-lg-4 {
        width: 100%;
    }
    
    .col-lg-4 {
        margin-top: 2rem;
    }
}

@media (max-width: 768px) {
    .modal-dialog {
        margin: 0.5rem;
    }
    
    .card-body {
        padding: 1.5rem !important;
    }
    
    .btn {
        padding: 0.5rem 1rem;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const currentUserId = <?= json_encode($_SESSION['id'] ?? 0) ?>;
    const isGroupAdmin = <?= json_encode(isset($_SESSION['id']) && isset($groupAdmin['user_id']) && $_SESSION['id'] == $groupAdmin['user_id']) ?>;
    const groupId = <?= $groupId ?>;
    
    // Инициализация модальных окон Bootstrap
    const confirmSkipStatusModal = new bootstrap.Modal(document.getElementById('confirmSkipStatusModal'));
    const confirmStartTaskModal = new bootstrap.Modal(document.getElementById('confirmStartTaskModal'));
    const confirmCompleteTaskModal = new bootstrap.Modal(document.getElementById('confirmCompleteTaskModal'));
    
    // Текущие данные задачи
    let currentTask = {};
    let pendingStatusChange = null;
    
    // Обработка открытия модального окна задачи
document.getElementById('modalTask').addEventListener('show.bs.modal', function(event) {
    const button = event.relatedTarget;
    
    // Получаем данные задачи
    currentTask = {
        id: button.getAttribute('data-task-id'),
        userId: button.getAttribute('data-task-user-id') ? parseInt(button.getAttribute('data-task-user-id')) : null,
        title: button.getAttribute('data-task-title'),
        description: button.getAttribute('data-task-description'),
        userName: button.getAttribute('data-task-user'),
        userEmail: button.getAttribute('data-task-user-email') || '',
        userPhoto: button.querySelector('img')?.src || '',
        created: button.getAttribute('data-task-created'),
        closed: button.getAttribute('data-task-closed') || null, // Добавляем дату закрытия
        status: getStatusFromCard(button),
        groupId: groupId
    };
        console.log('Загружена задача:', currentTask);
        
        // Заполняем модальное окно
        fillTaskModal(currentTask);
        
        // Настраиваем права доступа
        setupAccessControls(currentTask);
    });
    
    // Определение статуса по карточке
    function getStatusFromCard(button) {
        const card = button.querySelector('.card');
        if(card.classList.contains('border-info')) return 'open';
        if(card.classList.contains('border-warning')) return 'active';
        if(card.classList.contains('border-success')) return 'completed';
        return 'unknown';
    }
    
    // Заполнение модального окна данными
    function fillTaskModal(task) {
        // Основные поля
        document.getElementById('taskModalId').value = task.id;
        document.getElementById('modalTaskTitle').textContent = 'Задача: ' + task.title.substring(0, 50) + (task.title.length > 50 ? '...' : '');
        document.getElementById('taskTitle').textContent = task.title;
        document.getElementById('taskCreated').textContent = formatDate(task.created);
          if(task.closed) {
        document.getElementById('taskClosed').textContent = formatDate(task.closed);
    } else {
        document.getElementById('taskClosed').textContent = '—';
    }
        // Описание
        const descEl = document.getElementById('taskDescription');
        if(task.description && task.description.trim() !== '') {
            descEl.innerHTML = task.description.replace(/\n/g, '<br>');
            descEl.classList.remove('text-muted');
        } else {
            descEl.innerHTML = '<p class="text-muted mb-0">Нет описания</p>';
        }
        
        // Статус
        const statusBadge = document.getElementById('taskStatusBadge');
        const statusMap = {
            'open': '<span class="status-badge status-open">📋 Открытая задача</span>',
            'active': '<span class="status-badge status-active">🕑 В работе</span>',
            'completed': '<span class="status-badge status-completed">✅ Завершена</span>'
        };
        statusBadge.innerHTML = statusMap[task.status] || '<span class="badge bg-secondary">Неизвестно</span>';
        
        // Пользователь
        const avatarUrl = task.userPhoto || '/img/default-avatar.png';
        document.getElementById('taskUserAvatar').src = avatarUrl;
        document.getElementById('taskUserName').textContent = task.userName || 'Не назначена';
        document.getElementById('taskUserEmail').textContent = task.userEmail || '';
        
        // Даты
        document.getElementById('taskCreated').textContent = formatDate(task.created);
        document.getElementById('taskClosed').textContent = '—';
        
        // ID задачи
        document.getElementById('taskIdCode').textContent = `#${task.id}`;
        
        // Для формы редактирования
        document.getElementById('editTitle').value = task.title;
        document.getElementById('editDescription').value = task.description || '';
        document.getElementById('editUserId').value = task.userId || '';
        
        // Статус в форме
        const statusValue = { 'open': '0', 'active': '1', 'completed': '2' }[task.status] || '0';
        document.getElementById('editStatus').value = statusValue;
    }
    
    // Форматирование даты
    function formatDate(dateString) {
        try {
            const date = new Date(dateString);
            return date.toLocaleDateString('ru-RU', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        } catch(e) {
            return dateString;
        }
    }
    
    // Настройка прав доступа
    function setupAccessControls(task) {
    // Используем нестрогое сравнение, так как userId может быть строкой "0" или числом 0
    const isAssignedUser = (task.userId == currentUserId && task.userId != 0 && task.userId != null);
    const isTaskCompleted = (task.status === 'completed');
    
        
        console.log('Права доступа:', { isAssignedUser, isTaskCompleted, isGroupAdmin, task });
        
        // Скрываем все блоки управления
        document.getElementById('statusChangeSection').style.display = 'none';
        document.getElementById('adminEditSection').style.display = 'none';
        
        // Скрываем кнопки редактирования и удаления по умолчанию
        if(document.getElementById('deleteTaskBtn')) {
            document.getElementById('deleteTaskBtn').style.display = 'none';
        }
        if(document.getElementById('toggleEditBtn')) {
            document.getElementById('toggleEditBtn').style.display = 'none';
        }
        
        // Права администратора
        if(isGroupAdmin) {
            if(document.getElementById('deleteTaskBtn')) {
                document.getElementById('deleteTaskBtn').style.display = 'inline-block';
                
                // Обработка удаления
                document.getElementById('deleteTaskBtn').onclick = function() {
                    if(confirm('Вы уверены, что хотите удалить эту задачу? Это действие нельзя отменить.')) {
                        document.getElementById('deleteTaskId').value = currentTask.id;
                        document.getElementById('deleteTaskForm').submit();
                    }
                };
            }
            if(document.getElementById('toggleEditBtn')) {
                document.getElementById('toggleEditBtn').style.display = 'inline-block';
                
                // Кнопка переключения редактирования
                document.getElementById('toggleEditBtn').onclick = function() {
                    const editSection = document.getElementById('adminEditSection');
                    const isVisible = editSection.style.display === 'block';
                    editSection.style.display = isVisible ? 'none' : 'block';
                    this.innerHTML = isVisible ? 
                        '<i class="bi bi-pencil me-2"></i> Редактировать' : 
                        '<i class="bi bi-eye me-2"></i> Просмотр';
                };
            }
            
            // Кнопка отмены редактирования
            if(document.getElementById('cancelEditBtn')) {
                document.getElementById('cancelEditBtn').onclick = function() {
                    document.getElementById('adminEditSection').style.display = 'none';
                    if(document.getElementById('toggleEditBtn')) {
                        document.getElementById('toggleEditBtn').innerHTML = '<i class="bi bi-pencil me-2"></i> Редактировать';
                    }
                    fillTaskModal(currentTask); // Возвращаем исходные данные
                };
            }
        }
        
        // Права назначенного пользователя (если задача не завершена)
        if(isAssignedUser && !isTaskCompleted) {
            document.getElementById('statusChangeSection').style.display = 'block';
            
            // Настройка кнопок изменения статуса
            setupStatusButtons(task);
        }
    }
// Настройка кнопок изменения статуса
function setupStatusButtons(task) {
    // Получаем элементы карточек
    const startWorkCard = document.getElementById('startWorkCard');
    const completeCard = document.getElementById('completeCard');
    const skipStatusCard = document.getElementById('skipStatusCard');
    
    // Получаем кнопки
    const startButtons = document.querySelectorAll('.start-task-btn');
    const completeButtons = document.querySelectorAll('.complete-task-btn');
    
    // Сначала скрываем все карточки
    startWorkCard.style.display = 'none';
    completeCard.style.display = 'none';
    skipStatusCard.style.display = 'none';
    
    console.log('Настройка кнопок для статуса:', task.status);
    
    // В зависимости от текущего статуса
    if(task.status === 'open') {
        console.log('Задача открыта - показываем "Начать работу" и "Сразу завершить"');
        // Показываем карточку "Начать работу" и "Сразу завершить"
        startWorkCard.style.display = 'block';
        skipStatusCard.style.display = 'block';
        
        // Настраиваем кнопку "Начать работу"
        startButtons.forEach(btn => {
            btn.style.display = 'block';
            btn.onclick = function() {
                showStartTaskConfirmation();
            };
        });
        
        // Кнопка "Сразу завершить" - открывает модальное окно подтверждения
        document.getElementById('skipToCompletedBtn').onclick = function(e) {
            e.preventDefault();
            showSkipStatusConfirmation();
        };
        
    } else if(task.status === 'active') {
        console.log('Задача активна - показываем "Завершить"');
        // Показываем только карточку "Завершить"
        completeCard.style.display = 'block';
        
        // Настраиваем кнопку "Завершить"
        completeButtons.forEach(btn => {
            btn.style.display = 'block';
            btn.onclick = function() {
                showCompleteTaskConfirmation();
            };
        });
    }
}
    // Показать подтверждение начала задачи
    function showStartTaskConfirmation() {
        pendingStatusChange = 1; // Статус "В работе"
        document.getElementById('startTaskTitle').textContent = currentTask.title;
        confirmStartTaskModal.show();
    }
    
    // Показать подтверждение завершения задачи
    function showCompleteTaskConfirmation() {
        pendingStatusChange = 2; // Статус "Завершена"
        document.getElementById('completeTaskTitle').textContent = currentTask.title;
        confirmCompleteTaskModal.show();
    }
    
    // Показать подтверждение пропуска статуса
    function showSkipStatusConfirmation() {
        pendingStatusChange = 2; // Статус "Завершена" (пропуск)
        document.getElementById('skipTaskTitle').textContent = currentTask.title;
        document.getElementById('confirmSkipCheckbox').checked = false;
        document.getElementById('confirmSkipStatusBtn').disabled = true;
        confirmSkipStatusModal.show();
    }
    
    // Обработчик для чекбокса пропуска статуса
    document.getElementById('confirmSkipCheckbox').addEventListener('change', function() {
        document.getElementById('confirmSkipStatusBtn').disabled = !this.checked;
    });
    
    // Обработчик кнопки подтверждения пропуска статуса
    document.getElementById('confirmSkipStatusBtn').addEventListener('click', function() {
        submitStatusChange(pendingStatusChange, true); // true = пропуск статуса
        confirmSkipStatusModal.hide();
    });
    
    // Обработчик кнопки подтверждения начала задачи
    document.getElementById('confirmStartTaskBtn').addEventListener('click', function() {
        submitStatusChange(pendingStatusChange);
        confirmStartTaskModal.hide();
    });
    
    // Обработчик кнопки подтверждения завершения задачи
    document.getElementById('confirmCompleteTaskBtn').addEventListener('click', function() {
        submitStatusChange(pendingStatusChange);
        confirmCompleteTaskModal.hide();
    });
    
   // Функция отправки изменения статуса
function submitStatusChange(newStatus, isSkip = false) {
    // Создаем отдельную форму для изменения статуса
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '/group/task/update';
    form.style.display = 'none';
    
    // Добавляем обязательные поля
    const idInput = document.createElement('input');
    idInput.type = 'hidden';
    idInput.name = 'id';
    idInput.value = currentTask.id;
    
    const statusInput = document.createElement('input');
    statusInput.type = 'hidden';
    statusInput.name = 'status';
    statusInput.value = newStatus;
    
    const groupIdInput = document.createElement('input');
    groupIdInput.type = 'hidden';
    groupIdInput.name = 'groupId';
    groupIdInput.value = currentTask.groupId;
    
    form.appendChild(idInput);
    form.appendChild(statusInput);
    form.appendChild(groupIdInput);
    
    // Если это пропуск статуса, добавляем параметр
    if(isSkip) {
        const skipInput = document.createElement('input');
        skipInput.type = 'hidden';
        skipInput.name = 'skip_confirmed';
        skipInput.value = '1';
        form.appendChild(skipInput);
    }
    
    // Добавляем форму на страницу и отправляем
    document.body.appendChild(form);
    form.submit();
}
});
</script>