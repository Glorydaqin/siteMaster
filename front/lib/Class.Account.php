<?php

class Account
{
    /**
     * 获取账号
     * @param $id
     * @param int $site_id
     * @return mixed
     */
    public static function get_account($id, $site_id = 1)
    {
        //改为从redis取
        $redis = new RedisCache();
        $key = REDIS_PRE . 'account_info:' . $id;
        $info = $redis->get_cache($key);

        if (!$info)
            return [];

        $info = json_decode($info, true);
        if ($info['site_id'] != $site_id)
            return [];

        return $info;
    }

    /**
     * 获取账号
     * @param $id
     * @param int $site_id
     * @return mixed
     */
    public static function get_account_with_site($id, $site_id = 1)
    {
        $sql = "select a.*,s.name as site_name from site_account a left join site s on a.site_id=s.id where a.id = {$id} and a.site_id = {$site_id};";
        $result = $GLOBALS['db']->getFirstRow($sql);
        return $result;
    }

    /**
     * 通过id找账号
     * @param $id
     * @return mixed
     */
    public static function get_site_account($id)
    {
        //通过id 找账号
        $sql = "select * from site_account where id = {$id};";
        $result = $GLOBALS['db']->getFirstRow($sql);
        return $result;
    }

    /**
     * 获取账号列表
     * @param int $site_id
     * @param int $type 1 普通类型 2 cookie类型 3 跳转类型
     * @param boolean $is_expired 是否过期 默认否
     * @return mixed
     */
    public static function get_site_list($site_id = 0, $type = 1, $is_expired = false)
    {
        $time = date("Y-m-d H:i:s");
        if ($is_expired) {
            $expire_str = "expired_at < '{$time}'";
        } else {
            $expire_str = "expired_at > '{$time}'";
        }
        $sql = "select * from site_account where site_id = {$site_id} and deleted = 0 and type={$type} and {$expire_str} order by sort asc,id asc;";
        return $GLOBALS['db']->getRows($sql);
    }

    /**
     * 记录记录
     */
    public static function record_use()
    {

    }
}
