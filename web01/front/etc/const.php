<?php
if (!defined('IN_DS')) {
    die('Hacking attempt');
}
//config
define('VER', 'v201801');
define('CLEAR_CACHE_KEY', "del_cache_key");
define('CACHE_PATH', DATA_ROOT . 'cache_html/');
define('DIR_TMP_COOKIE', DATA_ROOT . 'cookie/');
define('IMAGE_PATH', DATA_ROOT . 'image/');
define('LOG_LOCATION', INCLUDE_ROOT . 'data/log/');
define('MEM_CACHE_LOG', INCLUDE_ROOT . 'data/');

// 各站域名映射
define('DOMAIN_AHREFS', 'ahrefs.' . DOMAIN);
define('DOMAIN_SEMRUSH', 'semrush.' . DOMAIN);
define('DOMAIN_MAJESTIC', 'majestic.' . DOMAIN);
define('DOMAIN_SPYFU', 'spyfu.' . DOMAIN);
//mangools 系列
define('DOMAIN_MANGOOLS', 'mangools.' . DOMAIN);
define('DOMAIN_KWFINDER', 'kwfinder.' . DOMAIN);
define('DOMAIN_SERPCHECKER', 'serpchecker.' . DOMAIN);
define('DOMAIN_SERPWATCHER', 'serpwatcher.' . DOMAIN);
define('DOMAIN_LINKMINER', 'linkminer.' . DOMAIN);
define('DOMAIN_SITEPROFILER', 'siteprofiler.' . DOMAIN);


function __lib_autoload($class)
{
    $class_file = INCLUDE_ROOT . 'lib/Class.' . $class . '.php';
    $class_seo_file = INCLUDE_ROOT . 'lib/seo/Class.' . $class . '.php';
    $class_image_file = INCLUDE_ROOT . 'lib/image/Class.' . $class . '.php';

    if (file_exists($class_file))
        return include_once($class_file);
    if (file_exists($class_seo_file))
        return include_once($class_seo_file);
    if (file_exists($class_image_file))
        return include_once($class_image_file);
}

spl_autoload_register("__lib_autoload");

