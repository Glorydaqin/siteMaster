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

$site_id = $_POST['site_id'] ?? 0;
$site_id = intval($site_id);
$last_plugin_id = $_POST['last_plugin_id'] ?? 0;
$last_plugin_id = addslashes($last_plugin_id);

$data = [
    'code' => 200,
    'data' => [],
    'message' => 'ok'
];
$row = User::get_user_by_plugin_id($last_plugin_id);

if (!empty($row)) {
    if (strtotime($row['expired_at']) >= time()) {

        $access_result = User::get_access_with($row['id'], $site_id);
        if (!$access_result) {
            // 账号过期
            $data['code'] = 4001;
            $data['message'] = '账号到期';

            echo json_encode($data);
            exit();
        }

        //访问量限制
        $site_info = Site::get_info($site_id);
        //[
        //    ['alias' => '搜索量', 'urlContain' => 'v4/daProjects', 'maxHit' => 10, 'leftHit' => 10]
        //]
        $limit_map = json_decode($site_info['limit_map']);
        $day_before = date("Y-m-d H:i:s", strtotime('-1 day'));
        $visitList = UserRecord::getList($row['id'], $site_id, $day_before);
        foreach ($limit_map as $key => $map) {
            foreach ($visitList as $visit) {
                if (strripos($visit['url'], $map['urlContain']) > 0) {
                    $limit_map[$key]['leftHit']--;
                }
            }
        }

        $data['data'] = $limit_map;

    } else {
        // 账号过期
        $data['code'] = 4001;
        $data['message'] = '账号到期';
    }

} else {
    $data['code'] = 4002;
    $data['message'] = '账号在其他设备登录';
}

echo json_encode($data);
exit();
