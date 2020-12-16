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
$last_plugin_id = addslashes($last_plugin_id);
$url = addslashes($url);
$account_id = addslashes($account_id);
$data = addslashes(json_encode($data));
if (empty($last_plugin_id) || empty($url) || empty($account_id)) {
    $data['code'] = 4001;

    echo json_encode($data);
    exit();
}

try {

    $site_account_info = Account::get_site_account($account_id);
    $user_info = User::get_user_by_plugin_id($last_plugin_id);
    if (!$site_account_info || !$user_info) {
        $data['code'] = 4002;

        echo json_encode($data);
        exit();
    }
    UserRecord::save($user_info['id'], $site_account_info['site_id'], $site_account_info['id'], $url);

    echo json_encode($data);
    exit();
} catch (\Exception $exception) {
    $data['code'] = 4004;
    $data['message'] = $exception->getMessage();

    echo json_encode($data);
    exit();
}
