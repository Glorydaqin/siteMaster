<?php

class Ahrefs
{
    private $cdn_domain = "https://cdn.ahrefs.com/";
    private $domain = "https://www.ahrefs.com/";
    private $login_url = '';

    private $user_name = '';
    private $password = '';
    private $cookie_key = '';

    public function __construct($user_name, $password)
    {
        $this->user_name = $user_name;
        $this->password = $password;
        $this->cookie_key = "siteMaster_Ahrefs_" . $user_name;
    }

    public function curl($url, $data = [])
    {
        $ch = curl_init();

        $user_agent = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/78.0.3904.108 Safari/537.36';
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 25);   //只需要设置一个秒的数量就可以
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_REFERER, $this->domain);//这里写一个来源地址，可以写要抓的页面的首页
        curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        if (!empty($ip_info)) {
            curl_setopt($ch, CURLOPT_PROXY, $ip_info);
        }

        $cookie_file = DIR_TMP_COOKIE . $this->cookie_key . $this->user_name . ".txt";
        if (!file_exists($cookie_file)) {
            file_put_contents($cookie_file, "");
        }

        if (!empty($data)) {
            // post数据
            curl_setopt($ch, CURLOPT_POST, 1);
            // post的变量
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }
//        if (!empty($headers)) {
//            // headers
//            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
//        }
        curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);
        $content = curl_exec($ch);

        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $res = curl_getinfo($ch);
        curl_close($ch);
        $r = array();
        $r['code'] = $httpCode;
        $r['url'] = $res['url'];
        $r['body'] = $content;

        return $r;
    }

    public function get($url,$data)
    {
        if (!$this->check_is_login()) {
            $this->login($this->user_name, $this->password);
        }

        $result = $this->curl($url,$data);
        return $result;
    }


    public function check_is_login()
    {
        $result = $this->curl($this->domain . 'dashboard');
        if ($result['code'] != 200 || !stripos($result['body'], 'Account settings')) {
            return false;
        }
        return true;
    }


    public function login()
    {

    }

}