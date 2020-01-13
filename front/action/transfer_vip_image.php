<?php
/**
 * 都要调用的数据
 * Created by PhpStorm.
 * User: daqin
 * Date: 2018/3/14
 * Time: 18:46
 */

use \lib\image\QianTu;

if (!defined('IN_DS')) {
    die('Hacking attempt');
}

include_once 'common.php';

try {

    $account_id = $_SESSION['account_id'];
    $url = $_POST['url'] ?? '';
    $type = $_POST['type'] ?? '';

    switch ($type) {

        case "qiantu":
            $object = new QianTu();
            $image_url = $object->getImageUrl($url);
            break;
        case "qianku":
            break;

        default:
            die("error type");
    }
    //扣点

    //返回需要的图片链接


} catch (\Exception $exception) {
    Log::info($exception);
}
exit();