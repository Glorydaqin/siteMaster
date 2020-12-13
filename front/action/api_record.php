<?php
/**
 * 都要调用的数据
 * Created by PhpStorm.
 * User: daqin
 * Date: 2018/3/14
 * Time: 18:46
 */
if (!defined('IN_DS')) {
    die('Hacking attempt');
}
$data = [
    'code' => 200,
    'data' => []
];

//登陆
$last_plugin_id = $_POST['last_plugin_id'] ?? '';
$url = $_POST['url'] ?? '';
$account_id = $_POST['account_id'] ?? '';
$data = $_POST['data'] ?? '{}';
$last_plugin_id = addslashes($last_plugin_id);
$url = addslashes($url);
$account_id = addslashes($account_id);
$data = addslashes(json_encode($data));
if (empty($last_plugin_id) || empty($url) || empty($account_id)) {
    $data['code'] = 4001;

    echo json_encode($data);
    exit();
}

$site_account_info = Account::get_site_account($account_id);
$user_info = User::get_user_by_plugin_id($last_plugin_id);



echo json_encode($data);
exit();
