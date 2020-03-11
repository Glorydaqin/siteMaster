<?php
//db_new_source
define('DB_HOST', 'localhost');
define('DB_NAME', 'site_master');
define('DB_USER', 'homestead');
define('DB_PASS', 'secret');
define('DB_PORT', 3306);

define('MYSQL_ENCODING', 'UTF8');
define('DEBUG_MODE', true);

define('HTTP_HOST', $_SERVER['HTTP_HOST']);
define('SITE_URL', 'https://' . HTTP_HOST);
define('DATA_ROOT', INCLUDE_ROOT . 'data/');
define('FRONT_DIR', INCLUDE_ROOT . 'action/');
define('LOG_DIR', dirname(INCLUDE_ROOT) . '/log/');

define('REDIS_HOST', '127.0.0.1');   //redis host
define('REDIS_PORT', 6379);   //redis port
define('REDIS_PRE', 'rent:');   //redis 缓存前缀

define("PROTOCOL", 'http://');
define('DOMAIN', 'test.com');
define('DOMAIN_AHREFS', 'ahrefs.' . DOMAIN);
define('DOMAIN_SEMRUSH', 'semrush.' . DOMAIN);
define('DOMAIN_MAJESTIC', 'majestic.' . DOMAIN);
//mangools 系列
define('DOMAIN_KWFINDER', 'kwfinder.' . DOMAIN);
define('DOMAIN_SERPCHECKER', 'serpchecker.' . DOMAIN);
define('DOMAIN_SERPWATCHER', 'serpwatcher.' . DOMAIN);
define('DOMAIN_LINKMINER', 'linkminer.' . DOMAIN);
define('DOMAIN_SITEPROFILER', 'siteprofiler.' . DOMAIN);