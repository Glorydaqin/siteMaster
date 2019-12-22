<?php

class Site
{
    /**
     * 详情
     * @param $site_id
     * @return mixed
     */
    public static function get_info($site_id)
    {
        $sql = "select * from site where id = {$site_id}";
        $result = $GLOBALS['db']->getFirstRow($sql);
        return $result;
    }

    /**
     * 获取网站列表
     * @return mixed
     */
    public static function get_list()
    {
        $sql = "select * from site";
        $result = $GLOBALS['db']->getRows($sql);
        return $result;
    }

    /**
     * @param $user_id
     * @return mixed
     */
    public static function get_list_with_access($user_id)
    {
        $list = self::get_list();
        foreach ($list as &$item) {
            $item['is_available'] = User::get_access_with($user_id, $item['id']);
        }
        return $list;
    }
}