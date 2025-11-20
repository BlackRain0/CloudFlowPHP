<?php
namespace app\controllers;
use app\util\Router;
use app\util\Connect;


class Auth{
    public static function registrationUser($formData, $formFile){
        $email = $formData['email'];
        $name = $formData['name'];
        $pass = $formData['pass'];
        $confirmPass = $formData['confirmPass'];
        $photo = $formFile['photo'];

        if($pass !== $confirmPass){
            Router::errors('500');
        }

        $fileName = time(). '_' . $photo['name'];
        $path = "uploads/photo/" . $fileName;

        if(move_uploaded_file($photo["tmp_name"], $path)){
            $pass = password_hash($pass, PASSWORD_DEFAULT);

            $queryReg = "INSERT INTO `users`(`id`, `name`, `mail`, `password`, `photo`)
             VALUES (null,'$name','$email','$pass', '$photo')";
             $reg = mysqli_query(Connect::connect(), $queryReg);

             header("Location: ../");
        }else{
            Router::errors('500');
        }

    }

    public static function authUser($data){
        $email = $data["email"];
        $pass = $data["pass"];

        $queryAllUsers = "SELECT * FROM `users`";
        $allUsers = mysqli_query(Connect::connect(), $queryAllUsers);
        $allUsers = mysqli_fetch_all($allUsers);

        foreach($allUsers as $user){
            if($user[2] == $email){
                if(password_verify($pass, $user[3])){
                    session_start();
                    $_SESSION["email"] = $user[2];
                    header("Location: ../");
                    break;
                }
            }else{
                echo "User not found";
                break;
            }
        }
    }

    public static function logout(){

        unset($_SESSION["email"]);
        header('Location: ../');
    }
    

}

?>