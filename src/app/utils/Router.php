<?php
namespace app\utils;

class Router{
    
    public static $pagesList = [];

    public static function getUrl($url, $namePage){
        self::$pageList[] = [
            "url" => $url,
            "namePage" => $namePage,
        ];
    }

    public static function postUrl($url, $controller, $method, $data=null, $file=null){
        self::$pagesList[] =[
            "url" => $url,
            "controller" => $controller,
            "method" => $method,
            "data" => $data,
            "file" => $file,
        ]
    }


}

?>