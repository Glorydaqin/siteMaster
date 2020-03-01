<?php
/**
 * 都要调用的数据
 * Created by PhpStorm.
 * User: daqin
 * Date: 2018/3/14
 * Time: 18:46
 */
if (!defined('IN_DS')) {
    die('Hacking attempt');
}

if (!DEBUG_MODE) {
    die('error access');
}

try {
    $username = "692860800@qq.com";
    $password = "daqing";
    //登陆
    $transfer = new SEMRush($username, $password);


    $response = $transfer->get(SEMRush::$domain . 'dashboard/');
    dd($response);

} catch (\Exception $exception) {
    dd($exception);
}
exit();