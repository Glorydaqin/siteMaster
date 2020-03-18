<?php
error_reporting(E_ALL);
ini_set('date.timezone', 'PRC');
define('IN_DS', true);
define('ROOT_PATH', dirname(dirname(__FILE__)));
define('INCLUDE_ROOT', ROOT_PATH . DIRECTORY_SEPARATOR . "front" . DIRECTORY_SEPARATOR);
$server_domain = '';
require ROOT_PATH . '/vendor/autoload.php';
include_once INCLUDE_ROOT . 'etc/init.php';

set_time_limit(300);
//从竞争对手插件里拿账号cookie
//随机等待时间
//$sleep_time = rand(5, 50);
//sleep($sleep_time);

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
//dump($data);
$response = curl($url, $data);
d($response);
if ($response['code'] == 200) {
    $db = new Mysql(DB_NAME, DB_HOST, DB_USER, DB_PASS, DB_PORT);
    $site_sql = "select * from site where name='mangools'";
    $site = $db->getFirstRow($site_sql);

    if (!$site) {
        die('site error');
    }

//    dump($response['body']);
    $json = explode(',', $response['body']);
    $cookies = [];
    foreach ($json as $item) {
        if (strlen($item) > 300 && !isset($cookies[$item])) {
            $cookies[$item] = $item;
        }
    }

    //mock类型的先删除后写入
    $db_accounts_sql = "select * from site_account where site_id = {$site['id']} and `type`=2;";
    $db_accounts = $db->getRows($db_accounts_sql);

    $key_map = array_combine(array_column($db_accounts, 'username'), array_column($db_accounts, 'id'));

    $insert = $update = $delete = 0;
    foreach ($cookies as $cookie) {

        if (isset($key_map[$cookie])) {
            //更新为未删除
            $up_sql = "update site_account set deleted=0 where id={$key_map[$cookie]} and deleted = 1";

            $db->query($up_sql);
            unset($key_map[$cookie]);
            $update++;
        } else {
            $insert_sql = "insert into site_account(site_id,username,password,`type`) value ({$site['id']},'{$cookie}','{$cookie}',2);";
            $db->query($insert_sql);
            $insert++;
        }
    }

    //剩下的标记删除
    foreach ($key_map as $id) {
        $up_sql = "update site_account set deleted=1 where id={$id} and deleted = 0";
        $db->query($up_sql);
        $delete++;
    }

    dd('finish , insert ' . $insert . ",update " . $update . ',delete ' . $delete);
}
