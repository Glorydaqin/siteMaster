<?php

class Account
{
    /**
     * 获取账号
     * @param $id
     * @param string $site
     * @return mixed
     */
    public static function get_account($id,$site = 'ahrefs')
    {
        $sql = "select * from site_account where id = {$id} and site = '{$site}';";
        $result = $GLOBALS['db']->getFirstRow($sql);
        return $result;
    }

    /**
     * 获取账号列表
     * @param string $site
     * @return mixed
     */
    public static function get_site_list($site = 'ahrefs')
    {
        $sql = "select * from site_account where site = '{$site}';";
        $result = $GLOBALS['db']->getRows($sql);
        return $result;
    }

    /**
     * 记录记录
     */
    public static function record_use()
    {

    }
}