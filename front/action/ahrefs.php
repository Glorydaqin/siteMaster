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

if (!empty($_GET['account_id'])) {
    $_SESSION['account_id'] = $_GET['account_id'];
} elseif (empty($_SESSION['account_id'])) {
    die('choose account');
}

$account_id = $_SESSION['account_id'];
$url = $_GET['url'];
$first_sub = explode('/', $url)[0];

//检查账号存在
$account = Account::get_account($account_id, 'ahrefs');
if (empty($account)) {
    die('account error');
}

if (!in_array($first_sub, [
    'dashboard',
    'explore'
])) {
    die('folder limit');
}
$real_url = Ahrefs::$domain . $url;
$Ahrefs = new Ahrefs($account['username'], $account['password']);
$response = $Ahrefs->get($url, $_POST);
$html = $response['body'];

// 替换内容
//链接
$html = preg_replace_callback("/href=[\'\"](.*?)[\'\"]/", function ($matches) {
    // 明确的当前域名 开头
    if (substr($matches[1],0,1) == '/') {
        return 'href="' . DOMAIN . 'ahrefs/?url=' . urlencode($matches[1]) . '"';
    }else{
        // 不明确的域名开头
        if(stripos($matches[1],'www.ahrefs.com')){
            return 'href="' . DOMAIN . 'ahrefs/?url=' . urlencode($matches[1]) . '"';
        }else{
            return $matches[1];
        }
    }

}, $html);
//资源
$html = preg_replace_callback("/src=[\'\"](.*?)[\'\"]/", function ($matches) {
    // 明确的当前域名 开头
    if (substr($matches[1],0,1) == '/') {
        return 'src="' . DOMAIN . 'ahrefs/?url=' . urlencode($matches[1]) . '"';
    }else{
        // 不明确的域名开头
        if(stripos($matches[1],'www.ahrefs.com')){
            return 'src="' . DOMAIN . 'ahrefs/?url=' . urlencode($matches[1]) . '"';
        }else{
            return $matches[1];
        }
    }
}, $html);


//添加一个top bar
$body_index =


echo $html;
exit();