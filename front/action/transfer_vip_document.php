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

//    $account_id = $_SESSION['account_id'];
//    $url = $_POST['url'] ?? '';
//    $type = $_POST['type'] ?? '';

    echo "coming soon!";

} catch (\Exception $exception) {
    Log::info($exception);
}
exit();