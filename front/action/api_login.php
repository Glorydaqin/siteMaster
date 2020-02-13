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

$data = [
    'code' => 200,
    'data' => []
];
$row = User::check_user($username, $password);
if ($row && strtotime($row['expired_at']) >= time()) {
    $_SESSION['user_id'] = $row['id'];
    $_SESSION['username'] = $row['username'];
    $_SESSION['user_expired_at'] = $row['expired_at'];
    $_SESSION['site_expired_at'] = User::get_access($row['id']);

    $data['data'] = User::get_access($row['id']);
} elseif ($row && strtotime($row['expired_at']) < time()) {
    $data['code'] = 4001;
} else {
    $data['code'] = 4002;
}

echo json_encode($data);
exit();