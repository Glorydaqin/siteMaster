<?php
//db_new_source
define('DB_HOST', 'localhost');
define('DB_NAME', 'site_master');
define('DB_USER', 'homestead');
define('DB_PASS', 'secret');

define('MYSQL_ENCODING', 'UTF8');
define('DEBUG_MODE', true);

define('HTTP_HOST', $_SERVER['HTTP_HOST']);
define('SITE_URL', 'https://' . HTTP_HOST);
define('FRONT_DIR', INCLUDE_ROOT . 'action/');

define("PROTOCOL", 'http://');
define('DOMAIN', 'test.com');
define('DOMAIN_KWFINDER', 'kwfinder.test.com');
define('DOMAIN_AHREFS', 'ahrefs.test.com');