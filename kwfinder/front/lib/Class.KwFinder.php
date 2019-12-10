<?php

class KwFinder
{
    public static $cdn_domain = "https://cdn.ahrefs.com";
    public static $domain = "https://app.kwfinder.com/";
    public static $mangools_domain = "https://mangools.com/";
    public static $login_url = 'https://mangools.com/users/sign_in';

    public static $user_agent = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/78.0.3904.108 Safari/537.36';

    private $user_name = '';
    private $password = '';
    private $cookie_key = '';

    public function __construct($user_name, $password)
    {
        $this->user_name = $user_name;
        $this->password = $password;
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
        return $tmp;
    }

    public function curl($url, $data = [])
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

        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $res = curl_getinfo($ch);
        curl_close($ch);
        $r = array();
        $r['code'] = $httpCode;
        $r['url'] = $res['url'];
        $r['body'] = $content;

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
//        $cache = new Cache();
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
        if (stripos($result['url'], '/user/login')) {
            //跳转到登陆的说明未登陆
            $this->login();
            $result = $this->curl($url, $data);
        }
//        if ($is_cdn) {
//            $result_str = json_encode($result);
//            $cache->set_cache($url, $result_str);
//        }
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
        //:authority: mangools.com
        //:method: POST
        //:path: /users/sign_in
        //:scheme: https
        //accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3
        //accept-encoding: gzip, deflate, br
        //accept-language: zh-CN,zh;q=0.9,en;q=0.8
        //cache-control: no-cache
        //content-length: 203
        //content-type: application/x-www-form-urlencoded
        //cookie: analytics_source=%7B%22referrer%22%3A%22https%3A%2F%2Fwww.baidu.com%2Flink%3Furl%3DKy7dcZQ1O9vCy82GK8TgVSJgEhgzfklAEqe0dpJ5_D3%5Cu0026wd%3D%5Cu0026eqid%3Dca78b385001ae63d000000065dee48ed%22%2C%22page%22%3A%22https%3A%2F%2Fkwfinder.com%2F%22%7D; currency=usd; country_code=cn; _ga=GA1.2.1991415405.1575897357; _gid=GA1.2.376932348.1575897357; __tawkuuid=e::mangools.com::00m8h664dn1cIsLrW7g2nSu/11CzGJJShgEaUiRHHaUxJUW9qslH3wqpFWpG2KvQ::2; intercom-id-rbrt7jx6=c0396ca7-52a6-4bc1-9093-cbdce2e77649; earlyDiscountPopup=hide; bp_ut_session=%7B-q-pageviews-q-%3A1-c--q-referrer-q-%3A-q-https%3A%2F%2Fapp.kwfinder.com%2Fdashboard%3Flanguage_id%3D0%26location_id%3D0%26query%3Dgoogle.com%26source_id%3D1%26sub_source_id%3D3-q--c--q-landingPage-q-%3A-q-https%3A%2F%2Fmangools.com%2Fblog%2Fkwfinder-guide%2F%3Fref%3Dheader-app-kw%26_ga%3D2.76835543.376932348.1575897357-1991415405.1575897357-q--c--q-started-q-%3A1575899507869%7D; intercom-session-rbrt7jx6=eW5OZnJOVy9tcHV6Vk1aeDNHZGorK2RSQ0RrcEttQUp4QXlxY1hMU3AxaWxMWDkwM1pTUUlxV3F6VTAvNTZzRy0tVktyNkpPUHZjSkVVNURNcklkVVBLZz09--23a94eaa877d262cae3ef36f619588193ee41f83; hide-extension-block=1; _mangotools_com_session=eEU2WmJSSzRIVEpvSXJicG5GTXJrOTB6MGo2UWYvcWI3OFpsSEEwRVhyenVQYjhDcXNmcTNlTkI4TTdTejRtTHAwOWRLanRJR2JWb3NTb2VQdnphZDNtcjJXZzNJK25qU1pwOGhnaHdRVk8zS1plYU1sL2lpV3Rqd0loR2tSNlV1TEozTHZRM3ZjbmFRbGk5Tk96VkNBPT0tLVc5clQ1Uy9aVEQxRHcvckFGdnhoRWc9PQ%3D%3D--bb4661b61798beffa5cd3ab306e8d1f7a1ac76f5; _gat=1; TawkConnectionTime=0
        //origin: https://mangools.com
        //pragma: no-cache
        //referer: https://mangools.com/users/sign_in
        //sec-fetch-mode: navigate
        //sec-fetch-site: same-origin
        //sec-fetch-user: ?1
        //upgrade-insecure-requests: 1
        //user-agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/78.0.3904.108 Safari/537.36

        //utf8: ✓
        //authenticity_token: 8tdKJsIVlKMcoS5ip+oMAISS7YVL8clVqu3wf3BtzB5t6B6zzFa0VbFjFcZ8XVCyFm8B9C12zSDkceHVbZ6QfA==
        //user[email]: d
        //user[password]: daqing
        //ref:
        //button:

        $index_result = $this->curl(self::$login_url);
        preg_match_all("/name=\"authenticity_token\"\s+?value=\"(.*?)\"/", $index_result['body'], $match_result);

        $token = isset($match_result[1][0]) ? $match_result[1][0] : '';
        $data = [
            'utf8' => '✓',
            'authenticity_token' => $token,
            'user[email]' => $this->user_name,
            'user[password]' => $this->password,
            'ref' => '',
            'button' => '',
        ];
        $result = $this->curl(self::$login_url, $data);
        if ($result['code'] == 200) {
            return true;
        }
        return false;
    }

}