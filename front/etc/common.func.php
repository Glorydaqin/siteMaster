<?php
if (!defined('IN_DS')) {
	die('Hacking attempt');
}


function getip(){
    if (getenv("HTTP_CLIENT_IP") && strcasecmp(getenv("HTTP_CLIENT_IP"), "unknown"))
        $ip = getenv("HTTP_CLIENT_IP");
    else if (getenv("HTTP_X_FORWARDED_FOR") && strcasecmp(getenv("HTTP_X_FORWARDED_FOR"), "unknown"))
        $ip = getenv("HTTP_X_FORWARDED_FOR");
    else if (getenv("REMOTE_ADDR") && strcasecmp(getenv("REMOTE_ADDR"), "unknown"))
        $ip = getenv("REMOTE_ADDR");
    else if (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], "unknown"))
        $ip = $_SERVER['REMOTE_ADDR'];
    else
        $ip = "unknown";
    return($ip);
}

function temporarily_header_401() {
	header('HTTP/1.1 401 Moved Permanently');
	header('Cache-Control: no-cache');
	exit;
}

function temporarily_header_302($url = '') {
	$url = trim($url);
	header('HTTP/1.1 302 Moved Permanently');
	header('Cache-Control: no-cache');
	header('Location: ' . $url);
	exit;
}
