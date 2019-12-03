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
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->get_clean_header());
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

    public function get($url, $data = [], $is_cdn = false)
    {
        if (!$is_cdn) {
            if (!$this->check_is_login()) {
                $this->login();
            }
        }

        $result = $this->curl($url, $data);
        return $result;
    }


    public function check_is_login()
    {
        $result = $this->curl(self::$domain . 'dashboard');
        if ($result['code'] != 200 || !stripos($result['body'], 'Account settings')) {
            return false;
        }
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
        preg_match_all("/value=\"(.*?)\" name=\"_token\"/", $index_result['body'], $match_result);

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