<?php

class Log
{
    //一定只在debug下记录日志。

    public static function info($content)
    {
//        if(DEBUG_MODE){
//            $db = new Mysql(DB_NAME, DB_HOST, DB_USER, DB_PASS, DB_PORT);
//            $content = is_array($content) ? json_encode($content) : $content;
//            $content = addslashes($content);
//            $sql = "insert into log (`user`,`type`,`content`) values ('','info','{$content}');";
//            $db->query($sql);
//        }
    }

    /**
     * 文件log
     * Log constructor.
     */
    public static function file()
    {

    }
}