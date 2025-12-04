<?php
namespace app\controllers;
use app\utils\Router;
use app\utils\Connect;
use mysqli;
use mysqli_stmt;

class Auth{
    public static function registrationUser($formData, $formFile){
        $email = $formData['email'];
        $name = $formData['name'];
        $pass = $formData['pass'];
        $confirmPass = $formData['confirmPass'];
        $photo = $formFile['photo'];
        if(strlen($pass) < 6){
            header("Location: /auth?error=Пароль слишком короткий");
            exit;  
        }
        if(count(array_count_values(str_split($pass))) == 1) {
         header("Location: /auth?error=Пароль слишком простой");
            exit;  
        }

        if($pass !== $confirmPass){
            header("Location: /auth?error=Пароли не совпадают");
            exit;
        }
        if(User::getUserByEmail($email)){
             header("Location: /auth?error=Пользователь уже существует");
            return;
        }else{

            $fileName = time(). '_' . $photo['name'];
            $path = "src/uploads/users/" . $fileName;
    
            if(move_uploaded_file($photo["tmp_name"], $path)){
                $pass = password_hash($pass, PASSWORD_DEFAULT);
    
                $queryReg = "INSERT INTO `users`(`id`, `name`, `mail`, `password`, `photo`)
                 VALUES (null,'$name','$email','$pass', '$path')";
               $reg = mysqli_query(Connect::connect(), $queryReg);
    
                header("Location: /?success=Регистрация успешна");
                exit;
            }else{
                header("Location: /auth?error=Ошибка загрузки фото");
                exit;
            }
        }

    }

    public static function authUser($data){
        $email = $data["email"];
        $pass = $data["pass"];
        $conn = Connect::connect();

        $query = "SELECT * FROM `users` WHERE `mail` = ? ";
        $stmt = mysqli_prepare($conn,$query);
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result  = mysqli_stmt_get_result($stmt);
        mysqli_stmt_close($stmt);
        $user = mysqli_fetch_assoc($result);
        if($user){
            if(password_verify($pass, $user['password'])){
                $_SESSION['email'] = $user['mail'];
                $_SESSION['id'] = $user['id'];
                header("Location: ../");
                exit;
            }else{
                 header("Location: /login?error=Неверная почта или пароль");
                return;
            }
        }else{
        header("Location: /login?error=Неверная почта или пароль");
            exit;
        }
    }

    public static function logout(){

        unset($_SESSION["email"]);
        header("Location: ../");
        exit;
    }
    

}

?>