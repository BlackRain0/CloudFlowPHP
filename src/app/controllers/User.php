<?php

namespace app\controllers;
use app\utils\Router;
use app\utils\Connect;
use mysqli;
use mysqli_stmt;

class User{
    public static function redactUser($data, $formFile){
    $id = $data['id'];
    $name = $data['name'];
    $mail = $data['mail'];
    $photo = $formFile['photo'];

    // Получаем текущего пользователя для сохранения старого фото, если новое не загружено
    $currentUser = self::getUserById($id);
    
    if($photo['error'] == 0 && $photo['size'] > 0) {
        // Загружаем новое фото
        $fileName = time(). '_' .$photo['name'];
        $path = "uploads/photo/" . $fileName;
        
        if(!move_uploaded_file($photo['tmp_name'], $path)) {
            header("Location: /user?error=Ошибка загрузки фото");
            exit;
        }
        
        $photoPath = $fileName;
    } else {
        // Оставляем старое фото
        $photoPath = $currentUser['photo'];
    }

    $conn = Connect::connect();
    $queryRedact = "UPDATE `users` SET `name` = ?, `mail` = ?, `photo` = ? WHERE `id` = ?";
    $stmt = mysqli_prepare($conn, $queryRedact);
    mysqli_stmt_bind_param($stmt, "sssi", $name, $mail, $photoPath, $id);
    
    if(mysqli_stmt_execute($stmt)){
        // Обновляем данные в сессии
        $_SESSION['name'] = $name;
        $_SESSION['email'] = $mail;
        
        header("Location: /user?success=Профиль успешно обновлен");
        exit;
    } else {
        header("Location: /user?error=Ошибка обновления профиля");
        exit;
    }
}

   public static function getUserById($id){
    $queryGet = "SELECT * FROM `users` WHERE `id`='$id'";
    $get = mysqli_query(Connect::connect(), $queryGet);
     if(!$get){
            die("error: getting failed");
        }else{
            return mysqli_fetch_assoc($get);
        }
   }
      public static function getUserByEmail($data){
    $conn = Connect::connect();
    $queryGet = "SELECT * FROM `users` u WHERE u.mail = ?";
    $stmt = mysqli_prepare($conn, $queryGet);
    mysqli_stmt_bind_param($stmt, "s", $data);
        if(!mysqli_stmt_execute($stmt)){
        error_log("Execute failed: " . mysqli_stmt_error($stmt));
        mysqli_stmt_close($stmt);
        return [];
    }
    $result = mysqli_stmt_get_result($stmt);
     if(!$result){
            error_log("error: getting failed, query: " . $queryGet);
            return null;
        }
        mysqli_stmt_close($stmt);
          return mysqli_fetch_assoc($result);
        
   }
   
   public static function getUserByGroup($id){
    $conn = Connect::connect();
    $queryGet = "SELECT u.* FROM `users` u 
    INNER JOIN user_group ug ON u.id = ug.user_id
    WHERE ug.group_id = ?";
    
    $stmt = mysqli_prepare($conn, $queryGet);
    mysqli_stmt_bind_param($stmt,'i',$id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

     if(!$result){
            error_log("error: getting failed");
        }
        $users = [];
        while($row = mysqli_fetch_assoc($result)){
            $users [] = $row;
        }
            mysqli_stmt_close($stmt);
            return $users;
        
   }

   public static function addGroupToUser($data){
    $conn = Connect::connect();
    $code = $data['groupCode'];
    $quearyGroup = "SELECT g.id FROM `groups` g WHERE g.group_code = '$code'";
    $groupGet = mysqli_query($conn,$quearyGroup);
    $groupIdArray = mysqli_fetch_assoc($groupGet);
    $groupId = $groupIdArray['id'];
    $currentUser = User::getUserByEmail($_SESSION['email']);
    $userId = $currentUser['id'];

    $checkQuery = "SELECT id FROM `user_group` WHERE `user_id` = ? AND `group_id` = ? ";
    $checkStmt = mysqli_prepare($conn, $checkQuery);
    mysqli_stmt_bind_param($checkStmt, 'ii', $userId, $groupId);
    mysqli_stmt_execute($checkStmt);
    $checkResult = mysqli_stmt_get_result($checkStmt);
    if(mysqli_num_rows($checkResult) > 0 ){
        mysqli_stmt_close($checkStmt);
        die('Вы уже состоите в данной группе');
    }else{
        mysqli_stmt_close($checkStmt);
   
   
       $quearyAdd = "INSERT INTO `user_group`(`id`, `user_id`, `group_id`, `user_role`)
        VALUES (null, ? , ? , 1)";
        $stmt = mysqli_prepare($conn,$quearyAdd);
        if(!$stmt){
           die("Ошибка подготовки запроса");
        }
        mysqli_stmt_bind_param($stmt, "ii", $userId, $groupId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_affected_rows($stmt);
        mysqli_stmt_close($stmt);
        if($result >0){
           header("Location: /");
           exit;
        }else{
           die("Error: " . "Группа не найдена" );
        }
    }

   }


    public static function deleteUser($id){
        $id = $id['id'];
       $queryDelete = "DELETE FROM `users` WHERE `id`=$id";
        $delete = mysqli_query(Connect::connect(), $queryDelete);
        if(!$delete){
            die("error: delete failed");
        }else{
            header("Location: ../");
        }
    }

}