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

} else {
    echo $tpl->render('register.php');
}

exit();