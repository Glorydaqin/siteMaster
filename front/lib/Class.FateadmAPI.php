<?php


class FateadmAPI
{
    public $app_id;
    public $app_key;
    public $pd_id;
    public $pd_key;
    public $host;

    /**
     * // 主要调用的API请求类
     * // 参数 （appID，appKey，pdID，pdKey）
     * FateadmAPI constructor.
     * @param $app_id
     * @param $app_key
     * @param $pd_id
     * @param $pd_key
     */
    function __construct($app_id = '319135', $app_key = '+BZtdN9PFfTvkKGq7JAMpFKHap7Qyq0Q', $pd_id = '119135', $pd_key = 'EgoHxDJ5NNoQUrCzPhgy0sMHHR+bQ8HX')
    {
        $this->app_id = $app_id;
        $this->app_key = $app_key;
        $this->pd_id = $pd_id;
        $this->pd_key = $pd_key;
        $this->host = "http://pred.fateadm.com";
    }

    function SetHost($url)
    {
        $this->host = $url;
    }

    function PFunc()
    {
        echo "appid: " . $this->app_id . "\n";
        echo "appkey: " . $this->app_key . "\n";
        echo "pd_id: " . $this->pd_id . "\n";
        echo "pd_key: " . $this->pd_key . "\n";
    }

    /**
     * 识别验证码
     * 参数： $predict_type：识别类型, $img_data：要识别的图片数据
     * 返回值：
     *      $json_rsp->RetCode：正常时返回0
     *      $json_rsp->ErrMsg：异常时显示异常详情
     *      $json_rsp->RequestId：唯一的订单号
     *      $json_rsp->rsp->result：识别结果
     */
    function Predict($predict_type, $img_data)
    {
        $timestamp = time();
        $sign = $this->GetSign($this->pd_id, $this->pd_key, $timestamp);
        $data = array(
            'user_id' => $this->pd_id,
            'timestamp' => $timestamp,
            'sign' => $sign,
            'predict_type' => $predict_type,
            'up_type' => "mt"
        );
        if ($this->app_id != "") {
            $asign = $this->GetSign($this->app_id, $this->app_key, $timestamp);
            $data["appid"] = $this->app_id;
            $data["asign"] = $asign;
        }
        $url = $this->host . "/api/capreg";
        $rsp = $this->MFormPost($url, $data, $img_data);
        $json_rsp = json_decode($rsp);
        if ($json_rsp->RetCode == 0) {
            $result = json_decode($json_rsp->RspData);
            $json_rsp->rsp = $result;
        }
        return $json_rsp;
    }

    /**
     * 识别失败，进行退款请求
     * 参数：$request_id：需要退款的订单号
     * 返回值：
     *      $json_rsp->RetCode：正常时返回0
     *      $json_rsp->ErrMsg：异常时显示异常详情
     *
     * 注意：
     *      Predict识别接口，仅在ret_code == 0 时才会进行扣款，才需要进行退款请求，否则无需进行退款操作
     * 注意2：
     *      退款仅在正常识别出结果后，无法通过网站验证的情况，请勿非法或者滥用，否则可能进行封号处理
     */
    function Justice($request_id)
    {
        $timestamp = time();
        $sign = $this->GetSign($this->pd_id, $this->pd_key, $timestamp);
        $data = array(
            'user_id' => $this->pd_id,
            'timestamp' => $timestamp,
            'sign' => $sign,
            'request_id' => $request_id,
        );
        $url = $this->host . "/api/capjust";
        $post_data = $this->ArrayToPostData($data);
        $rsp = $this->Post($url, $post_data);
        $json_rsp = json_decode($rsp);
        return $json_rsp;
    }

    /**
     * 查询余额
     * 参数：无
     * 返回值：
     *      $json_rsp->RetCode：正常时返回0
     *      $json_rsp->ErrMsg：异常时显示异常详情
     *      $json_rsp->cust_val：用户余额
     */
    function QueryBalanc()
    {
        $timestamp = time();
        $sign = $this->GetSign($this->pd_id, $this->pd_key, $timestamp);
        $data = array(
            'user_id' => $this->pd_id,
            'timestamp' => $timestamp,
            'sign' => $sign,
        );
        $url = $this->host . "/api/custval";
        $post_data = $this->ArrayToPostData($data);
        $rsp = $this->Post($url, $post_data);
        $json_rsp = json_decode($rsp);
        return $json_rsp;
    }

    /**
     * 充值接口
     * 参数：$cardid：充值卡号, $cardkey：充值卡签名串
     * 返回值：
     *      $json_rsp->RetCode：正常时返回0
     *      $json_rsp->ErrMsg：异常时显示异常详情
     */
    function Charge($cardid, $cardkey)
    {
        $timestamp = time();
        $sign = $this->GetSign($this->pd_id, $this->pd_key, $timestamp);
        $csign = $this->GetCardSign($cardid, $cardkey, $timestamp, $this->pd_key);
        $data = array(
            'user_id' => $this->pd_id,
            'timestamp' => $timestamp,
            'sign' => $sign,
            'cardid' => $cardid,
            'csign' => $csign,
        );
        $url = $this->host . "/api/charge";
        $post_data = $this->ArrayToPostData($data);
        $rsp = $this->Post($url, $post_data);
        $json_rsp = json_decode($rsp);
        return $json_rsp;
    }

    /**
     * 查询网络延迟
     * 参数： $predict_type:识别类型
     * 返回值：
     *      $json_rsp->RetCode：正常时返回0
     *      $json_rsp->ErrMsg：异常时显示异常详情
     */
    function RTT($predict_type)
    {
        $timestamp = time();
        $sign = $this->GetSign($this->pd_id, $this->pd_key, $timestamp);
        $data = array(
            'user_id' => $this->pd_id,
            'timestamp' => $timestamp,
            'sign' => $sign,
            'predict_type' => $predict_type,
        );
        if ($this->app_id != "") {
            $asing = $this->GetSign($this->app_id, $this->app_key, $timestamp);
            $data["appid"] = $this->app_id;
            $data["asign"] = $asing;
        }
        $url = $this->host . "/api/qcrtt";
        $post_data = $this->ArrayToPostData($data);
        $rsp = $this->Post($url, $post_data);
        $json_rsp = json_decode($rsp);
        if ($json_rsp->RetCode == 0) {
            $result = json_decode($json_rsp->RspData);
            $json_rsp->rsp = $result;
        }
        return $json_rsp;
    }

    /***
     * 余额查询,只返回余额
     * 参数：无
     * 返回值:用户余额
     */
    function QueryBalancExtend()
    {
        $rsp = $this->QueryBalanc();
        return json_decode($rsp->RspData)->cust_val;
    }

    /***
     * 充值接口，成功返回0
     * 参数：$cardid：充值卡号, $cardkey：充值卡签名串
     * 返回值： 充值成功返回0
     */
    function ChargeExtend($cardid, $cardkey)
    {
        $rsp = $this->Charge($cardid, $cardkey);
        return $rsp->RetCode;
    }

    /***
     * 退款接口，成功返回0
     * 参数：$request_id：需要退款的订单号
     * 返回值：退款成功时返回0
     */
    function JusticeExtend($request_id)
    {
        $rsp = $this->Justice($request_id);
        return $rsp->RetCode;
    }

    /***
     * 识别接口，只返回识别结果
     * 参数： $predict_type：识别类型, $img_data：要识别的图片数据
     * 返回值： 识别的结果
     */
    function PredictExtend($predict_type, $img_data)
    {
        $rsp = $this->Predict($predict_type, $img_data);
        return json_decode($rsp->RspData)->result;
    }


    private function GetSign($pd_id, $pd_key, $timestamp)
    {
        $chk_sign1 = md5($timestamp . $pd_key);
        $chk_sign2 = md5($pd_id . $timestamp . $chk_sign1);
        return $chk_sign2;
    }

    private function GetCardSign($cardid, $cardkey, $timestamp, $pdkey)
    {
        $sign = md5($pdkey . $timestamp . $cardid . $cardkey);
        return $sign;
    }

    private function Post($url, $post_data)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 120);    //设置本机的post请求超时时间
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }

    private function ArrayToPostData($data)
    {
        $post_data = "";
        $first_flag = true;
        foreach ($data as $key => $val) {
            if ($first_flag == true) {
                $first_flag = false;
            } else {
                $post_data = $post_data . "&";
            }
            $post_data = $post_data . $key . "=" . $val;
        }
        return $post_data;
    }

    private function MFormPost($url, $post_data, $img_data)
    {
        $uniq_id = uniqid();
        $upload_data = $this->ArrayToMFormData($post_data, $img_data, $uniq_id);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 120);    //设置本机的post请求超时时间
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $upload_data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Content-Type: multipart/form-data; boundary=" . $uniq_id,
            "Content-Length: " . strlen($upload_data)
        ]);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }

    private function ArrayToMFormData($post_data, $file, $uniq_id)
    {
        $data = "";
        //拼接报头
        foreach ($post_data as $key => $val) {
            $data .= "--" . $uniq_id . "\r\n"
                . 'Content-Disposition: form-data; name="' . $key . "\"\r\n\r\n"
                . $val . "\r\n";
        }
        //拼接文件
        $data .= "--" . $uniq_id . "\r\n"
            . 'Content-Disposition: form-data; name="img_data"; filename="' . "img_data" . "\"\r\n"
            . 'Content-Type:application/octet-stream' . "\r\n\r\n";
        $data .= $file . "\r\n";
        $data .= "--" . $uniq_id . "--\r\n";
        return $data;
    }

}

