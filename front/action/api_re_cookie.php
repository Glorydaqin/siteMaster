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

$list = Account::get_site_list(1, 2);

foreach ($list as $info) {
    $transfer = new Ahrefs($info['username'], $info['password']);
    $cookie_file = DIR_TMP_COOKIE . $transfer->cookie_key . ".txt";
    @unlink($cookie_file);

    $result = $transfer->login();
    if ($result) {
        //取cookie内容
        $cookie_content = file_get_contents($cookie_file);
        preg_match_all("/BSSESSID\t([^\s\n]+)/", $cookie_content, $match);

        if (isset($match[1][0])) {

            //内容写db
            $sql = "update site_account set cookie = '{$match[1][0]}' where id = {$info['id']}";
            $GLOBALS['db']->query($sql);

            dump("update account: {$info['username']} ,cookie:{$match[1][0]}");
        }else{
            dump("update account: {$info['username']} ,cookie: error match");
            dump($cookie_content);
        }
    }else{
        dump("update account: {$info['username']} ,code != 200");
    }

}


exit();
