<?php

class Ahrefs
{
    public static $cdn_domain = "https://cdn.ahrefs.com/";
    public static $domain = "https://ahrefs.com/";
    public static $login_url = 'https://ahrefs.com/user/login';

    public static $user_agent = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/78.0.3904.108 Safari/537.36';

    private $user_name = '';
    private $password = '';
    private $cookie_key = '';
    public $buffer = ''; //your download buffer goes here.

    public function __construct($user_name, $password)
    {
        $this->user_name = $user_name;
        $this->password = $password;
        $this->cookie_key = "siteMaster_Ahrefs_" . $user_name;
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
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            curl_setopt($ch, CURLOPT_POST, 1);
        }

        $clean_header = $this->get_clean_header();
        curl_setopt($ch, CURLOPT_HTTPHEADER, $clean_header);
        curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);
        curl_setopt($ch, CURLOPT_WRITEFUNCTION, function($curl, $data) {
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
        if (stripos($result['url'], '/user/login')) {
            //跳转到登陆的说明未登陆
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
        //Request URL: https://ahrefs.com/user/login
        //Request Method: POST
        //Status Code: 200
        //Remote Address: 127.0.0.1:1086
        //Referrer Policy: no-referrer-when-downgrade
        //cache-control: private, must-revalidate
        //content-encoding: gzip
        //content-length: 139
        //content-type: application/json
        //date: Mon, 02 Dec 2019 08:49:55 GMT
        //expires: -1
        //pragma: no-cache
        //server: nginx
        //set-cookie: XSRF-TOKEN=tnjhJdDRETARsrLUZTbU22Ci2ThzL1opQqLwKR7M; expires=Mon, 02-Dec-2019 12:49:55 GMT; Max-Age=14400; path=/; domain=.ahrefs.com; secure
        //set-cookie: BSSESSID=%2BaBI6ixHk6BKoCEV8Aw%2FoP5A%2BsVjIH5GoB2alK5p; path=/; domain=.ahrefs.com; secure; HttpOnly
        //status: 200
        //strict-transport-security: max-age=31536000
        //vary: Accept-Encoding
        //:authority: ahrefs.com
        //:method: POST
        //:path: /user/login
        //:scheme: https
        //accept: application/json, text/javascript, */*; q=0.01
        //accept-encoding: gzip, deflate, br
        //accept-language: zh-CN,zh;q=0.9,en;q=0.8
        //cache-control: no-cache
        //content-length: 132
        //content-type: application/x-www-form-urlencoded; charset=UTF-8
        //cookie: intercom-id-dic5omcp=cb13bbae-f105-4460-b83b-6d4fd2b89b80; _iub_cs-794932=%7B%22consent%22%3Atrue%2C%22timestamp%22%3A%222019-11-30T14%3A41%3A47.035Z%22%2C%22version%22%3A%221.2.4%22%2C%22id%22%3A794932%7D; XSRF-TOKEN=tnjhJdDRETARsrLUZTbU22Ci2ThzL1opQqLwKR7M; BSSESSID=GVzfGE0TwEpnXcuvQjD8TG5g6krX5rUTpUxd09az
        //origin: https://ahrefs.com
        //pragma: no-cache
        //referer: https://ahrefs.com/user/login
        //sec-fetch-mode: cors
        //sec-fetch-site: same-origin
        //user-agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/78.0.3904.108 Safari/537.36
        //x-requested-with: XMLHttpRequest

        //_token: tnjhJdDRETARsrLUZTbU22Ci2ThzL1opQqLwKR7M
        //email: 1912037638@qq.com
        //password: Ranqinghua1
        //return_to: https://ahrefs.com/

        $index_result = $this->curl(self::$domain);
        preg_match_all("/value=\"(.*?)\"\s+?name=\"_token\"/", $index_result['body'], $match_result);

        $token = isset($match_result[1][0]) ? $match_result[1][0] : '';
        $data = [
            '_token' => $token,
            'email' => $this->user_name,
            'password' => $this->password,
            'return_to' => self::$domain
        ];
        $result = $this->curl(self::$login_url, $data);
        if ($result['code'] == 200) {
            return true;
        }
        return false;
    }

}