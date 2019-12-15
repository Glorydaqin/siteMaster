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
        $date = date("Y-m-d");
        $sql = "insert into user_record (`user_id`,`site_id`,`account_id`,`url`,`date`) values ('{$user_id}','{$site_id}','{$account_id}','{$url}','{$date}');";
        $GLOBALS['db']->query($sql);
    }

    public static function check_user_limit($user_id, $site_id, $url_pre = 'site-explorer/overview/v2/subdomains/live')
    {
        $date = date("Y-m-d");

        $sql = "select count(*) as num from user_record where user_id = {$user_id} and site_id={$site_id} and url like '{$url_pre}%' and date = '{$date}' group by id";
        $count = $GLOBALS['db']->getFirstRowColumn($sql, 'num');
        return empty($count) ? 0 : $count;
    }

    public static function check_account_limit($user_id, $limit)
    {

    }

}