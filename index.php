<?php

ini_set('date.timezone', 'PRC');
define('IN_DS', true);
define('INCLUDE_ROOT', dirname(__FILE__) . DIRECTORY_SEPARATOR . "front" . DIRECTORY_SEPARATOR);
require 'vendor/autoload.php';
include_once INCLUDE_ROOT . 'etc/init.php';

$script_uri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';

$script_uri_tmp_arr = explode('?', $script_uri);
$script_uri = empty($script_uri_tmp_arr) ? $script_uri : $script_uri_tmp_arr[0];

if (empty($script_uri) || $script_uri == "/") {
    //首页
    include_once FRONT_DIR . 'index.php';
} else {
    include_once FRONT_DIR . 'ahrefs.php';  //ahrefs
}

die;