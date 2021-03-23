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
    $choose_session = $_SESSION['mangools'] ?? [];
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
        'account', 'user', 'logout'
    ])) {
        die('folder limit ｜ 目录访问限制');
    }

    $url = trim($url, '/');
    $url_is_cdn = false;
    if (stripos($url, '.js') || stripos($url, '.css') || stripos($url, '.svg')) {
        $url_is_cdn = true;
    }

    //查询记录数
//    $keywordLimit = UserRecord::check_user_limit($_SESSION['user_id'], $_SESSION['site_id'], 'site-explorer/overview/v2/subdomains/live');
//    if ($keywordLimit >= UserRecord::keywordsLimit) {
//        die('Reach the keywords limit | 达到关键词限制');
//    }
    $transfer = new Mangools($account);
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

        $html = $transfer->replace_main_js($real_url, $html);
    }
    //http://kwfinder.vipfor.me/mangools_api_domain/v3/kwfinder/serps?kw=ss&location_id=0&page=0 实际搜索的链接
    //记录操作
//    UserRecord::record($_SESSION['user_id'], $_COOKIE['site_id'], $account_id, $url);

// 替换内容
    if (isset($response['info']['content_type']) && isset($response['info']['content_type']) == 'text/html') {
        $html = $transfer->replace_html($real_url, $html);
    }

    header('Content-Type: ' . $response['info']['content_type']);
    echo $html;
} catch (\Exception $exception) {
    dd($exception);
}
exit();
