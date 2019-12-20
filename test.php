<?php


ini_set('date.timezone', 'PRC');
define('IN_DS', true);
define('INCLUDE_ROOT', dirname(__FILE__) . DIRECTORY_SEPARATOR . "front" . DIRECTORY_SEPARATOR);
require 'vendor/autoload.php';
include_once INCLUDE_ROOT . 'etc/init.php';



$user_name = '692860800@qq.com';
$user_pass = 'daqing';
$majestic = new Majestic($user_name,$user_pass);
$majestic->login();


//获取验证码


