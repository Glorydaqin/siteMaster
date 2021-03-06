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

include_once 'common.php';

try {

    $url = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
    $account_id = $_COOKIE['account_id'];

//检查账号存在
    $account = Account::get_account($account_id, $_COOKIE['site_id']);
    if (empty($account)) {
        die('account error | 账号错误');
    }

    if (
        stripos($url, '/account/') !== false
    ) {
        die('folder limit ｜ 目录访问限制');
    }

    $url = trim($url, '/');
    $url_is_cdn = false;
    if (stripos($url, '.js') || stripos($url, '.css') || stripos($url, '.svg')) {
        $url_is_cdn = true;
    }

    $transfer = new Majestic($account['username'], $account['password']);
    $real_url = $transfer->revoke_url($url);

    $raw_data = file_get_contents('php://input');
    if (!empty($raw_data)) {
        $post_data = $raw_data;
    } else {
        $post_data = $_POST;
    }

    $response = $transfer->get($real_url, $post_data, $url_is_cdn);
    $html = $response['body'];

    if (stripos($real_url, '.js') || stripos($real_url, '.css') || stripos($real_url, '.svg')) {
        header("Cache-Control: public");
        header("Pragma: cache");
        $offset = 60 * 60 * 24; // cache 1 day
        $ExpStr = "Expires: " . gmdate("D, d M Y H:i:s", time() + $offset) . " GMT";
        header($ExpStr);
        header('Content-Type: ' . $response['info']['content_type']);

        echo $transfer->replace_main_js($real_url, $html);
        die;
    }

    //记录操作
    UserRecord::record($_SESSION['user_id'], $_COOKIE['site_id'], $account_id, $url);

// 替换内容
    if (isset($response['info']['content_type']) && isset($response['info']['content_type']) == 'text/html') {

//链接
        $html = preg_replace_callback("/href=[\'\"](.*?)[\'\"]/", function ($matches) {
            // 明确的当前域名 开头
//            if (substr($matches[1], 0, 1) != '/') {
//                // 非主站域名
//                if (stripos($matches[1], 'app.kwfinder.com') === false) {
//                    if (stripos($matches[1], 'mangools.com')) {
//                        return 'href="' . '/mangools_domain/' . substr($matches[1], stripos($matches[1], 'mangools.com') + strlen('mangools.com') + 1) . '"';
//                    }
//
//                } elseif (stripos($matches[1], 'app.kwfinder.com')) {
//                    return 'href="' . PROTOCOL . DOMAIN_KWFINDER . '/' . substr($matches[1], stripos($matches[1], 'app.kwfinder.com') + strlen('app.kwfinder.com') + 1) . '"';
//                }
//            }
            return $matches[0];
        }, $html);
//    //资源
        $html = preg_replace_callback("/src=[\'\"](.*?)[\'\"]/", function ($matches) {
            // 明确的当前域名 开头
//            if (substr($matches[1], 0, 1) != '/') {
//                // 非主站域名
//                if (stripos($matches[1], 'app.kwfinder.com') === false) {
//                    if (stripos($matches[1], 'mangools.com')) {
//                        return 'src="' . '/mangools_domain/' . substr($matches[1], stripos($matches[1], 'mangools.com') + strlen('mangools.com') + 1) . '"';
//                    }
//
//                } elseif (stripos($matches[1], 'app.kwfinder.com')) {
//                    return 'src="' . PROTOCOL . DOMAIN_KWFINDER . '/' . substr($matches[1], stripos($matches[1], 'app.kwfinder.com') + strlen('app.kwfinder.com') + 1) . '"';
//                }
//            }
            return $matches[0];
        }, $html);

        //替换用户信息
        $html = str_replace($account['username'], 'account_' . $account_id, $html);
    }

    header('Content-Type: ' . $response['info']['content_type']);
    echo $html;
} catch (\Exception $exception) {
    Log::info($exception);
}
exit();