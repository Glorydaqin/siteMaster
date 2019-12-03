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


//$url = $_GET['url'] ?? '';
$url = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
if ($url == '/' . SITE_FOLDER_PRE . "/ahrefs/" && !isset($_POST[SITE_FOLDER_PRE . 'account_id'])) {
    //选择页面
    $account_list = Account::get_site_list('ahrefs');
    $tpl->assign('account_list', $account_list);
    echo $tpl->render('ahrefs.php');
    exit();
} elseif (isset($_POST[SITE_FOLDER_PRE . 'account_id'])) {
    $_SESSION['account_id'] = $_POST[SITE_FOLDER_PRE . 'account_id'];
    temporarily_header_302('/dashboard');
}
exit();

