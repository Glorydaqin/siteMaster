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
//判断登陆
include_once 'common_logined.php';

//获取站点
$site_list = Site::get_list_with_access($_SESSION['user_id']);

foreach ($site_list as &$site) {
    $account_list = Account::get_site_list($site['id'], 2);
    $site['account_list'] = $account_list;
}

$tpl->assign('site_list', $site_list);

echo $tpl->render('dash.php');
exit();