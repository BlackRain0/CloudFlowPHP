<?php

use app\controllers\Group;
use app\controllers\Task;
use app\controllers\User;

include("src/views/src/includes/header.php");

$success = $_GET['success'] ?? null;
$error = $_GET['error'] ?? null;

$groupId = $_GET['id'];
$currentGroup = Group::getGroupById($groupId);
$groupUsers = User::getUserByGroup($groupId);
$groupAdmin = Group::getGroupAdmin($groupId);
$groupTasks = Task::getTaskByGroup($groupId);
$groupTasks = is_array($groupTasks) ? $groupTasks : []; // 0 -open, 1- active, 3-done
$openTasks = [];
$activeTasks = [];
$closedTasks = [];
foreach($groupTasks as $group){
    switch($group['status']){
        case 0:
            $openTasks[] = $group;
            break;
        case 1:
            $activeTasks[] = $group;
            break;
        case 2:
            $closedTasks[] = $group;
            break;
        default:
        die('Группа ' . print_r($group) . " имеет не верный статус");
    }
}

?>
<?php if($success): ?>
<div class="alert alert-success alert-dismissible fade show mx-3 mt-3" role="alert">
    <i class="bi bi-check-circle me-2"></i><?= htmlspecialchars($success) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<?php endif; ?>

<?php if($error): ?>
<div class="alert alert-danger alert-dismissible fade show mx-3 mt-3" role="alert">
    <i class="bi bi-exclamation-triangle me-2"></i><?= htmlspecialchars($error) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<?php endif; ?>

 <div class="container-fluid">
     <div class="row">
        <div class="col-12">
<div class="row g-0 justify-content-center align-items-center mb-4">
    <div class="col-auto">
        <div class="h1 text-color-black mb-0"><?=$currentGroup['title'] ?></div>
    </div>
    <?php if($_SESSION['id'] == $groupAdmin['user_id']){ ?>
    <div class="col-auto">
        <a href="#" class="btn btn-primary ms-3" data-bs-toggle="modal" data-bs-target="#addTask">
            <i class="bi bi-plus-lg"></i>
        </a>
    </div> 
    <div class="col-12 text-center"> 
        <h6 class="text-muted ">
          Код группы 
           <h5 class="text-decoration-underline">
              <?= $currentGroup['group_code'] ?>
              </h5>
              Код группы</div>
        </h6>
    <?php }?>
</div>
             <div class="card">
                <div class="card-body"> 
<div class="row">
    <!-- Левая колонка: Открытые задачи -->
    <div class="col-lg-4 col-md-6 col-12 mb-4 border-end">
        <h4 class="text-center mb-3 border-bottom">📋 Открытые задачи</h4>
        <div class="row row-cols-1 row-cols-md-3 g-2">
            <?php foreach($openTasks as $task): 
                $currentUser = User::getUserById($task['fk_user_id']);
                $userName = $currentUser['name'];
                $userEmail = $currentUser['mail'];

            ?>
            <div class="col mb-2">
                <a href="#" class="text-decoration-none" 
                data-bs-toggle="modal"
                 data-bs-target="#modalTask"
                 data-task-id="<?= $task['id'] ?>"
                 data-task-title="<?= htmlspecialchars($task['title']) ?>"
                data-task-description="<?= htmlspecialchars($task['description'] ?? '') ?>"
                data-task-user="<?= htmlspecialchars($userName) ?>"
                data-task-user-id="<?= htmlspecialchars($currentUser['id']) ?>"
                data-task-created="<?= $task['created_at'] ?>"
                 data-task-closed="<?= $task['closed_at'] ?? '' ?>"
                data-task-user-email="<?= htmlspecialchars($userEmail) ?>"
                 >

                    <div class="card border-info h-100 
                    hover-lift 
                    border-hover-info 
                    text-dark">
                        <div class="card-header border-info py-2 bg-info bg-opacity-10">
                            <div class="d-flex align-items-center">
                                <img src="<?= htmlspecialchars($currentUser['photo']) ?>" 
                                alt="user photo" 
                                class="rounded-circle border border-info border-2 me-2" 
                                style="width: 2rem; height: 2rem; object-fit: cover;">
                                <h6 class="text-info m-0 text-truncate">
                                    <?= htmlspecialchars($userName) ?>
                                </h6>
                            </div>
                        </div>
                        <div class="card-body py-2">
                            <p class="card-text small mb-0">
                                <?= htmlspecialchars($task['title']) ?>
                            </p>
                        </div>
                        <div class="card-footer border-info py-1 bg-info bg-opacity-10">
                            <small class="text-muted">
                                <!-- date('H:i', strtotime($task['created_at']))  -->
                                <?=htmlspecialchars($task['created_at']); ?>
                            </small>
                        </div>
                    </div>
                </a>
            </div>
            <?php endforeach; ?>
            
            <?php if(empty($task)): ?>
            <div class="col-12">
                <div class="text-center py-3 border rounded">
                    <i class="bi bi-inbox fs-4 text-muted"></i>
                    <p class="text-muted mt-2 small">Нет открытых задач</p>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Центральная колонка: Активные задачи -->
    <div class="col-lg-4 col-md-6 col-12 mb-4 border-end">
        <h4 class="text-center mb-3 border-bottom">🕑 Активные задачи</h4>
        <div class="row row-cols-1 row-cols-md-3 g-2">
            <?php foreach($activeTasks as $task): 
                $currentUser = User::getUserById($task['fk_user_id']);
                $userName = $currentUser['name'];
               $userEmail = $currentUser['mail'];
            ?>
            <div class="col mb-2">
                <a href="#" class="text-decoration-none" 
                data-bs-toggle="modal"
                 data-bs-target="#modalTask"
                 data-task-id="<?= $task['id'] ?>"
                 data-task-title="<?= htmlspecialchars($task['title']) ?>"
                data-task-description="<?= htmlspecialchars($task['description'] ?? '') ?>"
                data-task-user="<?= htmlspecialchars($userName) ?>"
                 data-task-closed="<?= $task['closed_at'] ?? '' ?>"
                data-task-user-id="<?= htmlspecialchars($currentUser['id']) ?>"
                data-task-created="<?= $task['created_at'] ?>"
                data-task-user-email="<?= htmlspecialchars($userEmail) ?>"
                 >
                    <div class="card border-warning h-100 
                    hover-lift 
                    border-hover-warning 
                    text-dark">
                        <div class="card-header border-warning py-2 bg-warning bg-opacity-10">
                            <div class="d-flex align-items-center">
                                <img src="<?= htmlspecialchars($currentUser['photo']) ?>" 
                                alt="user photo" 
                                class="rounded-circle border border-warning border-2 me-2" 
                                style="width: 2rem; height: 2rem; object-fit: cover;">
                                <h6 class="text-warning m-0 text-truncate">
                                    <?= htmlspecialchars($userName) ?>
                                </h6>
                            </div>
                        </div>
                        <div class="card-body py-2">
                            <p class="card-text small mb-0">
                                <?= htmlspecialchars($task['title']) ?>
                            </p>
                        </div>
                        <div class="card-footer border-warning py-1 bg-warning bg-opacity-10">
                            <small class="text-muted">
                                <!-- date('H:i', strtotime($task['created_at']))  -->
                                <?=htmlspecialchars($task['created_at']); ?>
                            </small>
                        </div>
                    </div>
                </a>
            </div>
            <?php endforeach; ?>
            
            <?php if(empty($task)): ?>
            <div class="col-12">
                <div class="text-center py-3 border rounded">
                    <i class="bi bi-inbox fs-4 text-muted"></i>
                    <p class="text-muted mt-2 small">Нет активных задач</p>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Правая колонка: Завершенные задачи -->
    <div class="col-lg-4 col-md-12 col-12">
        <h4 class="text-center mb-3 border-bottom">✅ Завершенные задачи</h4>
        <div class="row row-cols-1 row-cols-md-3 g-2">
            <?php foreach($closedTasks as $task): 
                $currentUser = User::getUserById($task['fk_user_id']);
                $userName = $currentUser['name'];
                $userEmail = $currentUser['mail'];
            ?>
            <div class="col mb-2">
                <a href="#" class="text-decoration-none" 
                data-bs-toggle="modal"
                 data-bs-target="#modalTask"
                 data-task-id="<?= $task['id'] ?>"
                 data-task-title="<?= htmlspecialchars($task['title']) ?>"
                data-task-description="<?= htmlspecialchars($task['description'] ?? '') ?>"
                data-task-user-id="<?= htmlspecialchars($currentUser['id']) ?>"
                data-task-user="<?= htmlspecialchars($userName) ?>"
                data-task-created="<?= $task['created_at'] ?>"
                data-task-closed="<?= $task['closed_at'] ?? '' ?>"
                data-task-user-email="<?= htmlspecialchars($userEmail) ?>"
                 >
                    <div class="card border-success h-100 
                    hover-lift 
                    border-hover-success 
                    text-dark">
                        <div class="card-header border-success py-2 bg-success bg-opacity-10">
                            <div class="d-flex align-items-center">
                                <img src="<?= htmlspecialchars($currentUser['photo']) ?>" 
                                alt="user photo" 
                                class="rounded-circle border border-success border-2 me-2" 
                                style="width: 2rem; height: 2rem; object-fit: cover;">
                                <h6 class="text-success m-0 text-truncate">
                                    <?= htmlspecialchars($userName) ?>
                                </h6>
                            </div>
                        </div>
                        <div class="card-body py-2">
                            <p class="card-text small mb-0">
                                <?= htmlspecialchars($task['title']) ?>
                            </p>
                        </div>
                        <div class="card-footer border-success py-1 bg-success bg-opacity-10">
                            <small class="text-muted">
                                <!-- date('H:i', strtotime($task['created_at']))  -->
                                <?=htmlspecialchars($task['created_at']); ?> - открыта
                            </small>
                        </div>
                        <div class="card-footer border-success py-1 bg-success bg-opacity-10">

                            <small class="text-muted">
                                <?=htmlspecialchars($task['closed_at']); ?> - закрыта
                            </small>
                        </div>
                    </div>
                </a>
            </div>
            <?php endforeach; ?>
            
            <?php if(empty($task)): ?>
            <div class="col-12">
                <div class="text-center py-3 border rounded">
                    <i class="bi bi-inbox fs-4 text-muted"></i>
                    <p class="text-muted mt-2 small">Нет завершенных задач</p>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
                </div>
             </div>
        </div>
     </div>
 </div>
 <style>
.hover-lift:hover {
    transform: translateY(-3px);
    transition: transform 0.2s ease;
}

/* Подсветка границы */
.border-hover-info:hover {
    border-color: #0dcaf0!important;
    border-width: 2px;
}
.border-hover-warning:hover {
    border-color: #ffc107!important;
    border-width: 2px;
}
.border-hover-success:hover {
    border-color: #198754!important;
    border-width: 2px;
}

/* Курсор для всей карточки */
.card[style*="cursor"] {
    cursor: pointer;
}

/* Для всех карточек внутри ссылок */
a .card {
    color: inherit; /* Наследует цвет текста */
}

a:hover .card {
    text-decoration: none;
}
 </style>

<?php  include("src/views/src/includes/modaltask.php"); ?>
<?php include("src/views/src/includes/modalAddTask.php"); ?>
<?php include("../CloudFlowPHP/src/views/src/includes/footer.php"); ?>