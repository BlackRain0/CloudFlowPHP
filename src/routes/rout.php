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

Router::postUrl('/user/auth', Auth::class, 'authUser', true, false);
Router::postUrl('/user/register', Auth::class, 'registrationUser', true, true);
Router::postUrl('/user/logout', Auth::class, 'logout', false, false);

Router::postUrl('/group/add', Group::class,'createGroup', true, false);
Router::postUrl('/group/get', Group::class,'getGroupById', true, false);
Router::postUrl('/group/redact', Group::class,'redactGroupName', true, false);
Router::postUrl('/group/delete', Group::class,'deleteGroup', true, false);
Router::postUrl('/group/user/get', User::class,'getUserByGroup', true, false);
Router::postUrl('/group/user/add', Group::class,'addUserToGroup', true, false);
Router::postUrl('/group/user/redact', Group::class,'redactUserRole', true, false);
Router::postUrl('/group/user/delete', Group::class,'deleteUserFromGroup', true, false);


Router::postUrl('/group/task/add', Task::class, 'createTask', true, false);
Router::postUrl('/group/task/get', Task::class, 'getTaskByGroup', true, false);
Router::postUrl('/group/task/get/id', Task::class, 'getTaskById', true, false);
Router::postUrl('/group/task/redact', Task::class, 'redactTask', true, false);
Router::postUrl('/group/task/delete', Task::class, 'deleteTask', true, false);

Router::postUrl('/user/redact', User::class, 'redactUser', true, true);
Router::postUrl('/user/get', User::class, 'getUserById', true, false);
Router::postUrl('/user/delete', User::class, 'deleteUser', true, false);



?>