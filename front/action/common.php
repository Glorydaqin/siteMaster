<?php
/**
 * 都要调用的数据
 * Created by PhpStorm.
 * User: daqin
 * Date: 2018/3/14
 * Time: 18:46
 */

//dd($_SESSION);
//检测登陆状态
if (!isset($_SESSION['user_id'])) {
    temporarily_header_302(PROTOCOL . DOMAIN . '/');
}

if($_SERVER['HTTP_HOST'] != DOMAIN){
    // 转发子域名验证 session
    if (!isset($_SESSION['account_id'])) {
        die('choose account');
    }

}