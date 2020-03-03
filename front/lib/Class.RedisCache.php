<?php
class RedisCache
{
	public $c_obj   = null;
	
	function __construct($server='127.0.0.1', $port='6379') {
        $this->c_obj = new Redis();
        $this->c_obj->connect($server,$port);
	}
	
	//设置缓存
	function set_cache($key,$content,$exp_time=86400){

        $this->set($key,$content,$exp_time);

		return $content;
	}
	
	//获取缓存
	function get_cache($key){

        $res =$this->c_obj->get($key);
        if ($res === false || empty($res)) {
            return '';
        }else{
            return $res;
        }
	}
	
	//设置缓存
	private function set($key,&$content,$exp_time=86400){
        $this->c_obj->set($key,$content,$exp_time);
	}
}