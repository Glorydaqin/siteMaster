<?php

require 'vendor/autoload.php';


use CAPTCHAReader\src\App\IndexController;

$start_time = microtime(true);//运行时间开始计时

$indexController = new IndexController();
$res = $indexController->entrance('https://zh.majestic.com/account/login/captcha','online');
dump($res);

$end_time = microtime(true);//计时停止
echo '执行时间为：' . ($end_time - $start_time) . ' s' . "<br/>\n";
