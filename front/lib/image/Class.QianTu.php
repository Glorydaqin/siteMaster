<?php

/**
 * Created by PhpStorm.
 * User: qinwei
 * Date: 2020-01-12
 * Time: 23:32
 */
class QianTu
{

    function curl($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5000);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'User-Agent: Mozilla/5.0 (iPhone; CPU iPhone OS 8_0 like Mac OS X) AppleWebKit/600.1.3 (KHTML, like Gecko) Version/8.0 Mobile/12A4345d Safari/600.1.4'
        ));
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_REDIR_PROTOCOLS, -1);
        curl_setopt($ch, CURLOPT_REFERER, 'https://www.58pic.com/c/15990160');//修改Referer
        $contents = curl_exec($ch);
        curl_close($ch);
        return $contents;
    }

    function getImageUrl($PicUrl)
    {
        $contents = $this->curl($PicUrl);
        preg_match("~preview(.*?).jpg~", $contents, $matches);
        if (count($matches) == 0) {
            return false;
        }
        $img_url = $matches[1];
        $img_url_no_watermark = "http://pic" . $img_url . ".jpg";

        return $img_url_no_watermark;
    }

    public function downImage($url)
    {
        $content = $this->curl($url);
        return $content;
    }
}