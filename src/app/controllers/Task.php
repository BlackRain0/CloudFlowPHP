<?php

namespace app\controllers;
use app\utils\Connect;

class Task{
    public static function createTask($data){
        $title = $data['title'];
        $description = $data['description'];
        $userId = $data['userId'];
        $groupId = $data['groupId'];
        $createAt = $data['createAt'];
        $closedAt = $data['closedAt'];
        $status = $data['status'];

        $queryCreate = "INSERT INTO `tasks`(`id`, `title`, `description`,
         `fk_user_id`, `created_at`, `closed_at`, `status`, `fk_group_id`)
         VALUES (null,'$title','$description','$userId','$createAt','$closedAt','$status','$groupId')";
         $create = mysqli_query(Connect::connect(), $queryCreate);
        if(!$create){
            die("error: create failed");
        }else{
            header("Location: ../");
        }   
    }

 public static function getTaskByGroup($id){
    $id = $id['id'];
    $queryGet = "SELECT * FROM `tasks` WHERE `fk_group_id`='$id'";
    $get = mysqli_query(Connect::connect(), $queryGet);
     if(!$get){
            die("error: getting failed");
        }else{
            header("Location: ../");
        }
    }
     public static function getTaskById($id){
    $id = $id['id'];
    $queryGet = "SELECT * FROM `tasks` WHERE `id`='$id'";
    $get = mysqli_query(Connect::connect(), $queryGet);
     if(!$get){
            die("error: getting failed");
        }else{
            header("Location: ../");
        }
    }


    public static function redactTask($data){
        $taskId = $data['taskId'];
        $title = $data['title'];
        $description = $data['description'];
        $userId = $data['userId'];
        $groupId = $data['groupId'];
        $createAt = $data['createAt'];
        $closedAt = $data['closedAt'];
        $status = $data['status'];

        $queryRedact = "UPDATE `tasks` SET `title`='$title',
        `description`='$description',`fk_user_id`='$userId',`created_at`='$createAt',
        `closed_at`='$closedAt',`status`='$status',`fk_group_id`='$groupId' WHERE `id` = '$taskId'";
        $redact = mysqli_query(Connect::connect(), $queryRedact);
       if(!$redact){
            die("error: redact failed");
        }else{
            header("Location: ../");
        }   
    }

    public static function deleteTask($id){
        $id = $id['id'];
       $queryDelete = "DELETE FROM `tasks` WHERE `id`=$id";
        $delete = mysqli_query(Connect::connect(), $queryDelete);
        if(!$delete){
            die("error: delete failed");
        }else{
            header("Location: ../");
        }
    }

}