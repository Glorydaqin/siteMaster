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


if (empty($script_uri) || $script_uri == "/") {
    //首页
    include_once FRONT_DIR . 'index.php';
} elseif ($script_uri == '/dash/') {
//        include_once FRONT_DIR . 'layout.php';
    include_once FRONT_DIR . 'dash.php';
} elseif ($script_uri == '/dashboard/') {
    include_once FRONT_DIR . 'dashboard.php';
} elseif ($script_uri == '/logout/') {
    include_once FRONT_DIR . 'logout.php';
} elseif ($script_uri == '/login/') {
    include_once FRONT_DIR . 'login.php';
} elseif ($script_uri == '/privacy/' || $script_uri == '/terms/') {
    include_once FRONT_DIR . 'static.php';
} else {
    temporarily_header_401();
}

