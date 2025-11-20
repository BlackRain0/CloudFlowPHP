<?php
use app\utils\Router;
use app\controllers\Auth;
use app\controllers\Group;
use app\controllers\Task;
use app\controllers\User;

Router::getUrl('/', 'home');
Router::getUrl('/login', 'login');
Router::getUrl('/auth', 'auth');
Router::getUrl('/users', 'users');
Router::getUrl('/groups', 'groups');




?>