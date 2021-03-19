<?php

class Mangools
{
    public static $domain = "https://app.kwfinder.com/";
    public static $mangools_domain = "https://mangools.com/";
    public static $mangools_api_domain = "https://api2.mangools.com/";

//    public static $login_url = 'https://mangools.com/users/sign_in';
    public static $cookie_url = 'https://app.kwfinder.com/?sso_ticket=420629489d99646c3c7332a1f08844b1930bf308e6b38f045765713b84aa469e&login_token=rn8CE9VPGmZMyzhK_fyy';


    public static $user_agent = 'Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/47.0.2526.106 Safari/537.36';

    private $user_name = '';
    private $password = '';
    public $cookie_key = '';
    public $in_domain = ''; // 用户访问的域名

//    public $ticket_key = '';

    public function __construct($user_name, $password, $in_domain)
    {
        $this->user_name = $user_name;
        $this->password = $password;
        $this->in_domain = $in_domain;

        if ($in_domain == DOMAIN_SITEPROFILER) {
            self::$domain = 'https://app.siteprofiler.com/';
        } elseif ($in_domain == DOMAIN_SERPWATCHER) {
            self::$domain = 'https://app.serpwatcher.com/';
        } elseif ($in_domain == DOMAIN_SERPCHECKER) {
            self::$domain = 'https://app.serpchecker.com/';
        } elseif ($in_domain == DOMAIN_LINKMINER) {
            self::$domain = 'https://app.linkminer.com/';
        } else {
            self::$domain = 'https://app.kwfinder.com/';
        }

        $this->cookie_key = "siteMaster_mangools_" . $user_name;
    }

    /**
     * 获取过滤后的header
     */
    public function get_clean_header()
    {
        $headers = getallheaders();
        if (isset($headers['Referer'])) {
            unset($headers['Referer']);
//            $headers['Referer'] = str_replace(DOMAIN, self::$domain, $headers['Referer']);
        }
        if (isset($headers['Host'])) {
            unset($headers['Host']);
//            $headers['Host'] = str_replace(DOMAIN, self::$domain, $headers['Host']);
        }
        if (isset($headers['Origin'])) {
            unset($headers['Origin']);
//            $headers['Origin'] = str_replace(DOMAIN, self::$domain, $headers['Origin']);
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
            $tmp[] = $header . ': ' . $val;
        }
//sec-fetch-dest: document
// sec-fetch-mode: navigate
// sec-fetch-site: same-origin
// sec-fetch-user: ?1
// upgrade-insecure-requests: 1
        $tmp[] = 'sec-fetch-dest: document';
        $tmp[] = 'sec-fetch-mode: navigate';
        $tmp[] = 'sec-fetch-site: same-origin';
        $tmp[] = 'sec-fetch-user: ?1';
        $tmp[] = 'upgrade-insecure-requests: 1';
//        $tmp[] = 'referer: https://mangools.com/users/sign_in?ref=msg-app-kw&redirect=https%3A%2F%2Fapp.kwfinder.com';

        return $tmp;
    }

    public function curl($url, $data = [])
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 25);   //只需要设置一个秒的数量就可以
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_REFERER, self::$mangools_domain);
        curl_setopt($ch, CURLOPT_USERAGENT, self::$user_agent);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);

        $cookie_file = DIR_TMP_COOKIE . $this->cookie_key . ".txt";
        if (!file_exists($cookie_file)) {
            file_put_contents($cookie_file, "");
        }

        if (!empty($data)) {
            // post数据
            curl_setopt($ch, CURLOPT_POST, 1);
            // post的变量
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }
        $clean_header = $this->get_clean_header();
        curl_setopt($ch, CURLOPT_HTTPHEADER, $clean_header);
        curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);
        $content = curl_exec($ch);

        $res = curl_getinfo($ch);
        $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);

        curl_close($ch);
        $r = array();
        $r['code'] = $res['http_code'];
        $r['url'] = $res['url'];
        $r['body'] = $content;
        $r['info'] = $res;
        // 根据头大小去获取头信息内容
        $r['response_header'] = substr($content, 0, $headerSize);

        //写详细请求记录
//         $log_r = $r;
//         $log_r['body'] = utf8_encode($log_r['body']);
//         $log_content = [
//             'cookie_file' => $cookie_file,
//             'cookie_content' => file_get_contents($cookie_file),
//             'request' => [
//                 'url' => $url,
//                 'header' => $clean_header,
//                 'data' => $data
//             ],
//             'response' => $log_r
//         ];
// //        dd($log_content);
//         Log::info($log_content);

        return $r;
    }

    public function get($url, $data = [], $is_cdn = false)
    {
        //检查是否有缓存，有则调用缓存
        $cache = new Cache();

        if ($is_cdn) {
            $cache_file = $cache->get_cache($url);
            if ($cache_file) {
                $cache_file = json_decode($cache_file, true);
                return $cache_file;
            }
        }

        if (!$this->check_is_login()) {
            return $this->login();
        }

        $result = $this->curl($url, $data);

        if ($is_cdn && !empty($result['body'])) {
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

        return true;
    }


    public function login()
    {
        $result = $this->curl(self::$cookie_url);
        return $result;
//        dd($result);

////        if ($result['code'] == 200 && stripos($result['url'], '/apps?sso_ticket=')) {
//        if ($result['code'] == 200) {
//            //记录当前这个sso ticket
////            preg_match_all("/sso\_ticket=([\d\w]+)/", $result['url'], $match_ticket);
////            $ticket = $match_ticket[1][0] ?? '';
////            (new Cache())->set_cache($this->cookie_key, $ticket);
////            echo $result['body'];exit();
//            return true;
//        }
//        return false;
    }

    /**
     * 域名替换
     */
    public function replace_url()
    {

    }

    /**
     * 域名恢复
     * @param $url
     * @return string|string[]
     */
    public function revoke_url($url)
    {
        $real_url = self::$domain . $url;
        if (stripos($url, 'mangools_domain/') !== false) {
            $real_url = str_replace("mangools_domain/", self::$mangools_domain, $url);
        } elseif (stripos($url, 'mangools_api_domain/') !== false) {
            $real_url = str_replace("mangools_api_domain/", self::$mangools_api_domain, $url);
        }
//        if (stripos($real_url, 'users/current_user') !== false) {
//            $real_url .= '=' . (new Cache())->get_cache($this->cookie_key);
//        }
        return $real_url;
    }

    public function replace_main_js($url, $html)
    {
        if (preg_match("/app\.[a-z\d]+\.js/", $url)) {

            //替换工具相关域名
            $html = str_replace("https://app.kwfinder.com", PROTOCOL . DOMAIN_KWFINDER, $html);
            $html = str_replace("https://app.siteprofiler.com", PROTOCOL . DOMAIN_SITEPROFILER, $html);
            $html = str_replace("https://app.linkminer.com", PROTOCOL . DOMAIN_LINKMINER, $html);
            $html = str_replace("https://app.serpwatcher.com", PROTOCOL . DOMAIN_SERPWATCHER, $html);
            $html = str_replace("https://app.serpchecker.com", PROTOCOL . DOMAIN_SERPCHECKER, $html);
            //替换    https://api2.mangools.com => /mangools_api_domain
            $html = str_replace('https://api2.mangools.com', PROTOCOL . DOMAIN_KWFINDER . '/mangools_api_domain', $html);
            //替换 https://mangools.com => /mangools_domain
            $html = str_replace('https://mangools.com', PROTOCOL . DOMAIN_KWFINDER . '/mangools_domain', $html);

            if ($this->in_domain == DOMAIN_SITEPROFILER) {
                //替换    app.siteprofiler.com =>
                $html = str_replace('app.siteprofiler.com', DOMAIN_SITEPROFILER, $html);
            } elseif ($this->in_domain == DOMAIN_SERPWATCHER) {
                //替换    app.serpwatcher.com =>
                $html = str_replace('app.serpwatcher.com', DOMAIN_SERPWATCHER, $html);
            } elseif ($this->in_domain == DOMAIN_SERPCHECKER) {
                //替换    app.serpchecker.com =>
                $html = str_replace('app.serpchecker.com', DOMAIN_SERPCHECKER, $html);
            } elseif ($this->in_domain == DOMAIN_LINKMINER) {
                //替换    app.linkminer.com =>
                $html = str_replace('app.linkminer.com', DOMAIN_LINKMINER, $html);
            }
        }
        return $html;
    }

    public function replace_html($url, $html)
    {

    }
}