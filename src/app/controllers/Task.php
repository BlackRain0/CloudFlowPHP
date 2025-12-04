<?php
namespace app\controllers;
use app\utils\Connect;
use mysqli;

class Task {
   public static function createTask($data){
    $conn = Connect::connect();
    $title = $data['title'];
    $description = $data['description'] ?? '';
    $userId = $data['userId'] ?? null;
    $groupId = $data['groupId'];
    $status = $data['status'] ?? 0;
    $createAt = date('Y-m-d H:i:s');
    
    // Если задача создается завершенной, сразу ставим дату закрытия
    $closedAt = ($status == 2) ? date('Y-m-d H:i:s') : null;

    $queryCreate = "INSERT INTO `tasks`(`id`, `title`, `description`,
     `fk_user_id`, `created_at`, `closed_at`, `status`, `fk_group_id`)
     VALUES (null, ?, ?, ?, ?, ?, ?, ?)";
     
    $stmt = mysqli_prepare($conn, $queryCreate);
    mysqli_stmt_bind_param($stmt, "ssisssii", $title, $description, $userId, $createAt, $closedAt, $status, $groupId);
    
    if(mysqli_stmt_execute($stmt)){
        header("Location: /group?id=" . $groupId . "&success=Задача создана");
        exit;
    } else {
        header("Location: /group?id=" . $groupId . "&error=Ошибка создания: " . mysqli_error($conn));
        exit;
    }   
}

    public static function getTaskByGroup($groupId){
        $conn = Connect::connect();
        $queryGet = "SELECT * FROM `tasks` WHERE `fk_group_id` = ? ORDER BY `created_at` DESC";
        $stmt = mysqli_prepare($conn, $queryGet);
        mysqli_stmt_bind_param($stmt, "i", $groupId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        $tasks = [];
        while($row = mysqli_fetch_assoc($result)){
            $tasks[] = $row;
        };
        return $tasks;
    }
    public static function getTasksByUser($userId){
        $conn = Connect::connect();
        $queryGet = "SELECT * FROM `tasks` WHERE `fk_user_id` = ?";
        $stmt = mysqli_prepare($conn, $queryGet);
        mysqli_stmt_bind_param($stmt, "i", $userId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        $tasks = [];
        while($row = mysqli_fetch_assoc($result)){
            $tasks[] = $row;
        };
        return $tasks;
    }
    
    public static function getTaskById($id){
        $conn = Connect::connect();
        $query = "SELECT * FROM `tasks` WHERE `id` = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        return mysqli_fetch_assoc($result);
    }

public static function updateTask($data){
    $conn = Connect::connect();
    $taskId = $data['id'];
    $title = $data['title'] ?? null;
    $description = $data['description'] ?? null;
    $userId = isset($data['user_id']) && $data['user_id'] !== '' ? $data['user_id'] : null;
    $status = isset($data['status']) ? (int)$data['status'] : null;
    $groupId = $data['groupId'];
    $skipConfirmed = $data['skip_confirmed'] ?? null;
    
    // Получаем текущую задачу для проверки
    $currentTask = self::getTaskById($taskId);
    if(!$currentTask) {
        header("Location: /group?id=" . $groupId . "&error=Задача не найдена");
        exit;
    }
    
    $currentUserId = $_SESSION['id'] ?? null;
    
    // Проверка прав для изменения статуса (если пользователь не админ)
    if($status !== null && $title === null && $description === null && $userId === null) {
        // Это попытка изменить только статус
        $isAssignedUser = ($currentTask['fk_user_id'] == $currentUserId);
        
        // Проверяем, является ли пользователь админом группы
        $isAdmin = false;
        if($currentUserId) {
            $adminCheckQuery = "SELECT COUNT(*) as count FROM `user_group` 
                               WHERE `user_id` = ? AND `group_id` = ? AND `user_role` = 2";
            $stmt = mysqli_prepare($conn, $adminCheckQuery);
            mysqli_stmt_bind_param($stmt, "ii", $currentUserId, $groupId);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $adminRow = mysqli_fetch_assoc($result);
            $isAdmin = ($adminRow['count'] > 0);
        }
        
        if(!$isAdmin && !$isAssignedUser) {
            header("Location: /group?id=" . $groupId . "&error=Нет прав для изменения статуса");
            exit;
        }
        
        // Проверка на пропуск статуса (открытая → завершенная)
        if($currentTask['status'] == 0 && $status == 2) {
            // Если нет подтверждения пропуска, просим подтвердить
            if(!$skipConfirmed) {
                header("Location: /group?id=" . $groupId . "&confirm_skip=" . $taskId);
                exit;
            }
        }
    }
    
    // Формируем запрос для обновления
    $updates = [];
    $params = [];
    $types = '';
    
    if($title !== null) {
        $updates[] = "`title` = ?";
        $params[] = $title;
        $types .= 's';
    }
    
    if($description !== null) {
        $updates[] = "`description` = ?";
        $params[] = $description;
        $types .= 's';
    }
    
    if($userId !== null) {
        $updates[] = "`fk_user_id` = ?";
        $params[] = $userId;
        $types .= 'i';
    }
    
    if($status !== null) {
        $updates[] = "`status` = ?";
        $params[] = $status;
        $types .= 'i';
        
        // Если статус меняется на "завершенный", устанавливаем closed_at
        // Также проверяем, если задача уже была завершена, не обновляем дату
        if($status == 2 && $currentTask['status'] != 2) {
            $updates[] = "`closed_at` = ?";
            $params[] = date('Y-m-d H:i:s');
            $types .= 's';
        }
        // Если статус меняется с "завершенный" на другой, очищаем closed_at
        else if($status != 2 && $currentTask['status'] == 2) {
            $updates[] = "`closed_at` = NULL";
        }
    }
    
    if(empty($updates)) {
        header("Location: /group?id=" . $groupId . "&error=Нет данных для обновления");
        exit;
    }
    
    $query = "UPDATE `tasks` SET " . implode(', ', $updates) . " WHERE `id` = ?";
    $params[] = $taskId;
    $types .= 'i';
    
    $stmt = mysqli_prepare($conn, $query);
    
    if (!$stmt) {
        header("Location: /group?id=" . $groupId . "&error=Ошибка подготовки запроса: " . mysqli_error($conn));
        exit;
    }
    
    mysqli_stmt_bind_param($stmt, $types, ...$params);
    
    if(mysqli_stmt_execute($stmt)){
        $message = 'Задача обновлена';
        if($status == 1) {
            $message = 'Задача переведена в работу';
        } else if($status == 2) {
            $message = 'Задача завершена';
        }
        header("Location: /group?id=" . $groupId . "&success=" . urlencode($message));
        exit;
    } else {
        header("Location: /group?id=" . $groupId . "&error=Ошибка обновления: " . mysqli_error($conn));
        exit;
    }
}

    public static function deleteTask($data){
        $conn = Connect::connect();
        $id = $data['id'];
        $groupId = $data['groupId'];
        
        $query = "DELETE FROM `tasks` WHERE `id` = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "i", $id);
        
        if(mysqli_stmt_execute($stmt)){
            header("Location: /group?id=" . $groupId);
            exit;
        } else {
            header("Location: /group?id=" . $groupId);
            exit;
        }
    }
}
?>