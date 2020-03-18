<?php
ini_set('date.timezone', 'PRC');
define('IN_DS', true);
define('ROOT_PATH', dirname(dirname(__FILE__)));
define('INCLUDE_ROOT', ROOT_PATH . DIRECTORY_SEPARATOR . "front" . DIRECTORY_SEPARATOR);
$server_domain = '';
require ROOT_PATH . '/vendor/autoload.php';
include_once INCLUDE_ROOT . 'etc/init.php';

//将老版本数据库用户数据生成sql用于导入新版本
$all_user_sql = "select * from user";
$old_users = $GLOBALS['db']->getRows($all_user_sql);


foreach ($old_users as $user) {

    //1	admin	admin123	2020-02-29 17:07:01	[{"site_id":1,"site_name":"ahrefs","expired_at":"2020-01-16","search_limit":"10"},{"site_id":2,"site_name":"kwfinder","expired_at":"2020-01-16","search_limit":"10"}]	dgjihrgr0f9dv8nbvppnnkk1hp	2019-12-02 11:46:48	2020-01-10 23:23:04

    //  `username` varchar(255) DEFAULT NULL,
    //  `password` varchar(255) DEFAULT NULL,
    //  `expired_at` datetime DEFAULT '0000-00-00 00:00:00',
    //  `expired_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
    //
    $expired_at = date("Y-m-d H:i:s", strtotime("+5 day"));
    $json = [
        ['site_id' => 1, 'site_name' => 'ahrefs', 'expired_at' => $expired_at]
    ];
    $expired_json = json_encode($json);
    $sql = "insert into user(`username`,`password`,`expired_at`,`expired_json`) value('{$user['username']}','{$user['password']}','{$expired_at}','{$expired_json}');";
    if ($user['deleted'] == 0) {
        echo $sql . PHP_EOL;
    }
}
