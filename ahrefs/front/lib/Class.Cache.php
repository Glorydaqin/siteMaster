<?php

class Cache
{
    //文件缓存
    private $basePth;

    function __construct($basePth = CACHE_PATH)
    {
        $this->basePth = $basePth;
    }

    //设置缓存
    function set_cache($key, $content)
    {
        if (DEBUG_MODE) {
            return $content;
        }

        $this->set($key, $content);
        return $content;
    }

    //获取缓存
    function get_cache($key, $time = 864000)
    {
        $path = $this->getPath($key);

        if (!is_file($path)) return false;

        if ($time && filemtime($path) + $time < time()) {  //过期删除
            unlink($path);
            return false;
        }

        return file_get_contents($path);
    }

    //设置缓存
    function set($key, &$content)
    {
        $path = $this->getPath($key);
        return file_put_contents($path, $content);
    }


    private function getPath($key)
    {
        $key = md5($key);
        $keyDir = $this->basePth . DIRECTORY_SEPARATOR . substr($key, 0, 1) . DIRECTORY_SEPARATOR . substr($key, 1, 1) . DIRECTORY_SEPARATOR;
        $this->mkdirs($keyDir);
        return $keyDir . $key;
    }
    //创造指定的多级路径
    //参数 要创建的路径 文件夹的权限
    //返回值 布尔值
    private function mkdirs($dir, $mode = 0777)
    {
        if (is_dir($dir) || @mkdir($dir, $mode)) return true;
        if (!$this->mkdirs(dirname($dir), $mode)) return false;
        return @mkdir($dir, $mode);
    }
}

?>