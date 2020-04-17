<?php
ini_set('date.timezone', 'PRC');
define('IN_DS', true);
define('INCLUDE_ROOT', dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . "front" . DIRECTORY_SEPARATOR);
$server_domain = $_SERVER['HTTP_HOST'] ?? '';

require '../vendor/autoload.php';
include_once INCLUDE_ROOT . 'etc/init.php';

$script_uri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
$script_uri_tmp_arr = explode('?', $script_uri);
$script_uri = empty($script_uri_tmp_arr) ? $script_uri : $script_uri_tmp_arr[0];


if ($server_domain == DOMAIN) {
    // 自主page
    if (empty($script_uri) || $script_uri == "/") {
        //首页
        include_once FRONT_DIR . 'login.php';
    } elseif ($script_uri == '/index/') {
        include_once FRONT_DIR . 'layout.php';
    } elseif ($script_uri == '/dashboard/') {
        include_once FRONT_DIR . 'dashboard.php';
    } elseif ($script_uri == '/choose/') {
        include_once FRONT_DIR . 'choose.php';
    } elseif ($script_uri == '/logout/') {
        include_once FRONT_DIR . 'logout.php';
    } elseif ($script_uri == '/test/') {
        include_once FRONT_DIR . 'test.php';
    } elseif ($script_uri == '/api/login/') {
        include_once FRONT_DIR . 'api_login.php';
    } elseif ($script_uri == '/api/check/') {
        include_once FRONT_DIR . 'api_check.php';
//    } elseif (substr($script_uri, 0, strlen('/admin_api/')) == '/admin_api/') {
//        include_once FRONT_DIR . 'admin_api.php';
//    } elseif (substr($script_uri, 0, strlen('/admin_view/')) == '/admin_view/') {
//        include_once FRONT_DIR . 'admin_view.php';
    } //image 下载类别
    elseif (substr($script_uri, 0, strlen('/image/')) == '/image/') {
        include_once FRONT_DIR . 'transfer_vip_image.php';
    } //文档 下载类别
    elseif (substr($script_uri, 0, strlen('/document/')) == '/document/') {
        include_once FRONT_DIR . 'transfer_vip_document.php';
    }
} elseif ($server_domain == 'www.' . DOMAIN) {
    temporarily_header_302(PROTOCOL . DOMAIN . '/');
} elseif ($server_domain == DOMAIN_AHREFS) {
    include_once FRONT_DIR . 'transfer_ahrefs.php';
} elseif ($server_domain == DOMAIN_SEMRUSH) {
    include_once FRONT_DIR . 'transfer_semrush.php';
} elseif ($server_domain == DOMAIN_MAJESTIC) {
    include_once FRONT_DIR . 'transfer_majestic.php';
} elseif (in_array($server_domain, [DOMAIN_KWFINDER, DOMAIN_LINKMINER, DOMAIN_SERPCHECKER, DOMAIN_SERPWATCHER, DOMAIN_SITEPROFILER])) {
    include_once FRONT_DIR . 'transfer_mangools.php';
} elseif ($server_domain == DOMAIN_SPYFU) {
    include_once FRONT_DIR . 'transfer_spyfu.php';
}


die("错误的访问 | error access");
