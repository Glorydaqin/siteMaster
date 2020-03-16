<?php
ini_set('date.timezone', 'PRC');
define('IN_DS', true);
define('ROOT_PATH', dirname(dirname(__FILE__)));
define('INCLUDE_ROOT', ROOT_PATH . DIRECTORY_SEPARATOR . "front" . DIRECTORY_SEPARATOR);
require ROOT_PATH . '/vendor/autoload.php';
include_once INCLUDE_ROOT . 'etc/init.php';

//初始化

//将data文件夹copy到配置的目录 先检查目录是否为空
if (file_exists(DATA_ROOT)) {
    echo "DATA_ROOT 目标目录存在，请重新配置目录";
}
system("cp -r " . INCLUDE_ROOT . "data/ " . DATA_ROOT);


echo "finish" . PHP_EOL;
