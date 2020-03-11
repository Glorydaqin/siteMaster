<?php

class User
{
    public static function check_user($username, $password)
    {
        $check_sql = "select * from user where username = '{$username}' and password = '{$password}'";
        $row = $GLOBALS['db']->getFirstRow($check_sql);
        return $row;
    }

    /**
     * 保存用户信息到redis
     * @param $user_id
     * @param $info
     */
    public static function cache_user_info($user_id, $info)
    {
        $key = REDIS_PRE . "user_info:" . $user_id;
        $redis = new RedisCache();
        $redis->set_cache($key, json_encode($info, true), 86400);
    }

    /**
     * 修改 last plugin id
     * @param $user_id
     * @param $plugin_id
     * @return mixed
     */
    public static function set_last_plugin_id($user_id, $plugin_id)
    {
        $check_sql = "update user set last_plugin_id = '{$plugin_id}' where id = {$user_id}";
        $row = $GLOBALS['db']->query($check_sql);
        return $row;
    }

    /**
     * @param $id
     * @return mixed
     */
    public static function get_info($id)
    {
        $sql = "select * from user where id = {$id}";
        $result = $GLOBALS['db']->getFirstRow($sql);
        return $result;
    }

    /**
     * @param $user_id
     * @return array|mixed
     */
    public static function get_access($user_id)
    {
        $sql = "select expired_json from `user` where id = {$user_id}";
        $result = $GLOBALS['db']->getFirstRow($sql);

        $data = [];
        if (isset($result['expired_json']) && !empty($result['expired_json'])) {
            $data = json_decode($result['expired_json'], true);
        }
        return $data;
    }

    /**
     * 当前时间这个用户有这个网站权限
     * @param $user_id
     * @param $site_id
     * @return bool
     */
    public static function get_access_with($user_id, $site_id)
    {
        $now = time();
        $access = self::get_access($user_id);

        $keyval = array_combine(array_column($access, 'site_id'), array_column($access, 'expired_at'));
        if (isset($keyval[$site_id])) {
            if ($now < strtotime($keyval[$site_id])) {
                return $keyval[$site_id];
            }
        }
        return false;
    }

    /**
     * 获取上次登陆session id
     *
     * @param $user_id
     * @return string
     */
    public static function get_last_session_id($user_id)
    {
        $key = REDIS_PRE . "last_session_id:" . $user_id;
        $redis = new RedisCache();
        $info = $redis->get_cache($key);
        return $info;

        /*
                $info = self::get_info($user_id);
                return $info['last_session_id'] ?? '';*/
    }

    /**
     * 设置上次登陆session id
     * @param $user_id
     * @param string $session_id
     * @return string
     */
    public static function set_last_session_id($user_id, $session_id = '')
    {
        // last session 更改为redis
        $key = REDIS_PRE . "last_session_id:" . $user_id;
        $redis = new RedisCache();
        $redis->set_cache($key, $session_id);

//        $sql = "update user set last_session_id = '{$session_id}' where id = {$user_id}";
//        $result = $GLOBALS['db']->query($sql);
//
//        return $result;
    }
}