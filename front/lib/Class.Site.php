<?php

class Site
{
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

}