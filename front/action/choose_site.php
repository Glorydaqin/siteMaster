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

include_once 'common.php';


if (!isset($_GET['site_id'])) {

    $site_list = Site::get_list_with_access($_SESSION['user_id']);
    $welcome = "欢迎登陆: {$_SESSION['username']}";

    $tpl->assign('username', $_SESSION['username']);
    $tpl->assign('site_list', $site_list);
    $tpl->assign('welcome', $welcome);

    //选择页面
    echo $tpl->render('choose_site.php');
    exit();
} else {
    $_SESSION['site_id'] = $_GET['site_id'];
    temporarily_header_302('/choose_account/');
}
exit();

