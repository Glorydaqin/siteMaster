<?php

class SEMRush
{
    public static $cdn_domain = "https://cdn.ahrefs.com/";
    public static $domain = "https://semrush.com/";
    public static $login_url = 'https://semrush.com/user/login';

    public static $user_agent = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/78.0.3904.108 Safari/537.36';

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
        //:authority: www.semrush.com
        //:method: POST
        //:path: /sso/authorize
        //:scheme: https
        //accept: application/json, text/plain, */*
        //accept-encoding: gzip, deflate, br
        //accept-language: zh-CN,zh;q=0.9,en;q=0.8
        //cache-control: no-cache
        //content-length: 160
        //content-type: application/json;charset=UTF-8
        //cookie: __cfduid=df92aaaf16a7f0e88642741ec0b656f8f1572497476; ref_code=__default__; n_userid=rBtUbl26aEQWyQAPBWNSAg==; _ga=GA1.2.1655811541.1572497481; _gcl_au=1.1.936760122.1572497481; visit_first=1572497483031; tracker_ai_user=5gQ1L|2019-10-31T04:51:24.555Z; community_layout=k91rnvfimdv7kbs94c2agt2avs; mindboxDeviceUUID=1edd1e4f-fdf3-4fa8-a6aa-171469a0ed44; directCrm-session=%7B%22deviceGuid%22%3A%221edd1e4f-fdf3-4fa8-a6aa-171469a0ed44%22%7D; marketing=%7B%22user_cmp%22%3A%22%22%2C%22user_label%22%3A%22%22%7D; db=us; userdata=%7B%22tz%22%3A%22GMT+8%22%2C%22ol%22%3A%22zh%22%7D; utz=Asia%2FShanghai; __zlcmid=vfikNzRRRBpfvi; site_csrftoken=V08H2xOu7ajaUiBEsCfmbXMD2fBbDnIzumvC4KD4DAFmQKYW9eLj8Y5Yq4IlRVS2; __cflb=1796434593; _gid=GA1.2.1138566631.1576480861; ga_exp_7xHEszwjQFucPwMnhXHIzQ=0; _gac_UA-6197637-22=1.1576480909.Cj0KCQiA0NfvBRCVARIsAO4930kzW5lHXZTnHMBxgMU8TYJTdyl3WeE7t1bNceHrPWQ991BfuvYE2DkaAr9XEALw_wcB; _gcl_aw=GCL.1576480909.Cj0KCQiA0NfvBRCVARIsAO4930kzW5lHXZTnHMBxgMU8TYJTdyl3WeE7t1bNceHrPWQ991BfuvYE2DkaAr9XEALw_wcB; uvts=06ee9aaf-dff5-4d2f-5f57-9c26e8e10af6; __insp_uid=2685121539; billing_csrf_cookie=billing_csrf_cookie; webinars_session=UOOG7XXDKGl5LStsgLdm6SfeOmfD5xFyv3DSqJfn; community-semrush=rTiKExCgneJOcBVKiECTkV61iOyboiZOWOgBdCTI; blog_split=C; lux_uid=157648811925430662; __insp_wid=826279527; __insp_nv=false; __insp_targlpu=aHR0cHM6Ly93d3cuc2VtcnVzaC5jb20vdXNlcnMvbG9naW4uaHRtbA%3D%3D; __insp_targlpt=TG9naW4gdG8gU0VNcnVzaA%3D%3D; __insp_norec_sess=true; PHPSESSID=6f7d5ccddd59f2eeace20c9af2a362f0; SSO-JWT=eyJhbGciOiJFUzI1NiIsInR5cCI6IkpXVCJ9.eyJqdGkiOiI2ZjdkNWNjZGRkNTlmMmVlYWNlMjBjOWFmMmEzNjJmMCIsImlhdCI6MTU3NjQ4ODMwNiwiaXNzIjoic3NvIn0.tZMQuWR1HCLwHf9r1Ixv8SqVgmYl6a4OgOy6qCujyYec2jmmSz0UNI-J1AVN0GKKK6ypMZZIfa6e32n5LNt4WQ; XSRF-TOKEN=qABx1UKUy7KGiViJeqEDHsO3oaCC03f6Pnj903jA; usertype=Unlogged-User; __insp_slim=1576488349925; _gat=1
        //origin: https://www.semrush.com
        //pragma: no-cache
        //referer: https://www.semrush.com/users/login.html
        //sec-fetch-mode: cors
        //sec-fetch-site: same-origin
        //user-agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.79 Safari/537.36
        //x-xsrf-token: qABx1UKUy7KGiViJeqEDHsO3oaCC03f6Pnj903jA

        //{"user-agent-hash":"dce305f9c61beb1f46ab121fae098a7d",
        //"source":"semrush",
        //"email":"692860800@qq.com",
        //"password":"daqing",
        //"g-recaptcha-response":"",
        //"locale":"en"}

        $index_result = $this->curl(self::$domain);
        preg_match_all("/value=\"(.*?)\"\s+?name=\"_token\"/", $index_result['body'], $match_result);

        $token = isset($match_result[1][0]) ? $match_result[1][0] : '';
        $data = json_encode([
            'user-agent-hash' => '',
            'source' => 'semrush',
            'email' => $this->user_name,
            'password' => $this->password,
            'g-recaptcha-response' => '',
            'locale' => 'en',
        ]);

        $result = $this->curl(self::$login_url, $data);
        if ($result['code'] == 200) {
            return true;
        }
        return false;
    }

}