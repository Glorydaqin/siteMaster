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

$nav = [
    ['url' => '/dashboard/', 'name' => 'SEO工具', 'fa' => 'fa-amazon'],
];
//if (User::is_admin()) {
//    $nav[] = ['url' => '/admin/user_list', 'name' => '用户管理', 'fa' => 'fa-amazon'];
//}

$tpl->assign('nav', $nav);
echo $tpl->render('layout.php');
exit();
