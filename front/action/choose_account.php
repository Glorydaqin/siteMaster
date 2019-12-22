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

if (!isset($_SESSION['site_id'])) {
    temporarily_header_302('/choose_site/');
}

if (!isset($_POST['account_id'])) {

    //选择页面
    $account_list = Account::get_site_list($_SESSION['site_id']);
    $tpl->assign('account_list', $account_list);

    //剩余账号信息
//    $user_keyword_limit = UserRecord::keywordsLimit;
//    $user_keyword_num = UserRecord::check_user_limit($_SESSION['user_id'], 'keywords');

    //welcome
//    $limit = $user_keyword_limit - $user_keyword_num;
//    $welcome = "欢迎登陆: {$_SESSION['username']} , 关键词剩余 : {$limit}/{$user_keyword_limit} , 账号有效期: {$_SESSION['user_expired_at']}";
    $site_info = Site::get_info($_SESSION['site_id']);
//    $expired_at = $_SESSION['site_expired_at'];
    $keyval = array_combine(array_column($_SESSION['site_expired_at'], 'site_id'), array_column($_SESSION['site_expired_at'], 'expired_at'));
    $expired_at = $keyval[$_SESSION['site_id']];
    $welcome = "欢迎登陆: {$_SESSION['username']} ; {$site_info['name']} - 账号有效期: {$expired_at}";
    $tpl->assign('welcome', $welcome);

    echo $tpl->render('choose_account.php');
    exit();
} else {
    if (!is_numeric($_POST['account_id'])) {
        die('error param | 参数错误');
    }
    $_SESSION['account_id'] = addslashes($_POST['account_id']);

    $account_info = Account::get_account_with_site($_SESSION['account_id'], $_SESSION['site_id']);
    if (empty($account_info)) {
        die("error param | 参数错误");
    }

    //有数据了就该往外站跳转了
    if ($account_info['site_name'] == 'ahrefs') {
        $url = PROTOCOL . DOMAIN_AHREFS . '/dashboard';
    } elseif ($account_info['site_name'] == 'mangools') {
        $url = PROTOCOL . DOMAIN_KWFINDER . '/dashboard';
    } elseif ($account_info['site_name'] == 'majestic') {
        $url = PROTOCOL . DOMAIN_MAJESTIC . '/account';
    } else {
        //回去重新选site
        $url = PROTOCOL . DOMAIN . '/choose_site/';
    }

    temporarily_header_302($url);
}
exit();

