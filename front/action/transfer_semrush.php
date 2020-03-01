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

//$url = $_GET['url'] ?? '';
    $url = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';

    $account_id = $_SESSION['account_id'];
//转发页面
    $url = trim($url, '/');
    $first_sub = explode('/', $url)[0];

//检查账号存在
    $account = Account::get_account($account_id, $_SESSION['site_id']);
    if (empty($account)) {
        die('account error | 账号错误');
    }

    if (in_array($first_sub, [
        'accounts', 'billing-admin'
    ])) {
        die('folder limit ｜ 目录访问限制');
    }

//        //查询记录数
//        $keywordLimit = UserRecord::check_user_limit($_SESSION['user_id'], $_SESSION['site_id'], 'site-explorer/overview/v2/subdomains/live');
//        if ($keywordLimit >= UserRecord::keywordsLimit) {
//            die('Reach the keywords limit | 达到关键词限制');
//        }


    $transfer = new SEMRush($account['username'], $account['password']);

    $raw_data = file_get_contents('php://input');
    if (!empty($raw_data)) {
        $post_data = $raw_data;
    } else {
        $post_data = $_POST;
    }

    $url_is_cdn = false;
    $real_url = $transfer->revoke_url($url);

    $response = $transfer->get($real_url, $post_data, $url_is_cdn);

    $html = $response['body'];
    if ($url_is_cdn) {
        header("Cache-Control: public");
        header("Pragma: cache");
        $offset = 60 * 60 * 24; // cache 1 day
        $ExpStr = "Expires: " . gmdate("D, d M Y H:i:s", time() + $offset) . " GMT";
        header($ExpStr);
        header('Content-Type: ' . $response['info']['content_type']);

        echo $html;
        die;
    }
    //记录操作
    UserRecord::record($_SESSION['user_id'], $_SESSION['site_id'], $account_id, $url);

    if (stripos(' ' . strtolower($response['info']['content_type']), 'text/html')) {

        //替换用户信息
//        $html = str_replace($account['username'], 'account_' . $account_id, $html);
    }
    $html = $transfer->trans_url($html);

    header('Content-Type: ' . $response['info']['content_type']);
    echo $html;
} catch (\Exception $exception) {
    Log::info($exception);
}
exit();