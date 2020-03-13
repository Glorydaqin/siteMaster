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
    $choose_session = $_SESSION['spyfu'] ?? [];
    $account_id = $choose_session['account_id'] ?? '';
    $site_id = $choose_session['site_id'] ?? '';

//转发页面
    $url = trim($url, '/');
    $first_sub = explode('/', $url)[0];

//检查账号存在
    $account = Account::get_account($account_id, $site_id);
    if (empty($account)) {
        die('account error | 账号错误');
    }

    if (in_array($first_sub, [
        'account'
    ])) {
        die('folder limit ｜ 目录访问限制');
    }
    $transfer = new SpyFu($account['username'], $account['password']);

    $url_is_cdn = (stripos($url, 'cdn_spyfu') !== false) ? true : false;

    $real_url = SpyFu::$domain . $url;
    $keywordLimit = 0;
    if ($url_is_cdn) {
        $real_url = SpyFu::$cdn_domain . substr($url, strlen('cdn_spyfu/'));
    } else {
        //验证和记录访问
        $transfer->check_limit($url, $_SESSION['user_id']);
    }

    $raw_data = file_get_contents('php://input');
    if (!empty($raw_data)) {
        $post_data = $raw_data;
    } else {
        $post_data = $_POST;
    }

    if (stripos($real_url, 'site-explorer/csv-download')) {
        //下载接口使用分段下载
        header("Content-Type:text/csv");
        header('Content-Disposition: attachment; filename="file.csv"');
        $transfer->curl_download($real_url, $post_data);
        die;
    } else {
        $response = $transfer->get($real_url, $post_data, $url_is_cdn);
    }

    $html = $response['body'];
    if ($url_is_cdn) {
        header("Cache-Control: public");
        header("Pragma: cache");
        $offset = 60 * 60 * 24 * 7; // cache 7 day
        $ExpStr = "Expires: " . gmdate("D, d M Y H:i:s", time() + $offset) . " GMT";
        header($ExpStr);
        header('Content-Type: ' . $response['info']['content_type']);

//        //给subscriptionMessage 加上display none
//        if (stripos($url, 'css/ahrefs.css')) {
//            $html .= "
//        [class$='subscriptionMessage']{display:none}";
//        }
//        if (stripos($url, 'css/style.css')) {
//            $html .= "
//        .dropdown-item--title.user-title{display:none}";
//        }
        echo $html;
        die;
    }
    //记录操作
    UserRecord::record($_SESSION['user_id'], $site_id, $account_id, $url);

    if (stripos(' ' . $response['info']['content_type'], 'text/html')) {
// 替换内容
//链接
        $html = preg_replace_callback("/href=[\'\"](.*?)[\'\"]/", function ($matches) {
            // 明确的当前域名 开头
            if (substr($matches[1], 0, 1) != '/') {
                // 不明确的域名开头
                if (stripos($matches[1], 'cdn1.spyfu.com')) {
                    return 'href="/cdn_spyfu/' . substr($matches[1], stripos($matches[1], 'spyfu.com') + strlen('spyfu.com') + 1) . '"';
                } elseif (stripos($matches[1], 'spyfu.com')) {
                    return 'href="/' . substr($matches[1], stripos($matches[1], 'spyfu.com') + strlen('spyfu.com') + 1) . '"';
                }
            }
            return $matches[0];
        }, $html);
        $html = preg_replace_callback("/href=([a-z0-9\-\/\_\:\.]+)/", function ($matches) {
            // 明确的当前域名 开头
            if (substr($matches[1], 0, 1) != '/') {
                // 不明确的域名开头
                if (stripos($matches[1], 'cdn1.spyfu.com')) {
                    return 'href="/cdn_spyfu/' . substr($matches[1], stripos($matches[1], 'spyfu.com') + strlen('spyfu.com') + 1) . '"';
                } elseif (stripos($matches[1], 'spyfu.com')) {
                    return 'href="/' . substr($matches[1], stripos($matches[1], 'spyfu.com') + strlen('spyfu.com') + 1) . '"';
                }
            }
            return $matches[0];
        }, $html);

//    //资源
        $html = preg_replace_callback("/src=[\'\"](.*?)[\'\"]/", function ($matches) {
            // 明确的当前域名 开头
            if (substr($matches[1], 0, 1) != '/') {
                // 不明确的域名开头
                if (stripos($matches[1], 'cdn1.spyfu.com')) {
                    return 'src="/cdn_spyfu/' . substr($matches[1], stripos($matches[1], 'spyfu.com') + strlen('spyfu.com') + 1) . '"';
                } elseif (stripos($matches[1], 'spyfu.com')) {
                    return 'src="/' . substr($matches[1], stripos($matches[1], 'spyfu.com') + strlen('spyfu.com') + 1) . '"';
                }
            }
            return $matches[0];
        }, $html);
        $html = preg_replace_callback("/src=([a-z0-9\-\/\_\:\.]+)/", function ($matches) {
            // 明确的当前域名 开头
            if (substr($matches[1], 0, 1) != '/') {
                // 不明确的域名开头
                if (stripos($matches[1], 'cdn1.spyfu.com')) {
                    return 'src="/cdn_spyfu/' . substr($matches[1], stripos($matches[1], 'spyfu.com') + strlen('spyfu.com') + 1) . '"';
                } elseif (stripos($matches[1], 'spyfu.com')) {
                    return 'src="/' . substr($matches[1], stripos($matches[1], 'spyfu.com') + strlen('spyfu.com') + 1) . '"';
                }
            }
            return $matches[0];
        }, $html);

        //替换用户信息
//        $html = str_replace($account['username'], 'account_' . $account_id, $html);
    }

    header('Content-Type: ' . $response['info']['content_type']);
    echo $html;
} catch (\Exception $exception) {
    Log::info($exception);
}
exit();