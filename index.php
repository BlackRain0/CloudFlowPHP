<?php
session_start();
require_once "src/app/utils/connect.php";
require_once "src/app/utils/Router.php";
require_once "src/app/controllers/Auth.php";
require_once "src/app/controllers/Group.php";
require_once "src/app/controllers/Task.php";
require_once "src/app/controllers/User.php";
require_once "src/routes/rout.php";
require_once "src/views/home.php";
?>