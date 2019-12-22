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
include_once 'common.php';

//获取站点
$site_list = Site::get_list_with_access($_SESSION['user_id']);
//dd($site_list);

foreach ($site_list as &$site) {
    $account_list = Account::get_site_list($site['id']);

    foreach ($account_list as &$account) {
        if ($site['name'] == 'ahrefs') {
            $target = [
                ['name' => $site['name'], 'url' => PROTOCOL . DOMAIN_AHREFS],
            ];
        } elseif ($site['name'] == 'mangools') {
            $target = [
                ['name' => 'kwfinder', 'url' => PROTOCOL . DOMAIN_KWFINDER],
                ['name' => 'serpchecker', 'url' => PROTOCOL . DOMAIN_SERPCHECKER],
                ['name' => 'serpwatcher', 'url' => PROTOCOL . DOMAIN_SERPWATCHER],
                ['name' => 'linkminer', 'url' => PROTOCOL . DOMAIN_LINKMINER],
                ['name' => 'siteprofiler', 'url' => PROTOCOL . DOMAIN_SITEPROFILER],
            ];
        } elseif ($site['name'] == 'semrush') {
            $target = [
                ['name' => $site['name'], 'url' => PROTOCOL . DOMAIN_SEMRUSH],
            ];
        } elseif ($site['name'] == 'majestic') {
            $target = [
                ['name' => $site['name'], 'url' => PROTOCOL . DOMAIN_MAJESTIC],
            ];
        }

        $account['target'] = $target;
    }
    $site['account_list'] = $account_list;
}
//dd($site_list);

$tpl->assign('site_list', $site_list);


echo $tpl->render('dashboard.php');
exit();