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

if (isset($_POST['username'])) {
    //登陆
    $username = $_POST['username'];
    $password = $_POST['password'] ?? '';

    $check_sql = "select * from user where username = '{$username}' and password = '{$password}' and deleted = 0";
    $row = $GLOBALS['db']->getFirstRow($check_sql);
    if ($row) {
        session_start();
        $_SESSION['user_id'] = $row['id'];

        temporarily_header_302('/' . SITE_FOLDER_PRE . '/ahrefs/');
    } else {
        die('登陆失败');
    }
}

echo $tpl->render('index.php');
exit();