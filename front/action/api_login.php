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

$data = [
    'code' => 200,
    'data' => []
];
$row = User::check_user($username, $password);
if ($row && strtotime($row['expired_at']) >= time()) {
    $site_expired_at = User::get_access_with($row['id'], $site_id);
    $data['data']['site_expired_at'] = $site_expired_at;
    if (strtotime($site_expired_at) > time()) {
        $data['data']['is_active'] = true;
    } else {
        $data['data']['is_active'] = true;
    }

} elseif ($row && strtotime($row['expired_at']) < time()) {
    $data['code'] = 4001;
} else {
    $data['code'] = 4002;
}

echo json_encode($data);
exit();