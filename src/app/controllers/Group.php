<?php
namespace app\controllers;
use app\utils\Connect;

class Group{
    public static function createGroup($data){
        $title = $data['groupTitle'];
        $queryAdd = "INSERT INTO `groups`(`id`, `title`) VALUES (null,'$title')";
        $add = mysqli_query(App::connect(), $queryAdd);
        if(!$add){
            die("error: added failed");
        }else{
            header("Location: ../");
        }
    }
       public static function getGroupById($id){
    $id = $id['id'];
    $queryGet = "SELECT * FROM `groups` WHERE `id`='$id'";
    $get = mysqli_query(App::connect, $queryGet);
     if(!$get){
            die("error: getting failed");
        }else{
            header("Location: ../");
        }
   }

    public static function redactGroupName($data){
        $id = $data['groupId'];
        $title =  $data['groupTitle'];
        $qeuryRedact = "UPDATE `groups` SET `title`='$title' WHERE `id`='$id'";
        $redact = mysqli_query(App::connect(), $qeuryRedact);
        if(!$redact){
            die("error: redact failed");
        }else{
            header("Location: ../");
        }
    }

    public static function deleteGroup($id){
        $id = $id['id'];
        $queryDelete = "DELETE FROM `groups` WHERE `id`=$id";
        $delete = mysqli_query(App::connect(), $queryDelete);
        if(!$delete){
            die("error: delete failed");
        }else{
            header("Location: ../");
        }
    }

    public static function addUserToGroup($data){
        $userId = $data['userId'];
        $groupId = $data['groupId'];
        $userRole = $data['userRole'];

        $queryAdd = "INSERT INTO `user_group`(`id`, `user_id`, `group_id`, `user_role`) 
        VALUES (null,'$userId','$groupId','$userRole')";
        $add = mysqli_query(App::connect(), $queryAdd);
         if(!$add){
            die("error: added failed");
        }else{
            header("Location: ../");
        }
    }

    public static function redactUserRole($data){
        $userId = $data['userId'];
        $groupId = $data['groupId'];
        $newRole = $data['role'];
        $queryRedact = "UPDATE `user_group` 
        SET ``user_role`='$newRole' WHERE `user_id` ='$userId' AND `group_id`='$groupId'";
        $redact = mysqli_query(App::connect(), $qeuryRedact);
        if(!$redact){
            die("error: redact failed");
        }else{
            header("Location: ../");
        }
    }

    public static function deleteUserFromGroup($data){
        $userId = $data['userId'];
        $groupId = $data['groupId'];
        $queryDelete = "DELETE FROM `user_group` WHERE `user_id` ='$userId' AND `group_id`='$groupId'";
        $delete = mysqli_query(App::connect(), $queryDelete);
        if(!$delete){
            die("error: delete failed");
        }else{
            header("Location: ../");
        }
    }


}

?>