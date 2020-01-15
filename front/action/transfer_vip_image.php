<?php
/**
 * 都要调用的数据
 * Created by PhpStorm.
 * User: daqin
 * Date: 2018/3/14
 * Time: 18:46
 */

use FileEye\MimeMap\Extension;

if (!defined('IN_DS')) {
    die('Hacking attempt');
}

include_once 'common.php';

try {
    $user_id = $_SESSION['user_id'];
    if ($script_uri == '/image/down/') {

        $url = trim($_GET['url']) ?? '';
        $type = $_GET['type'] ?? '';

        $sql = "select * from image_source where `page_url` = '{$url}';";
        $info = $GLOBALS['db']->getFirstRow($sql);

        if ($info) {
            if ($info['user_id'] != $user_id) {
                $insert_sql = "insert ignore into image_source(`site`,`user_id`,`page_url`,`source_url`,`file_path`) value ('{$type}','{$user_id}','{$url}','{$info['source_url']}','{$info['file_path']}');";
                $GLOBALS['db']->query($insert_sql);
            }

            $extend = explode('.', $info['source_url'])[count(explode('.', $info['source_url'])) - 1];

            $ext = new Extension($extend);
            $content_type = ($ext->getDefaultType());
            header('content-type: ' . $content_type);
            header("Content-Disposition: attachment; filename=" . $info['source_url']);
            echo file_get_contents($info['file_path']);
            die();
        }

        switch ($type) {

            case "qiantu":
                $object = new QianTu();
                $image_url = $object->getImageUrl($url);
                $file_content = $object->downImage($image_url);
                break;
            case "qianku":
                break;

            default:
                die("error type");
        }
        $file_path = IMAGE_PATH . preg_replace("/[^\d\w\.]+/", '_', $image_url);
        file_put_contents($file_path, $file_content);
        //site page_url source_url file_path
        $sql = "insert into image_source(`site`,`user_id`,`page_url`,`source_url`,`file_path`) value ('{$type}','{$user_id}','{$url}','{$image_url}','{$file_path}');";
        $GLOBALS['db']->query($sql);

        $extend = explode('.', $image_url)[count(explode('.', $image_url)) - 1];
        $ext = new Extension($extend);
        $content_type = ($ext->getDefaultType());
        header('content-type: ' . $content_type);
        header("Content-Disposition: attachment; filename=" . $image_url);
        echo file_get_contents($file_content);
        die;

    } elseif ($script_uri == '/image/prev/') {
        $url = trim($_GET['url']) ?? '';

        $sql = "select * from image_source where `page_url` = '{$url}';";
        $info = $GLOBALS['db']->getFirstRow($sql);
        if ($info) {
            $extend = explode('.', $info['source_url'])[count(explode('.', $info['source_url'])) - 1];

            $ext = new Extension($extend);
            $content_type = ($ext->getDefaultType());
            header('content-type: ' . $content_type);
            echo file_get_contents($info['file_path']);
        } else {
            echo 'error';
        }

    } else {
        $site_list = [
            ['site_name' => '千图', 'type' => 'qiantu', 'example_url' => 'https://www.58pic.com/newpic/35573770.html'],
        ];

        $sql = "select * from image_source where `user_id` = {$user_id} order by id desc limit 10";
        $record_list = $GLOBALS['db']->getRows($sql);

        $tpl->assign('record_list', $record_list);
        $tpl->assign('site_list', $site_list);
        echo $tpl->render('page_image.php');
    }

} catch
(\Exception $exception) {
    Log::info($exception);
}
exit();