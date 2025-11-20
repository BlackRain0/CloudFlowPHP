<?php

namespace app\controllers;
use app\util\Router;
use app\utils\Connect;

class User{
    public static function redactUser($data, $formFile){
        $id = $data['id'];
        $name = $data['name'];
        $mail = $data['mail'];
        $photo = $formFile['photo'];

        $fileName = time(). '_' .$photo[name];
        $path = "uploads/photo/" . $fileName;
        if(move_uploaded_file($photo['tmp_name'], $path)){

            $queryRedact = "UPDATE `users` SET
            `name`='$name',`mail`='$mail',
            `photo`='$photo' WHERE `id`= $id";
            $redact = mysqli_query(App::connect, $queryRedact);
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
    $get = mysqli_query(App::connect, $queryGet);
     if(!$get){
            die("error: getting failed");
        }else{
            header("Location: ../");
        }
   }
   
   public static function getUserByGroup($id){
    $id = $id['id'];
    $queryGet = "SELECT * FROM `user_group` WHERE `group_id`='$id'";
    $get = mysqli_query(App::connect, $queryGet);
     if(!$get){
            die("error: getting failed");
        }else{
            header("Location: ../");
        }
   }


    public static function deleteUser($id){
        $id = $id['id'];
       $queryDelete = "DELETE FROM `users` WHERE `id`=$id";
        $delete = mysqli_query(App::connect(), $queryDelete);
        if(!$delete){
            die("error: delete failed");
        }else{
            header("Location: ../");
        }
    }

}