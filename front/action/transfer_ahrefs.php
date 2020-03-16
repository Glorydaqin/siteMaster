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
    $choose_session = $_SESSION['ahrefs'] ?? [];
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
        'account', 'user'
    ])) {
        die('folder limit ｜ 目录访问限制');
    }
    $type = $account['type'] == 2 ? 'mock' : 'normal';
    $transfer = new Ahrefs($account['username'], $account['password'], $type);

    $url_is_cdn = (stripos($url, 'cdn_ahrefs_com') !== false) ? true : false;

    $real_url = Ahrefs::$domain . $url;
    $keywordLimit = 0;
    if ($url_is_cdn) {
        $real_url = Ahrefs::$cdn_domain . substr($url, strlen('cdn_ahrefs_com/'));
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

        //给subscriptionMessage 加上display none
        if (stripos($url, 'css/ahrefs.css')) {
            $html .= "
        [class$='subscriptionMessage']{display:none}";
        }
        if (stripos($url, 'css/style.css')) {
            $html .= "
        .dropdown-item--title.user-title{display:none}";
        }
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
                if (stripos($matches[1], 'cdn.ahrefs.com')) {
                    return 'href="' . PROTOCOL . DOMAIN_AHREFS . '/cdn_ahrefs_com/' . substr($matches[1], stripos($matches[1], 'ahrefs.com') + strlen('ahrefs.com') + 1) . '"';
                } elseif (stripos($matches[1], 'ahrefs.com')) {
                    return 'href="' . PROTOCOL . DOMAIN_AHREFS . '/' . substr($matches[1], stripos($matches[1], 'ahrefs.com') + strlen('ahrefs.com') + 1) . '"';
                }
            }
            return $matches[0];
        }, $html);

//    //资源
        $html = preg_replace_callback("/src=[\'\"](.*?)[\'\"]/", function ($matches) {
            // 明确的当前域名 开头
            if (substr($matches[1], 0, 1) != '/') {
                // 不明确的域名开头
                if (stripos($matches[1], 'cdn.ahrefs.com')) {
                    return 'src="' . PROTOCOL . DOMAIN_AHREFS . '/cdn_ahrefs_com/' . substr($matches[1], stripos($matches[1], 'ahrefs.com') + strlen('ahrefs.com') + 1) . '"';
                } elseif (stripos($matches[1], 'ahrefs.com')) {
                    return 'src="' . PROTOCOL . DOMAIN_AHREFS . '/' . substr($matches[1], stripos($matches[1], 'ahrefs.com') + strlen('ahrefs.com') + 1) . '"';
                }
            }
            return $matches[0];
        }, $html);

        //替换用户信息
        $html = str_replace($account['username'], 'account_' . $account_id, $html);
        //替换account 按钮为 limit & usage
//        $html = str_replace("<a href=\"/account/my-account\" class=\"dropdown-item\">Account settings</a>",
//            "<a href=\"/account/limits-and-usage/web\" class=\"dropdown-item\">Limits & Usage</a>", $html);
        //放上余量信息
        $html = $transfer->get_limit($_SESSION['user_id'], $html);
    }

    header('Content-Type: ' . $response['info']['content_type']);
    echo $html;
} catch (\Exception $exception) {
    Log::info($exception);
}
exit();
