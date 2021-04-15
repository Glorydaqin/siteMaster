<?php
//db_new_source
define('DB_HOST', 'localhost');
define('DB_NAME', 'site_master');
define('DB_USER', 'homestead');
define('DB_PASS', 'secret');
define('DB_PORT', 3306);

define('MYSQL_ENCODING', 'UTF8');
define('DEBUG_MODE', true);
define('DATA_ROOT', INCLUDE_ROOT . 'data/');
define('FRONT_DIR', INCLUDE_ROOT . 'action/');
define('LOG_DIR', dirname(INCLUDE_ROOT) . '/log/');

define('REDIS_HOST', '127.0.0.1');   //redis host
define('REDIS_PORT', 6379);   //redis port
define('REDIS_PRE', 'rent:');   //redis 缓存前缀

define("PROTOCOL", 'http://');
define('DOMAIN', 'test.com');
