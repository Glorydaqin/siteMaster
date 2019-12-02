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


//$url = $_GET['url'] ?? '';
$url = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';

if ($url == '/' . SITE_FOLDER_PRE . "/ahrefs/" && !isset($_POST[SITE_FOLDER_PRE . 'account_id'])) {
    //选择页面
    $account_list = Account::get_site_list('ahrefs');
    $tpl->assign('account_list', $account_list);
    echo $tpl->render('ahrefs.php');
    exit();
} elseif (isset($_POST[SITE_FOLDER_PRE . 'account_id'])) {
    $_SESSION['account_id'] = $_POST[SITE_FOLDER_PRE . 'account_id'];
    temporarily_header_302('/dashboard');
} else {
    if (!isset($_SESSION['account_id'])) {
        die('choose account');
    }
    $account_id = $_SESSION['account_id'];
    //转发页面
    $url = trim($url, '/');
    $first_sub = explode('/', $url)[0];

    //检查账号存在
    $account = Account::get_account($account_id, 'ahrefs');
    if (empty($account)) {
        die('account error');
    }

    if (in_array($first_sub, [
        'account'
    ])) {
        die('folder limit');
    }

    $real_url = Ahrefs::$domain . $url;
    if (stripos($url, 'cdn_ahrefs_com') !== false) {
        $real_url = Ahrefs::$cdn_domain . substr($url, strlen('cdn_ahrefs_com'));
    }

    $Ahrefs = new Ahrefs($account['username'], $account['password']);
    $response = $Ahrefs->get($real_url, $_POST);
    $html = $response['body'];

// 替换内容
    //链接
    $html = preg_replace_callback("/href=[\'\"](.*?)[\'\"]/", function ($matches) {
        // 明确的当前域名 开头
        if (substr($matches[1], 0, 1) != '/') {
            // 不明确的域名开头
            if (stripos($matches[1], 'cdn.ahrefs.com')) {

//                if (stripos($matches[1], '.css')) {
//                    return $matches[0];
//                } else {
                return 'href="' . DOMAIN . 'cdn_ahrefs_com/' . substr($matches[1], stripos($matches[1], 'ahrefs.com') + strlen('ahrefs.com') + 1) . '"';
//                }

            } elseif (stripos($matches[1], 'ahrefs.com')) {
                return 'href="' . DOMAIN . substr($matches[1], stripos($matches[1], 'ahrefs.com') + strlen('ahrefs.com') + 1) . '"';
            } else {
                return $matches[0];
            }
        }

    }, $html);
//    //资源
//    $html = preg_replace_callback(" / src = [\'\"](.*?)[\'\"]/", function ($matches) {
//        // 明确的当前域名 开头
//        if (substr($matches[1], 0, 1) == '/') {
//            return 'src = "' . DOMAIN . 'ahrefs/?url=' . urlencode($matches[1]) . '"';
//        } else {
//            // 不明确的域名开头
//            if (stripos($matches[1], 'www . ahrefs . com')) {
//                return 'src = "' . DOMAIN . 'ahrefs/?url=' . urlencode($matches[1]) . '"';
//            } else {
//                return $matches[1];
//            }
//        }
//    }, $html);


//添加一个top bar
    $html = preg_replace_callback("/<body[^>]+?>/", function ($matches) {
        $inner_html = "<div style='position:absolute; z-index:99; top:5;  background-color:#ddd; '><a href='" . '/' . SITE_FOLDER_PRE . "/ahrefs/" . "'>HOME-选账号</a></div>";
        return "<body>" . $inner_html;
    }, $html);

    echo $html;
    exit();

}
exit();

