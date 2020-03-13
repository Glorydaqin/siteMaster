<?php

class SpyFu
{
    public static $cdn_domain = "https://cdn1.spyfu.com/";
    public static $domain = "https://www.spyfu.com/";
    public static $login_url = 'https://www.spyfu.com/auth/login';

    public $user_agent = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/78.0.3904.108 Safari/537.36';

    private $user_name = '';
    private $password = '';
    private $cookie_key = '';
    public $buffer = ''; //your download buffer goes here.

    /**
     * Ahrefs constructor.
     * @param $user_name
     * @param $password
     */
    public function __construct($user_name, $password)
    {
        $this->user_name = $user_name;
        $this->password = $password;
        $this->cookie_key = "siteMaster_" . __CLASS__ . "_" . $user_name;
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
        $headers['User-Agent'] = $this->user_agent;
        $tmp = [];
        foreach ($headers as $header => $val) {
            $tmp[] = $header . ': ' . $val;
        }
        return $tmp;
    }

    public function curl($url, $data = [])
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 25);   //只需要设置一个秒的数量就可以
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_REFERER, self::$domain);//这里写一个来源地址，可以写要抓的页面的首页
        curl_setopt($ch, CURLOPT_USERAGENT, $this->user_agent);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);

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

    public function curl_download($url, $data)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 200);   //只需要设置一个秒的数量就可以
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_REFERER, self::$domain);//这里写一个来源地址，可以写要抓的页面的首页
        curl_setopt($ch, CURLOPT_USERAGENT, $this->user_agent);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);

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
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            curl_setopt($ch, CURLOPT_POST, 1);
        }

        $clean_header = $this->get_clean_header();
        curl_setopt($ch, CURLOPT_HTTPHEADER, $clean_header);
        curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);
        curl_setopt($ch, CURLOPT_WRITEFUNCTION, function ($curl, $data) {
            echo $data;
            return strlen($data);
        });
        curl_exec($ch);
        curl_close($ch);
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
            $this->login();
        }

        $result = $this->curl($url, $data);
        if (stripos($result['url'], '/')
//            || stripos($result['body'], 'Sign in to Ahrefs')
        ) {
            //跳转到登陆的说明未登陆 || 没跳转但是需要登陆
            $this->login();
            $result = $this->curl($url, $data);
        }
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
        //Accept: application/json, text/plain, */*
        //Accept-Encoding: gzip, deflate, br
        //Accept-Language: zh-CN,zh;q=0.9,en;q=0.8
        //Connection: keep-alive
        //Content-Length: 76
        //Content-Type: application/json;charset=UTF-8
        //Cookie: _vwo_uuid_v2=D987BEB4A49FB29AA37ECA8161AE3A450|65a58fccce2bd31e14262af2d7bc95ea; __utmc=162630398; __utmz=162630398.1583846862.1.1.utmcsr=(direct)|utmccn=(direct)|utmcmd=(none); _ga=GA1.2.935833666.1583846862; _gid=GA1.2.220745340.1583846862; _vis_opt_s=1%7C; _vis_opt_test_cookie=1; _vwo_uuid=D987BEB4A49FB29AA37ECA8161AE3A450; _vwo_ds=3%241583846867%3A2.0491482%3A%3A; _vis_opt_exp_264_combi=1; __qca=P0-1924580995-1583846871982; _vis_opt_exp_264_goal_6=1; __distillery=3eb37e9_40a7678c-1635-4025-a4e5-10e7eb65991c-b08eef123-5235e0caf592-8a59; intercom-id-d2wjyudi=c617ddfb-e5b1-4d4e-98f8-23c73ccfcb11; _vis_opt_exp_266_combi=1; _vis_opt_exp_264_goal_13=1; _vis_opt_exp_266_goal_12=1; muxData=mux_viewer_id=46dfd354-54ac-4df9-8ec5-d4bc7d288ecf&msn=0.48609772500728&sid=eb6c9b25-1eae-4cc6-9f96-4ea59c61944c&sst=1583914929466&sex=1583916429466; uid=; whoson=761-1583915305058; SearchCount=2; LastSearch=google.com; __utma=162630398.935833666.1583846862.1584077593.1584085467.7; __utmt=1; ASP.NET_SessionId=ned0c0ii35clwhiwnwgzsf0t; __utmb=162630398.5.10.1584085467; _gat=1; _gat_UA-6858132-6=1; _gat_UA-6858132-12=1; _vwo_sn=238587%3A13; intercom-session-d2wjyudi=SE9RbGZOYUFNallDdE5JMWxhcXJ3TFJMdjFldFd3S2JpS2N0K29Oa2FaanJHaXhwb1E2Q2tDV0ZPaTkycFNzOS0tY0p2eTl6Qk1ETU5WampqelQvenZqZz09--adbe5f5afbe6c69776a8a426427dc7dd91352b95
        //Host: www.spyfu.com
        //Origin: https://www.spyfu.com
        //Referer: https://www.spyfu.com/auth/login
        //Sec-Fetch-Dest: empty
        //Sec-Fetch-Mode: cors
        //Sec-Fetch-Site: same-origin
        //User-Agent: Mozilla/5.0 (Macintosh

        //username: "ahrefst11@outlook.com"
        //password: "daqing88"
        //rememberMe: true


        $data = [
            'username' => $this->user_name,
            'password' => $this->password,
            'rememberMe' => true
        ];
        $result = $this->curl(self::$login_url, $data);
        if ($result['code'] == 200) {
            return true;
        }
        return false;
    }

    // 检查是否达到限制
    public function check_limit($url, $user_id)
    {
        $key = REDIS_PRE . 'user_limit:' . $user_id;
        $day = date("Ymd");

//        if (stripos(' ' . $url, 'site-explorer/overview/v2/subdomains/live?')) {
//            $limit_site_explorer_limit = 30;
//            $limit_site_explorer_key = REDIS_PRE . "site_explorer-{$day}:" . $user_id; //每人每天30次
//
//            $redis = RedisCache::connect();
//            //拿到这个key的 score
//            $score = $redis->zScore($key, $limit_site_explorer_key);
//            $score = $score ?? 0;
//            if ($score >= $limit_site_explorer_limit) {
//                // 达到限制
//                page_jump(PROTOCOL . DOMAIN_AHREFS . '/dashboard/', '超出查询限制数量');
//            }
//            $redis->zAdd($key, $score + 1, $limit_site_explorer_key);
//        }

    }
}