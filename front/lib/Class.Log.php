<?php

class Log
{
    //一定只在debug下记录日志。

    public static function info($content)
    {
        if(DEBUG_MODE){

            $content = is_array($content) ? json_encode($content) : $content;
            $content = addslashes($content);
            $sql = "insert into log (`user`,`type`,`content`) values ('','info','{$content}');";
            $GLOBALS['db']->query($sql);
        }
    }

    /**
     * 文件log
     * Log constructor.
     */
    public static function file()
    {

    }
}