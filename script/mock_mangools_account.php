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
$sleep_time = rand(15, 180);
sleep($sleep_time);

//
$url = "http://www.xixuanseo.com/aaa/login.php";
//type: xy1
//para: zxzuan@gmail.com
//parb: 5999
//parc: 183.67.56.241
//pard: 重庆市

$data = [
    "type" => 'xy1',
    'para' => 'zxzuan@gmail.com',
    'parb' => '5999',
    'parc' => '183.67.56.241',
    'pard' => '重庆市'
];
$response = curl($url, $data);

if ($response['code'] == 200) {
    $db = new Mysql(DB_NAME, DB_HOST, DB_USER, DB_PASS, DB_PORT);
    $site_sql = "select * from site where name='mangools'";
    $site = $db->getFirstRow($site_sql);

    if (!$site) {
        die('error');
    }


    $json = explode(',', $response['body']);
    $cookies = [];
    foreach ($json as $item) {
        if (strlen($item) > 300) {
            $cookies[] = $item;
        }
    }

    //mock类型的先删除后写入
    $db_accounts_sql = "select * from site_account where site_id = {$site['id']} and type=2";
    $db_accounts = $db->getRows($delete_sql);
    $key_map = array_combine(array_column($db_accounts, 'username'), array_column($db_accounts, 'id'));
    foreach ($cookies as $cookie) {
        if (isset($key_map[$cookie])) {
            //更新为未删除
            $up_sql = "update site_account set deleted=0 where id={$key_map[$cookie]} and deleted = 1";
            $db->query($up_sql);
            unset($key_map[$cookie]);
        } else {
            //新写入
            //`site_id` int(11) NOT NULL,
            //  `username` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
            //  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
            //  `type` tinyint(2) unsigned DEFAULT '1' COMMENT '1 普通账号，2 mock账号',
            //  `deleted` tinyint(2) DEFAULT '0',
            $insert_sql = "insert into site_account(site_id,username,password,`type`) value ({$site['id']},'{$username}','{$username}',2);";

            $db->query($insert_sql);
        }
    }

    //剩下的标记删除
    foreach ($key_map as $id) {
        $up_sql = "update site_account set deleted=1 where id={$id} and deleted = 0";
        $db->query($up_sql);
    }

}
