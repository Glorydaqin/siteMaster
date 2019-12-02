<?php
if (!defined('IN_DS')) {
    die('Hacking attempt');
}
//config
define('VER', 'v201801');
define('SITE_FOLDER_PRE','_in_ds_');
define('DOMAIN', 'http://sitemaster.test.com/');
define("CACHE_FUNC_DEBUG_MODE", false);
define('CACHE_PATH', INCLUDE_ROOT.'data/cache_html/');
define('CLEAR_CACHE_KEY', "del_cache_key");
define('DIR_TMP_COOKIE', INCLUDE_ROOT.'data/cookie/');

define('DATA_ROOT', INCLUDE_ROOT . 'log/');
define('LOG_LOCATION', INCLUDE_ROOT . 'data/log/');
define('MEM_CACHE_LOG', INCLUDE_ROOT . 'data/');

function __lib_autoload($class)
{
    $class_file = INCLUDE_ROOT . 'lib/Class.' . $class . '.php';

    if(file_exists($class_file))
        return include_once($class_file);
}
spl_autoload_register("__lib_autoload");