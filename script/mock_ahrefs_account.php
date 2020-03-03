<?php
ini_set('date.timezone', 'PRC');
define('IN_DS', true);
define('INCLUDE_ROOT', dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . "front" . DIRECTORY_SEPARATOR);
require '../vendor/autoload.php';
include_once INCLUDE_ROOT . 'etc/init.php';

//从竞争对手插件里拿账号cookie
//随机等待时间
//$sleep_time = rand(15, 300);
//sleep($sleep_time);

//
$url = "https://www.xixuanseo.com/s/login.php";
//type: ahrefs54g6a2sd1PQLG6xy1
//name: 2506306536@qq.com
//pass: 5888
//ip: 183.67.59.156
//addrs: 重庆市

//dd($url);
$data = [
    "type" => 'ahrefs54g6a2sd1PQLG6xy1',
    'name' => '2506306536@qq.com',
    'pass' => '5888',
    'ip' => '183.67.58.156',
    'addrs' => '重庆市'
];
$response = curl($url, $data);
if ($response['code'] == 200) {
    $site_sql = "select * from site where name='ahrefs'";
    $site = $GLOBALS['db']->getFirstRow($site_sql);

    $json = json_decode($response['body'], true);
    $cookies = $json['cookies'] ?? [];

    foreach ($cookies as $cookie) {
        //如果不存在，则写入

        $username = addslashes($cookie['cookie']);
        $check_sql = "select * from site_account where site_id = {$site['id']} and username = '{$username}'";
        $counts = $GLOBALS['db']->getRows($check_sql);

        if (count($counts) == 0) {
            //写数据
            //`site_id` int(11) NOT NULL,
            //  `username` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
            //  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
            //  `type` tinyint(2) unsigned DEFAULT '1' COMMENT '1 普通账号，2 mock账号',
            //  `deleted` tinyint(2) DEFAULT '0',
            $insert_sql = "insert into site_account(site_id,username,password,`type`) value ({$site['id']},'{$username}','{$username}',2);";

            dump($insert_sql);
            $GLOBALS['db']->query($insert_sql);
        }
    }

}