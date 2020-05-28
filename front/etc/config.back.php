<?php
//db_new_source
define('DB_HOST', 'mysql');
define('DB_NAME', 'rent');
define('DB_USER', 'root');
define('DB_PASS', 'root');
define('DB_PORT', 3306);

define('MYSQL_ENCODING', 'UTF8');
define('DEBUG_MODE', true);
define('DATA_ROOT', INCLUDE_ROOT . 'data/');
define('FRONT_DIR', INCLUDE_ROOT . 'action/');
define('LOG_DIR', dirname(INCLUDE_ROOT) . '/log/');

define('REDIS_HOST', 'redis');   //redis host
define('REDIS_PORT', 6379);   //redis port
define('REDIS_PRE', 'rent:');   //redis 缓存前缀

define("PROTOCOL", 'http://');
define('DOMAIN', 'sitemaster.com');
define('DOMAIN_AHREFS', 'ahrefs.' . DOMAIN);
define('DOMAIN_SEMRUSH', 'semrush.' . DOMAIN);
define('DOMAIN_MAJESTIC', 'majestic.' . DOMAIN);
define('DOMAIN_SPYFU', 'spyfu.' . DOMAIN);
//mangools 系列
define('DOMAIN_KWFINDER', 'kwfinder.' . DOMAIN);
define('DOMAIN_SERPCHECKER', 'serpchecker.' . DOMAIN);
define('DOMAIN_SERPWATCHER', 'serpwatcher.' . DOMAIN);
define('DOMAIN_LINKMINER', 'linkminer.' . DOMAIN);
define('DOMAIN_SITEPROFILER', 'siteprofiler.' . DOMAIN);
