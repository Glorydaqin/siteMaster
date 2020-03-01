<?php

class SEMRush
{
    public static $domain = "https://www.semrush.com/";
    public static $cdn_domain = "https://cdn.semrush.com/";
    public static $cdn_dpa_domain = "https://cdn-dpa.semrush.com/";
    public static $login_page_url = 'https://www.semrush.com/login/';
    public static $login_url = 'https://www.semrush.com/sso/authorize';

    public static $user_agent = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.100 Safari/537.36';
    public static $user_agent_hash = '2feb4b063d6ac9078120a336bd1e9ed0';

    private $user_name = '';
    private $password = '';
    private $cookie_key = '';

    public function __construct($user_name, $password)
    {
        $this->user_name = $user_name;
        $this->password = $password;
        $this->cookie_key = "siteMaster_SEMRush_" . $user_name;
    }

    /**
     * 获取过滤后的header
     */
    public function get_clean_header()
    {
        $headers = getallheaders();
        if (isset($headers['Referer'])) {
            unset($headers['Referer']);
        }
        if (isset($headers['Host'])) {
            unset($headers['Host']);
        }
        if (isset($headers['Origin'])) {
            unset($headers['Origin']);
        }
        if (isset($headers['Cookie'])) {
            unset($headers['Cookie']);
        }
        if (isset($headers['Accept-Encoding'])) {
            unset($headers['Accept-Encoding']);
        }
        if (isset($headers['Accept'])) {
            unset($headers['Accept']);
        }
        $headers['User-Agent'] = self::$user_agent;
        $tmp = [];
        foreach ($headers as $header => $val) {
            if (strtolower(substr($header, 0, 2)) == 'cf-' || strtolower(substr($header, 0, 2)) == 'cdn') {
                continue;
            }

            $tmp[] = $header . ': ' . $val;
        }
        return $tmp;
    }

    public function curl($url, $data = [], $header = [])
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 25);   //只需要设置一个秒的数量就可以
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_REFERER, self::$domain);//这里写一个来源地址，可以写要抓的页面的首页
        curl_setopt($ch, CURLOPT_USERAGENT, self::$user_agent);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);

        $cookie_file = DIR_TMP_COOKIE . $this->cookie_key . ".txt";
        if (!file_exists($cookie_file)) {
            file_put_contents($cookie_file, "");
        }
        if (!empty($header)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        }
        if (!empty($data)) {
            // post数据
            curl_setopt($ch, CURLOPT_POST, 1);
            // post的变量
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            curl_setopt($ch, CURLOPT_POST, 1);
        }
        $clean_header = $this->get_clean_header();
        curl_setopt($ch, CURLOPT_HTTPHEADER, $clean_header);
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
        $r['info'] = $res;

        //写详细请求记录
        $log_r = $r;
        $log_r['body'] = utf8_encode($log_r['body']);
        $log_content = [
            'cookie_file' => $cookie_file,
            'cookie_content' => file_get_contents($cookie_file),
            'request' => [
                'url' => $url,
                'header' => $clean_header,
                'data' => $data
            ],
            'response' => $log_r
        ];
        Log::info($log_content);

        return $r;
    }

    public function get($url, $data = [], $is_cdn = false)
    {
        //检查是否有缓存，有则调用缓存
        $cache = new Cache();
//        if ($is_cdn) {
//            $cache_file = $cache->get_cache($url);
//            if ($cache_file) {
//                $cache_file = json_decode($cache_file, true);
//                return $cache_file;
//            }
//        }

        if (!$this->check_is_login()) {
            $this->login();
        }

        $result = $this->curl($url, $data);
        if (stripos($result['url'], '/login/') || $result['url'] == self::$domain) {
            //跳转到登陆的说明未登陆
            $this->login();
            $result = $this->curl($url, $data);
        }
        if ($is_cdn) {
            $result_str = json_encode($result);
            $cache->set_cache($url, $result_str);
        }
        return $result;
    }


    public function check_is_login()
    {
        //用cookie 中有效时间判断 可能不太准确，但是效率高
        $cookie_file = DIR_TMP_COOKIE . $this->cookie_key . ".txt";
        if (!file_exists($cookie_file)) {
            file_put_contents($cookie_file, '');
        }
        if (empty(file_get_contents($cookie_file))) {
            return false;
        }
//        preg_match_all("/TRUE	(\d+?)	BSSESSID/", file_get_contents($cookie_file), $match_time);
//        $cookie_time = isset($match_time[1][0]) ? $match_time[1][0] : 0;
//        if ($cookie_time < time()) {
//            return false;
//        }
//        return true;

//        $result = $this->curl(self::$domain . 'dashboard');
//        if ($result['code'] != 200 || !stripos($result['body'], 'Account settings')) {
//            return false;
//        }
        return true;
    }


    public function login()
    {
        //x-newrelic-id: VQEOWV5VDRAHUVRTBwkAUg==
        //x-xsrf-token: ZYZZNbdiphInrIXHsNt0ZPH3Jp6eXG93IZ4gckCD

//        $login_page = self::curl(self::$login_page_url);
//        $content = $login_page['body'];
//        preg_match_all("/xpid\:\"([^\"]+)\"/", $content, $match_xid);
//        preg_match_all("/\"csrfToken\":\s+\"([^\"]+)\"/", $content, $match_xtoken);
//        $xid = $match_xid[1][0] ?? '';
//        $xtoken = $match_xtoken[1][0] ?? '';
//
//        $header = [
//            'x-newrelic-id:' . $xid,
//            'x-xsrf-token:' . $xtoken,
//            'Content-Type:' . 'application/x-www-form-urlencoded'
//        ];
//        d($header);

        //email:692860800@qq.com
        //locale:en
        //source:semrush
        //g-recaptcha-response:
        //user-agent-hash:2feb4b063d6ac9078120a336bd1e9ed0
        //password:daqing
        $data = [
            'user-agent-hash' => self::$user_agent_hash,
            'source' => 'semrush',
            'email' => $this->user_name,
            'password' => $this->password,
            'g-recaptcha-response' => '',
            'locale' => 'en',
        ];

        $result = $this->curl(self::$login_url, $data);

        if ($result['code'] == 200) {
            return true;
        }
        return false;
    }

    /**
     * 域名恢复
     * @param $url
     * @return string
     */
    public function revoke_url($url)
    {
        if (stripos(' ' . $url, "cdn_semrush/")) {
            $real_url = self::$cdn_domain . substr($url, strlen('cdn_semrush/'));
        } elseif (stripos(' ' . $url, "cdn_dpa_semrush/")) {
            $real_url = self::$cdn_dpa_domain . substr($url, strlen('cdn_semrush/'));
        } else {
            $real_url = self::$domain . $url;
        }

        return $real_url;
    }

    /**
     * 域名替换
     * @param $html
     * @return string|string[]|null
     */
    public function trans_url($html)
    {
        $html = preg_replace_callback("/href=[\'\"](.*?)[\'\"]/", function ($matches) {
            // 明确的当前域名 开头
            if (substr($matches[1], 0, 1) != '/') {
                $matches[1] = str_replace("https://", '', $matches[1]);
                $matches[1] = str_replace("http://", '', $matches[1]);
                // 不明确的域名开头
                if (substr($matches[1], 0, strlen('cdn.semrush.com')) == "cdn.semrush.com") {
                    return 'href="' . PROTOCOL . DOMAIN_SEMRUSH . '/cdn_semrush/' . substr($matches[1], stripos($matches[1], 'semrush.com') + strlen('semrush.com') + 1) . '"';
                } elseif (substr($matches[1], 0, strlen('cdn-dpa.semrush.com')) == "cdn-dpa.semrush.com") {
                    return 'href="' . PROTOCOL . DOMAIN_SEMRUSH . '/cdn_dpa_semrush/' . substr($matches[1], stripos($matches[1], 'semrush.com') + strlen('semrush.com') + 1) . '"';
                } elseif (substr($matches[1], 0, strlen('semrush.com')) == "semrush.com") {
                    return 'href="' . PROTOCOL . DOMAIN_SEMRUSH . '/' . substr($matches[1], stripos($matches[1], 'semrush.com') + strlen('semrush.com') + 1) . '"';
                }
            }
            return $matches[0];
        }, $html);

        $html = preg_replace_callback("/src=[\'\"](.*?)[\'\"]/", function ($matches) {
            // 明确的当前域名 开头
            if (substr($matches[1], 0, 1) != '/') {
                $matches[1] = str_replace("https://", '', $matches[1]);
                $matches[1] = str_replace("http://", '', $matches[1]);
                // 不明确的域名开头
                if (substr($matches[1], 0, strlen('cdn.semrush.com')) == "cdn.semrush.com") {
                    return 'src="' . PROTOCOL . DOMAIN_SEMRUSH . '/cdn_semrush/' . substr($matches[1], stripos($matches[1], 'semrush.com') + strlen('semrush.com') + 1) . '"';
                } elseif (substr($matches[1], 0, strlen('cdn-dpa.semrush.com')) == "cdn-dpa.semrush.com") {
                    return 'src="' . PROTOCOL . DOMAIN_SEMRUSH . '/cdn_dpa_semrush/' . substr($matches[1], stripos($matches[1], 'semrush.com') + strlen('semrush.com') + 1) . '"';
                } elseif (substr($matches[1], 0, strlen('semrush.com')) == "semrush.com") {
                    return 'src="' . PROTOCOL . DOMAIN_SEMRUSH . '/' . substr($matches[1], stripos($matches[1], 'semrush.com') + strlen('semrush.com') + 1) . '"';
                }
            }
            return $matches[0];
        }, $html);

        return $html;
    }

}