<?php
namespace app\utils;

class Router{
    
    public static $pageList = [];

    public static function getUrl($url, $namePage){
        self::$pageList[] = [
            "url" => $url,
            "namePage" => $namePage,
        ];
    }

    public static function postUrl($url, $controller, $method, $data=null, $file=null){
        self::$pagesList[] =[
            "url" => $url,
            "class" => $controller,
            "method" => $method,
            "data" => $data,
            "file" => $file,
            "post" => true,
        ];
    }

    public static function getContent(){
        $get = $_GET['q'] ?? '';
        foreach(self::$pageList as $val){
            if($val['url'] === '/'.$get){
                if($_SERVER["REQUEST_METHOD"] === 'POST'){
                    $action = new $val['class'];
                    $method = $val['method'];
                    switch($method){
                        case 'createGroup':
                            $action -> $method($_POST);
                            break;
                        case 'getGroupById':
                            $action -> $method($_POST);
                            break;
                        case "redactGroupName":
                            $action -> $method($_POST);
                            break;
                        case "deleteGroup":
                            $action -> $method($_POST);
                            break;
                        case "addUserToGroup":
                            $action -> $method($_POST);
                            break;
                        case "redactUserRole":
                            $action -> $method($_POST);
                            break;
                        case "deleteUserFromGroup":
                            $action -> $method($_POST);
                            break;
                        case 'createTask':
                            $action -> $method($_POST);
                            break;
                        case 'getTaskByGroup':
                            $action -> $method($_POST);
                            break;
                        case "redactTask":
                            $action -> $method($_POST);
                            break;
                        case "deleteTask":
                            $action -> $method($_POST);
                            break;
                        case "getUserById":
                            $action -> $method($_POST, $_FILES);
                            break;
                        case "getUserByGroup":
                            $action -> $method($_POST, $_FILES);
                            break;
                        case "registrationUser":
                            $action -> $method($_POST, $_FILES);
                            break;
                        case "redactUser":
                            $action -> $method($_POST, $_FILES);
                            break;
                        case "authUser":
                            $action -> $method($_POST);
                            break;
                        case "logout":
                            $action -> $method();
                            break;
                    }
                }else{
                    require_once "views/pages/" . $val["namePage"] . '.php';
                    die();
                }
            }
        }
        self::errors('404');
    }

    public static function errors($err){
        require_once "views/errors/" . $err . '.php';
        die();
    }


}

?>