<?php

class RedisCache
{
    private static $redis;
    private static $server;
    private static $port;

    function __construct($server = REDIS_HOST, $port = REDIS_PORT)
    {
        self::$server = $server;
        self::$port = $port;
        return self::connect();
    }

    public static function connect()
    {
        if (!isset(self::$redis)) {
            $redis = new \Redis();
            $redis->connect(self::$server, self::$port);
            return self::$redis = $redis;
        }
        return self::$redis;
    }

    //设置缓存
    function set_cache($key, $content, $exp_time = 86400)
    {
        self::$redis->set($key, $content, $exp_time);

        return $content;
    }

    //获取缓存
    function get_cache($key)
    {
        $res = self::$redis->get($key);
        if ($res === false || empty($res)) {
            return '';
        } else {
            return $res;
        }
    }

    public function __call($name, $arguments)
    {
        $redis = self::connect();
        return call_user_func_array([$redis, $name], $arguments);
    }

    public static function __callStatic($name, $arguments)
    {
        $redis = self::connect();
        return call_user_func_array([$redis, $name], $arguments);
    }

}