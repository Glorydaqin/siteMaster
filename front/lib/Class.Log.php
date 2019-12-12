<?php

class Log
{

    public static function info($content)
    {
        $content = is_array($content) ? json_encode($content) : $content;
        $content = addslashes($content);
        $sql = "insert into log (`user`,`type`,`content`) values ('','info','{$content}');";
        $GLOBALS['db']->query($sql);
    }

    /**
     * 文件log
     * Log constructor.
     */
    public static function log()
    {

    }
}