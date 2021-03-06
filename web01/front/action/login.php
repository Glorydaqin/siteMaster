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
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $email = addslashes($email);
    $password = addslashes($password);

    $row = User::check_user($email, $password);
    $_SESSION['user_id'] = $row['id'];
    $_SESSION['username'] = $row['username'];
    $_SESSION['user_expired_at'] = $row['expired_at'];
    $_SESSION['site_expired_at'] = User::get_access($row['id']);

    //更新session_id
    $session_id = session_id();
    User::set_last_session_id($row['id'], $session_id);
    //更新redis user info
    User::cache_user_info($row['id'], $row);

    temporarily_header_302('/dash/');
} else {
    echo $tpl->render('login.php');
}

exit();