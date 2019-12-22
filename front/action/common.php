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

//其他站点下检查配置
if ($_SERVER['HTTP_HOST'] != DOMAIN) {
    //site_id
    if (!isset($_SESSION['site_id'])) {
        temporarily_header_302(PROTOCOL . DOMAIN . '/choose_site/');
    }

    // 转发子域名验证 session
    if (!isset($_SESSION['account_id'])) {
        temporarily_header_302(PROTOCOL . DOMAIN . '/choose_account/');
    }

}