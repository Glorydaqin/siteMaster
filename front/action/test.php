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

//include_once 'common.php';

try {
    $account = Account::get_account(2, 'mangools');
    //登陆
    $transfer = new KwFinder($account['username'], $account['password']);

    $url = 'mangools_domain/users/current_user?r=1576054069238&sso_ticket';
    $url = $transfer->revoke_url($url);
    d($url);
    $response = $transfer->get($url);
    dd($response);



    echo $html;
} catch (\Exception $exception) {
    dd($exception);
}
exit();