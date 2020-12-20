<?php

class UserRecord
{
    const keywordsLimit = 10;

    /**
     * 记录操作的链接
     * @param $user_id
     * @param $site_id
     * @param $account_id
     * @param $url
     */
    public static function record($user_id, $site_id, $account_id, $url)
    {
        //redis 记录 以用户 日期 为维度的列表 的 有序集合
//        $date = date("Y-m-d");
//        $redis = new RedisCache();
//        $key = REDIS_PRE.'user_record:'.$user_id.':'

//        $sql = "insert into user_record (`user_id`,`site_id`,`account_id`,`url`,`date`) values ('{$user_id}','{$site_id}','{$account_id}','{$url}','{$date}');";
//        $GLOBALS['db']->query($sql);
    }

    public static function check_user_limit($user_id, $site_id, $url_pre = 'site-explorer/overview/v2/subdomains/live')
    {
//        $date = date("Y-m-d");
//
//        $sql = "select count(*) as num from user_record where user_id = {$user_id} and site_id={$site_id} and url like '{$url_pre}%' and date = '{$date}' group by id";
//        $count = $GLOBALS['db']->getFirstRowColumn($sql, 'num');
//        return empty($count) ? 0 : $count;
    }

    public static function check_account_limit($user_id, $limit)
    {

    }

    /**
     * 记录操作的链接
     * @param $user_id
     * @param $site_id
     * @param $account_id
     * @param $url
     */
    public static function save($user_id, $site_id, $account_id, $url)
    {
        $sql = "insert into user_record (`user_id`,`site_id`,`account_id`,`url`) values ('{$user_id}','{$site_id}','{$account_id}','{$url}');";
        $GLOBALS['db']->query($sql);
    }

    /**
     * @param $user_id
     * @param $site_id
     * @param $time_before
     * @return mixed
     */
    public static function getList($user_id, $site_id, $time_before)
    {
        $sql = "select url from user_record where user_id = {$user_id} and site_id = {$site_id} and created_at >= '{$time_before}';";
        return $GLOBALS['db']->getRows($sql);
    }
}
