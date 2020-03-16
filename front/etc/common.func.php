<?php
if (!defined('IN_DS')) {
    die('Hacking attempt');
}


function getip()
{
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
    return ($ip);
}

function temporarily_header_401()
{
    header('HTTP/1.1 401 Moved Permanently');
    header('Cache-Control: no-cache');
    exit;
}

function temporarily_header_302($url = '')
{
    $url = trim($url);
    header('HTTP/1.1 302 Moved Permanently');
    header('Cache-Control: no-cache');
    header('Location: ' . $url);
    exit;
}

function page_jump($url = '', $message = '')
{
    $alert = '';
    if (!empty($message)) {
        $alert = "alert('{$message}');";
    }
    echo "<SCRIPT language=JavaScript>{$alert}location.href='{$url}';</SCRIPT>";
    die();
}

if (!function_exists('getallheaders')) {
    function getallheaders()
    {
        $headers = [];
        foreach ($_SERVER as $name => $value) {
            if (substr($name, 0, 5) == 'HTTP_') {
                $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
            }
        }
        return $headers;
    }
}


// usage: echo charCodeAt("This is a string", 7)
function charCodeAt($str, $i)
{
    return ord(substr($str, $i, 1));
}

// usage: echo fromCharCode(72, 69, 76, 76, 79)
function fromCharCode()
{
    return array_reduce(func_get_args(), function ($a, $b) {
        $a .= chr($b);
        return $a;
    });
}

/**
 * 解密
 * @param $str
 * @return mixed|string
 */
function unCompileCode($str)
{
    $str = base64_decode($str);
    for (
        $t = fromCharCode(
            charCodeAt($str, 0) - strlen($str)
        ), $o = 1;
        $o < strlen($str);
        $o++
    ) {
        $t .= fromCharCode(
            charCodeAt($str, $o) - charCodeAt($t, $o - 1)
        );
    }
    return $t;
}

/**
 * 加密
 * @param $str
 * @return mixed|string
 */
function compileCode($str)
{
    $tmp = fromCharCode(charCodeAt($str, 0) + strlen($str));
    for ($i = 1; $i < strlen($str); $i++) {
        $tmp .= fromCharCode(
            charCodeAt($str, $i) + charCodeAt($str, $i - 1)
        );
    }
    $tmp = base64_encode($tmp);
    return $tmp;
}


function curl($url, $data = [])
{
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_TIMEOUT, 25);   //只需要设置一个秒的数量就可以
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_REFERER, '');//这里写一个来源地址，可以写要抓的页面的首页
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/78.0.3904.108 Safari/537.36');
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);

    if (!empty($data)) {
        // post数据
        curl_setopt($ch, CURLOPT_POST, 1);
        // post的变量
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    }
//
//    $clean_header = $this->get_clean_header();
//    curl_setopt($ch, CURLOPT_HTTPHEADER, $clean_header);
    $content = curl_exec($ch);

    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $res = curl_getinfo($ch);
    curl_close($ch);
    $r = array();
    $r['code'] = $httpCode;
    $r['url'] = $res['url'];
    $r['body'] = $content;
    $r['info'] = $res;

    return $r;
}


/**
 * 设置选择的账号和网址cookie
 * @param $site_id
 * @param $account_id
 * @param string $domain
 */
function set_choose_session($site_id, $account_id, $key = '')
{
    $_SESSION[$key] = ['account_id' => $account_id, 'site_id' => $site_id];
}

/*
 判断当前的运行环境是否是cli模式
 */
function is_cli()
{
    return preg_match("/cli/i", php_sapi_name()) ? true : false;
}
