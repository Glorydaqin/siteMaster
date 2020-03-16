<?php
if (!defined('IN_DS')) {
    die('Hacking attempt');
}

include_once(INCLUDE_ROOT . 'etc/common.func.php');
include_once(INCLUDE_ROOT . 'etc/config.php');
include_once(INCLUDE_ROOT . 'etc/const.php');

//注册异常提示组件 whoops
if (DEBUG_MODE) {
    error_reporting(E_ALL);
    $whoops = new \Whoops\Run;
    $whoops->appendHandler(new \Whoops\Handler\PrettyPageHandler);
    $whoops->register();
} else {
    error_reporting(0);
}

//非主域名不主动启用数据库
if (!is_cli() && $server_domain == DOMAIN) {
    $db = new Mysql(DB_NAME, DB_HOST, DB_USER, DB_PASS, DB_PORT);
    $tpl = new Template();
}
//session 子域名共享
ini_set("session.cookie_domain", '.' . DOMAIN);
session_start();
