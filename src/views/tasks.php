<?php
include("src/views/src/includes/header.php");
session_start();
use app\controllers\Task;
use app\controllers\Group;
use app\controllers\User;

$userId = $_SESSION['id'] ?? 0;
$currentUser = User::getUserById($userId);

if(!$currentUser) {
    header("Location: /login");
    exit;
}

// Получаем все группы пользователя
$groups = Group::getGroupByUser(['userId' => $userId]);

// Получаем все задачи пользователя
$allTasks = [];
foreach($groups as $group) {
    $groupTasks = Task::getTaskByGroup($group['id']);
    if(is_array($groupTasks)) {
        foreach($groupTasks as $task) {
            if(isset($task['fk_user_id']) && $task['fk_user_id'] == $userId) {
                $task['group_name'] = $group['title'];
                $task['group_id'] = $group['id'];
                $allTasks[] = $task;
            }
        }
    }
}

// Сортируем по дате
if(!empty($allTasks)) {
    usort($allTasks, function($a, $b) {
        return strtotime($b['created_at']) - strtotime($a['created_at']);
    });
}

// Фильтруем по статусам
$openTasks = [];
$activeTasks = [];
$completedTasks = [];

foreach($allTasks as $task) {
    if($task['status'] == 0) {
        $openTasks[] = $task;
    } elseif($task['status'] == 1) {
        $activeTasks[] = $task;
    } elseif($task['status'] == 2) {
        $completedTasks[] = $task;
    }
}
?>

<div class="container py-4">
    <h1 class="mb-4">
        <i class="bi bi-list-task text-primary me-2"></i>Мои задачи
    </h1>

    <!-- Статистика -->
    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="card border-info">
                <div class="card-body text-center py-3">
                    <h3 class="text-info mb-0"><?= count($openTasks) ?></h3>
                    <small class="text-muted">Открытых</small>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card border-warning">
                <div class="card-body text-center py-3">
                    <h3 class="text-warning mb-0"><?= count($activeTasks) ?></h3>
                    <small class="text-muted">В работе</small>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card border-success">
                <div class="card-body text-center py-3">
                    <h3 class="text-success mb-0"><?= count($completedTasks) ?></h3>
                    <small class="text-muted">Завершённых</small>
                </div>
            </div>
        </div>
    </div>

    <?php if(empty($allTasks)): ?>
        <div class="alert alert-info text-center py-5">
            <i class="bi bi-inbox fs-1 mb-3"></i>
            <p class="mb-0">Вам ещё не назначили задач</p>
        </div>
    <?php else: ?>
        <!-- Открытые -->
        <?php if(!empty($openTasks)): ?>
        <div class="mb-5">
            <h4 class="mb-3">
                <span class="badge bg-info"><?= count($openTasks) ?></span>
                Открытые задачи
            </h4>
            <div class="row g-3">
                <?php foreach($openTasks as $task): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card border-info h-100">
                        <div class="card-header bg-info bg-opacity-10 py-2">
                            <small class="text-muted">
                                <i class="bi bi-people me-1"></i>
                                <?= htmlspecialchars($task['group_name']) ?>
                            </small>
                        </div>
                        <div class="card-body py-3">
                            <h6><?= htmlspecialchars($task['title']) ?></h6>
                            <small class="text-muted d-block">
                                <?= date('d.m.Y', strtotime($task['created_at'])) ?>
                            </small>
                        </div>
                        <div class="card-footer bg-info bg-opacity-10 py-2">
                            <a href="/group?id=<?= $task['group_id'] ?>" class="btn btn-info btn-sm">
                                Перейти
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- В работе -->
        <?php if(!empty($activeTasks)): ?>
        <div class="mb-5">
            <h4 class="mb-3">
                <span class="badge bg-warning"><?= count($activeTasks) ?></span>
                В работе
            </h4>
            <div class="row g-3">
                <?php foreach($activeTasks as $task): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card border-warning h-100">
                        <div class="card-header bg-warning bg-opacity-10 py-2">
                            <small class="text-muted">
                                <i class="bi bi-people me-1"></i>
                                <?= htmlspecialchars($task['group_name']) ?>
                            </small>
                        </div>
                        <div class="card-body py-3">
                            <h6><?= htmlspecialchars($task['title']) ?></h6>
                            <small class="text-muted d-block">
                                <?= date('d.m.Y', strtotime($task['created_at'])) ?>
                            </small>
                        </div>
                        <div class="card-footer bg-warning bg-opacity-10 py-2">
                            <a href="/group?id=<?= $task['group_id'] ?>" class="btn btn-warning btn-sm">
                                Перейти
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Завершённые -->
        <?php if(!empty($completedTasks)): ?>
        <div class="mb-5">
            <h4 class="mb-3">
                <span class="badge bg-success"><?= count($completedTasks) ?></span>
                Завершённые
            </h4>
            <div class="row g-3">
                <?php foreach($completedTasks as $task): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card border-success h-100">
                        <div class="card-header bg-success bg-opacity-10 py-2">
                            <small class="text-muted">
                                <i class="bi bi-people me-1"></i>
                                <?= htmlspecialchars($task['group_name']) ?>
                            </small>
                        </div>
                        <div class="card-body py-3">
                            <h6><?= htmlspecialchars($task['title']) ?></h6>
                            <div class="small">
                                <span class="text-muted">
                                    <i class="bi bi-calendar-plus me-1"></i>
                                    <?= date('d.m.Y', strtotime($task['created_at'])) ?>
                                </span>
                                <?php if($task['closed_at']): ?>
                                <br>
                                <span class="text-success">
                                    <i class="bi bi-calendar-check me-1"></i>
                                    <?= date('d.m.Y', strtotime($task['closed_at'])) ?>
                                </span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="card-footer bg-success bg-opacity-10 py-2">
                            <a href="/group?id=<?= $task['group_id'] ?>" class="btn btn-success btn-sm">
                                Перейти
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<?php include("src/views/src/includes/footer.php"); ?>