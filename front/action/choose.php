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

$account_id = $_GET['account_id'] ?? '';
$site_id = $_GET['site_id'] ?? '';
$site_name = $_GET['site_name'] ?? '';

if (empty($site_id) || empty($account_id) || !is_numeric($site_id) || !is_numeric($account_id)) {
    die('error param | 参数错误');
}

$account_info = Account::get_account_with_site($account_id, $site_id);
if (empty($account_info)) {
    die("error param | 参数错误");
}
//缓存账号信息
$redis = new RedisCache();
$key = REDIS_PRE . 'account_info:' . $account_id;
$redis->set_cache($key, json_encode($account_info));

//有数据了就该往外站跳转了
if ($account_info['site_name'] == 'ahrefs') {
    $url = PROTOCOL . DOMAIN_AHREFS . '/dashboard';
} elseif ($account_info['site_name'] == 'mangools') {
    if ($site_name == 'serpchecker') {
        $url = PROTOCOL . DOMAIN_SERPCHECKER . '/?sso_ticket={{sso_ticket}}&login_token={{login_token}}';
    } elseif ($site_name == 'serpwatcher') {
        $url = PROTOCOL . DOMAIN_SERPWATCHER . '/?sso_ticket={{sso_ticket}}&login_token={{login_token}}';
    } elseif ($site_name == 'linkminer') {
        $url = PROTOCOL . DOMAIN_LINKMINER . '/?sso_ticket={{sso_ticket}}&login_token={{login_token}}';
    } elseif ($site_name == 'siteprofiler') {
        $url = PROTOCOL . DOMAIN_SITEPROFILER . '/?sso_ticket={{sso_ticket}}&login_token={{login_token}}';
    } else {
        $url = PROTOCOL . DOMAIN_KWFINDER . '/?sso_ticket={{sso_ticket}}&login_token={{login_token}}';
    }
} elseif ($account_info['site_name'] == 'majestic') {
    $url = PROTOCOL . DOMAIN_MAJESTIC . '/account';
} elseif ($account_info['site_name'] == 'semrush') {
    $url = PROTOCOL . DOMAIN_SEMRUSH . '/dashboard/';
} elseif ($account_info['site_name'] == 'spyfu') {
    $url = PROTOCOL . DOMAIN_SPYFU . '/hq/';
} else {
    //回去重新选site
    $url = PROTOCOL . DOMAIN . '/dashboard/';
}
set_choose_session($site_id, $account_id, $account_info['site_name']);

temporarily_header_302($url);
//page_jump($url);
exit();