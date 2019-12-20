<?php

class Majestic
{

    public static $domain = "https://zh.majestic.com/";
    public static $login_url = 'https://zh.majestic.com/account/login';
    public static $captcha_url = 'https://zh.majestic.com/account/login/captcha';


    public static $user_agent = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/78.0.3904.108 Safari/537.36';

    private $user_name = '';
    private $password = '';
    public $cookie_key = '';


    public function __construct($user_name, $password)
    {
        $this->user_name = $user_name;
        $this->password = $password;
        $this->cookie_key = __CLASS__ . "_" . $user_name;
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
        $tmp[] = 'upgrade-insecure-requests: 1';
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

        $res = curl_getinfo($ch);

        curl_close($ch);
        $r = array();
        $r['code'] = $res['http_code'];
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
        if (stripos($result['url'], '/users/current_user') && !empty($result['body'])) {
            $response = json_decode($result['body'], true);
            if (empty($response['user'])) {
                // 返回用户是空说明需要登陆了
                $this->login();
                $result = $this->curl($url, $data);
            }
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

        return true;
    }


    public function login()
    {

        //get captcha
        $captcha_response = $this->curl(self::$captcha_url);

        if (empty($captcha_response['body']))
            return false;
        $dmApi = new FateadmAPI();
        $rsp = $dmApi->Predict(10600, $captcha_response['body']);
        $code = json_decode($rsp->RspData)->result;
        $requestId = $rsp->RequestId;

        //login
        //:authority: zh.majestic.com
        //:method: POST
        //:path: /account/login
        //:scheme: https
        //accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9
        //accept-encoding: gzip, deflate, br
        //accept-language: zh-CN,zh;q=0.9,en;q=0.8
        //cache-control: no-cache
        //content-length: 189
        //content-type: application/x-www-form-urlencoded
        //cookie: RURI=%2F; REF=www.baidu.com; _gcl_au=1.1.2037983004.1576483820; _ga=GA1.2.1208316467.1576483847; _gid=GA1.2.2072404171.1576746526; _pk_ref.2.c4b1=%5B%22%22%2C%22%22%2C1576807036%2C%22https%3A%2F%2Fwww.baidu.com%2Flink%3Furl%3D2pIStCjGK4Gtkx_CcRu3h_-kM3cPGFyMnk6kRLlA20GTqKEWsaxkdK6CaB-WK7QZ%26wd%3D%26eqid%3Dea717f7d00030231000000065df73be6%22%5D; _pk_ses.2.c4b1=1; _pk_id.2.c4b1=661b01f2aa37b7cf.1576483853.3.1576807390.1576807036.
        //origin: https://zh.majestic.com
        //pragma: no-cache
        //referer: https://zh.majestic.com/account/login?redirect=%2Fplans-pricing
        //sec-fetch-mode: navigate
        //sec-fetch-site: same-origin
        //sec-fetch-user: ?1
        //upgrade-insecure-requests: 1
        //user-agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.88 Safari/537.36

        //redirect: /plans-pricing
        //ruri: /plans-pricing
        //forceConcurrentLogin:
        //subscriptionCode:
        //country:
        //currency:
        //term:
        //EmailAddress: 61234151@qq.com
        //Password: daqing
        //Captcha: 468554
        //CaptchaShown: 1
        $data = [
            'redirect' => '/account',
            'ruri' => '/account',
            'forceConcurrentLogin' => '',
            'subscriptionCode' => '',
            'country' => '',
            'currency' => '',
            'term' => '',
            'EmailAddress' => $this->user_name,
            'Password' => $this->password,
            'Captcha' => $code,
            'CaptchaShown' => '1',
            'RememberMe' => 'on',
        ];
        $login_result = $this->curl(self::$login_url, $data);

        //如果返回的结果中有 ==》  验证码不正确
        if (stripos($login_result['body'], '验证码不正确')) {
            $rs = $dmApi->JusticeExtend($requestId);
            return false;
        } elseif (stripos($login_result['url'], '/account')) {
            return true;
        }
        return false;
    }

    /**
     * 域名替换
     */
    public function replace_url()
    {

    }

    /**
     * 域名恢复
     */
    public function revoke_url($url)
    {
        $real_url = self::$domain . $url;
        if (stripos($url, 'mangools_domain/') !== false) {
            $real_url = str_replace("mangools_domain/", KwFinder::$mangools_domain, $url);
        } elseif (stripos($url, 'mangools_api_domain/') !== false) {
            $real_url = str_replace("mangools_api_domain/", KwFinder::$mangools_api_domain, $url);
        }
        if (stripos($real_url, 'users/current_user') !== false) {
            $real_url .= '=' . (new Cache())->get_cache($this->cookie_key);
        }
        return $real_url;
    }

    public function replace_main_js($url, $html)
    {
        if (preg_match("/app\.[a-z\d]+\.js/", $url)) {
            //替换    https://api2.mangools.com => /mangools_api_domain
            $html = str_replace('https://api2.mangools.com', PROTOCOL . DOMAIN_KWFINDER . '/mangools_api_domain', $html);
            //替换 https://mangools.com => /mangools_domain
            $html = str_replace('https://mangools.com', PROTOCOL . DOMAIN_KWFINDER . '/mangools_domain', $html);
            //替换    https://app.kwfinder.com =>
            $html = str_replace('https://app.kwfinder.com', PROTOCOL . DOMAIN_KWFINDER, $html);
            //替换    app.kwfinder.com =>
            $html = str_replace('app.kwfinder.com', DOMAIN_KWFINDER, $html);
        }
        return $html;
    }

    public function replace_html($url, $html)
    {

    }
}