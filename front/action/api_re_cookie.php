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

$site_account_id = $_GET['site_account_id'] ?? '';
$site_account_id = addslashes($site_account_id);

$info = Account::get_site_account($site_account_id);
if ($info) {

    $transfer = new Ahrefs($info['username'], $info['password']);
    $cookie_file = DIR_TMP_COOKIE . $transfer->cookie_key . ".txt";
    $result = $transfer->login();
    if ($result) {
        //取cookie内容
        $cookie_content = file_get_contents($cookie_file);
        preg_match_all("/BSSESSID\t([^\s\n]+)/", $cookie_content, $match);

        if (isset($match[1][0])) {
            //内容写db
            $sql = "update site_account set cookie = '{$match[1][0]}' where id = {$site_account_id}";
            $GLOBALS['db']->query($sql);
            echo $match[1][0];
        }
    }
}
echo 0;

exit();
