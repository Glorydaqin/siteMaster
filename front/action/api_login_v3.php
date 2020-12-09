<?php
/**
 * 插件登录接口
 * Created by PhpStorm.
 * User: daqin
 * Date: 2018/3/14
 * Time: 18:46
 */
if (!defined('IN_DS')) {
    die('Hacking attempt');
}

//登陆
$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';
$username = addslashes($username);
$password = addslashes($password);
$site_id = addslashes($_POST['site_id']) ?? 1;
$v = $_POST['v'] ?? 1;

$data = [
    'code' => 200,
    'data' => []
];
//检查 site 版本配置
$site_info = Site::get_info($site_id);
if (!$site_info) {
    $data['code'] = 4001;
    $data['message'] = '参数错误';
    echo json_encode($data);
    exit();
}

if ($site_info['name'] == 'ahrefs') {
    $last_version = 3.4;
} elseif ($site_info['name'] == 'mangools') {
    $last_version = 3.4;
} else {
    $last_version = 1;
}

if ($v < $last_version) {
    $data['code'] = 4001;
    $data['message'] = '版本已升级,请卸载当前版本,登陆(https://vtool.club/download.html)重新下载安装最新版本';

    echo json_encode($data);
    exit();
}
$row = User::check_user($username, $password);
if ($row && strtotime($row['expired_at']) >= time()) {
    $last_plugin_id = time() . rand(1000, 9999);
    User::set_last_plugin_id($row['id'], $last_plugin_id);

    $site_expired_at = User::get_access_with($row['id'], $site_id);
    $data['data']['last_plugin_id'] = $last_plugin_id;
    $data['data']['site_expired_at'] = $site_expired_at;
    $data['data']['left_day'] = round((strtotime($site_expired_at) - time()) / 86400, 1);

    if ($site_expired_at && strtotime($site_expired_at) > time()) {
        $data['data']['is_active'] = true;

        //取账号
        $account_list = Account::get_site_list($site_id, $site_info['name'] === 'mangools' ? 3 : 2);

        $accounts = [];
        foreach ($account_list as $key => $item) {
            //加密后的cookie
//            if ($site_info['name'] == 'mangools') {
//                $accounts[] = ['encodeToken' => $item['cookie']]; // mangools cookie加密后解密错误,暂不加密
//            } else {
            $accounts[] = ['encodeToken' => compileCode($item['cookie'])];
//            }
        }

        $data['data']['account_list'] = array_reverse($accounts);

    } else {
        $data['data']['is_active'] = false;
        $data['code'] = 4003;
        $data['message'] = $site_info['name'] . '权限已到期';
    }

} elseif ($row && strtotime($row['expired_at']) < time()) {
    $data['code'] = 4001;
    $data['message'] = '账号已到期';
} else {
    $data['code'] = 4002;
    $data['message'] = '账号或密码错误';
}

echo json_encode($data);
exit();
