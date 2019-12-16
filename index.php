<?php
ini_set('date.timezone', 'PRC');
define('IN_DS', true);
define('INCLUDE_ROOT', dirname(__FILE__) . DIRECTORY_SEPARATOR . "front" . DIRECTORY_SEPARATOR);
require 'vendor/autoload.php';
include_once INCLUDE_ROOT . 'etc/init.php';

$server_domain = $_SERVER['HTTP_HOST'] ?? '';
$script_uri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
//dd($server_domain);
$script_uri_tmp_arr = explode('?', $script_uri);
$script_uri = empty($script_uri_tmp_arr) ? $script_uri : $script_uri_tmp_arr[0];

if ($server_domain == DOMAIN) {
    // 自主page
    if (empty($script_uri) || $script_uri == "/") {
        //首页
        include_once FRONT_DIR . 'index.php';
    } elseif ($script_uri == '/choose_site/') {
        include_once FRONT_DIR . 'choose_site.php';
    } elseif ($script_uri == '/choose_account/') {
        include_once FRONT_DIR . 'choose_account.php';
    } elseif ($script_uri == '/test/') {
        include_once FRONT_DIR . 'test.php';
    }
} elseif (in_array($server_domain, [DOMAIN_AHREFS, DOMAIN_KWFINDER])) {
    if ($server_domain == DOMAIN_AHREFS) {
        include_once FRONT_DIR . 'transfer_ahrefs.php';
    } elseif ($server_domain == DOMAIN_KWFINDER) {
        include_once FRONT_DIR . 'transfer_kwfinder.php';
    } elseif ($server_domain == DOMAIN_SEMRUSH) {
        include_once FRONT_DIR . 'transfer_kwfinder.php';
    }
}
die("错误的访问 | error access");