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

//登陆
$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';
$username = addslashes($username);
$password = addslashes($password);
$site_id = $_POST['site_id'] ?? 1;
$v = $_POST['v'] ?? 1;

$data = [
    'code' => 200,
    'data' => []
];
if ($v < 2) {
    $data['code'] = 40001;
    $data['message'] = '请升级插件';

    echo json_encode($data);
    exit();
}
$row = User::check_user($username, $password);
if ($row && strtotime($row['expired_at']) >= time()) {
    $last_plugin_id = time() . rand(1000, 9999);
    User::set_last_plugin_id($row['id'], $last_plugin_id);

    $site_expired_at = User::get_access_with($row['id'], $site_id);
    $data['data']['last_plugin_id'] = $last_plugin_id;
    $data['data']['site_expired_at'] = $site_expired_at;
    if (strtotime($site_expired_at) > time()) {
        $data['data']['is_active'] = true;
    } else {
        $data['data']['is_active'] = true;
    }
    //取账号
    $account_list = Account::get_site_list($site_id);
    foreach ($account_list as $key => $item) {
        $account_list[$key]['password'] = compileCode($item['password']);
    }

    $data['data']['account_list'] = array_reverse($account_list);
} elseif ($row && strtotime($row['expired_at']) < time()) {
    $data['code'] = 4001;
    $data['message'] = '账号到期';
} else {
    $data['code'] = 4002;
    $data['message'] = '账号或密码错误';
}

echo json_encode($data);
exit();
