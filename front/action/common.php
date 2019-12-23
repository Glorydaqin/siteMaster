<?php
/**
 * 都要调用的数据
 * Created by PhpStorm.
 * User: daqin
 * Date: 2018/3/14
 * Time: 18:46
 */

/// 登陆服务下检查登陆
if (!isset($_SESSION['user_id'])) {
    temporarily_header_302(PROTOCOL . DOMAIN . '/');
}
//检查session_id 是否一致
$session_id = session_id();
$last_session_id = User::get_last_session_id($_SESSION['user_id']);
if ($last_session_id != $session_id) {
    session_unset();
    temporarily_header_302(PROTOCOL . DOMAIN . '/');
}

//其他站点下检查配置
if ($_SERVER['HTTP_HOST'] != DOMAIN) {
    //site_id
    if (!isset($_SESSION['site_id'])) {
        die('参数错误，请关闭重试');
    }

    // 转发子域名验证 session
    if (!isset($_SESSION['account_id'])) {
        die('参数错误，请关闭重试');
    }
}