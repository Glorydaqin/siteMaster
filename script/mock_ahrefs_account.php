<?php
ini_set('date.timezone', 'PRC');
define('IN_DS', true);
define('ROOT_PATH', dirname(dirname(__FILE__)));
define('INCLUDE_ROOT', ROOT_PATH . DIRECTORY_SEPARATOR . "front" . DIRECTORY_SEPARATOR);
require ROOT_PATH . '/vendor/autoload.php';
include_once INCLUDE_ROOT . 'etc/init.php';

set_time_limit(300);
//从竞争对手插件里拿账号cookie
//随机等待时间
$sleep_time = rand(15, 50);
sleep($sleep_time);

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
    $db = new Mysql(DB_NAME, DB_HOST, DB_USER, DB_PASS, DB_PORT);
    $site_sql = "select * from site where name='ahrefs'";
    $site = $db->getFirstRow($site_sql);

    $json = json_decode($response['body'], true);
    $cookies = $json['cookies'] ?? [];

    $insert = $update = 0;
    foreach ($cookies as $cookie) {
        //如果不存在，则写入

        $username = addslashes($cookie['cookie']);
        $check_sql = "select * from site_account where site_id = {$site['id']} and username = '{$username}'";
        $counts = $db->getRows($check_sql);

        if (count($counts) == 0) {
            //拿一条删除的数据来修改
            $get_sql = "select * from site_account where site_id = {$site['id']} and `type` = 2 and deleted = 1 limit 1";
            $account = $db->getFirstRow($get_sql);

            if ($account) {
                $up_sql = "update site_account set username = '{$username}',password = '{$username}',deleted = 0,`type` = 2 where id = {$account['id']};";
                $db->query($up_sql);
                $update++;
            } else {
                //写数据
                //`site_id` int(11) NOT NULL,
                //  `username` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
                //  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
                //  `type` tinyint(2) unsigned DEFAULT '1' COMMENT '1 普通账号，2 mock账号',
                //  `deleted` tinyint(2) DEFAULT '0',
                $insert_sql = "insert into site_account(site_id,username,password,`type`) value ({$site['id']},'{$username}','{$username}',2);";
                $db->query($insert_sql);
                $insert++;
            }
        }
    }

    dd('finish , insert ' . $insert . ',update ' . $update);
}
