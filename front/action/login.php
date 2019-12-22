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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    //登陆
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $username = addslashes($username);
    $password = addslashes($password);

    $row = User::check_user($username, $password);
    if ($row && strtotime($row['expired_at']) >= time()) {
        $_SESSION['user_id'] = $row['id'];
        $_SESSION['username'] = $row['username'];
        $_SESSION['user_expired_at'] = $row['expired_at'];
        $_SESSION['site_expired_at'] = User::get_access($row['id']);

        temporarily_header_302('/index/');
    } elseif ($row && strtotime($row['expired_at']) < time()) {
        die('user deny | 账号过期');
    } else {
        die('user deny | 登陆失败');
    }
} else {
    echo $tpl->render('login.php');
}

exit();