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
$last_plugin_id = $_POST['last_plugin_id'];
$username = addslashes($username);
$password = addslashes($password);

$data = [
    'code' => 200,
    'data' => []
];
$row = User::check_user($username, $password);
if ($row && strtotime($row['expired_at']) >= time()) {
    if($last_plugin_id != $row['last_plugin_id']){
        $data['code'] = 4005;
    }

} elseif ($row && strtotime($row['expired_at']) < time()) {
    $data['code'] = 4001;
} else {
    $data['code'] = 4002;
}

echo json_encode($data);
exit();
