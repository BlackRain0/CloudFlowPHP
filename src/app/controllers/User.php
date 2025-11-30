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

        $fileName = time(). '_' .$photo['name'];
        $path = "uploads/photo/" . $fileName;
        if(move_uploaded_file($photo['tmp_name'], $path)){

            $queryRedact = "UPDATE `users` SET
            `name`='$name',`mail`='$mail',
            `photo`='$photo' WHERE `id`= $id";
            $redact = mysqli_query(Connect::connect(), $queryRedact);
           if(!$redact){
                die("error: redact failed");
            }else{
                header("Location: ../");
            }   
        }else{
            Router::errors('500');
        }
    }

   public static function getUserById($id){
    $id = $id['id'];
    $queryGet = "SELECT * FROM `users` WHERE `id`='$id'";
    $get = mysqli_query(Connect::connect(), $queryGet);
     if(!$get){
            die("error: getting failed");
        }else{
            header("Location: ../");
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
    $id = $id['id'];
    $queryGet = "SELECT u.* FROM `users` u 
    INNER JOIN user_group ug ON u.id = ug.user_id
    WHERE ug.group_id = ?";
    
    $stmt = mysqli_prepare(Connect::connect(), $queryGet);
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
            return $users;
        
   }

   public static function addGroupToUser($data){
    $code = $data['group_code'];
    $quearyGroup = "SELECT g.id FROM `groups` g WHERE g.group_code = '$code'";
    $groupGet = mysqli_query(Connect::connect(),$quearyGroup);
    $groupId = mysqli_fetch_assoc($groupGet);
    $currentUser = User::getUserByEmail($_SESSION['email']);
    $userId = $currentUser['id'];
    $quearyAdd = "INSERT INTO `user_group`(`id`, `user_id`, `group_id`, `user_role`)
     VALUES (null,'$userId','$groupId', 2)";
     $add = mysqli_query(Connect::connect(),$quearyAdd);
     if(!$add){
        die("Error: adding faied");
     }else{
        header("Location: ../");
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