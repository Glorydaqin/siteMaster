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

dd(1);
try {
    $account = Account::get_account(2, 'semrush');
    //登陆
    $transfer = new SEMRush($account['username'], $account['password']);


    $response = $transfer->get(SEMRush::$domain . 'dashboard');
    dd($response);

} catch (\Exception $exception) {
    dd($exception);
}
exit();