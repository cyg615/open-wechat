<?php
/**
 * Created by PhpStorm.
 * User: owner
 * Date: 2019-06-25
 * Time: 11:30
 * Project Name: openWeChat
 */

namespace Open\Support\Request;


use GuzzleHttp\Client;
use Open\Support\Log\Log;

class ApiRequest
{


    /**
     * 第三方平台POST清过
     *
     * @param $action
     * @param $url
     * @param $params
     * @return array|mixed
     */
    public static function postRequest($action,$url,$params)
    {
        try{
            $client = new Client();
            $response = $client->post($url,[
                'timeout' => 5,
                'json' => $params
            ]);
            $content = self::responseProcess($response);

        } catch (\Exception $exception) {
            $content = [
                'errcode' => 40000,
                'errmsg' => '['.$exception->getCode().']'.$exception->getMessage()
            ];
        }


        if(!isset($content['errcode']) || $content['errcode'] == 0) {
            $content['errcode'] = 0;
            return $content;
        } else{
            Log::error($action,$content);
            throw new \Exception($content['errmsg'],$content['errcode']);
        }
        return false;
    }

    /**
     * get请求
     *
     * @param $action
     * @param $url
     * @param $params
     * @return array|bool|mixed
     * @throws \Exception
     */
    public static function getRequest($action,$url,$params)
    {
        try{
            $client = new Client();
            $response = $client->get($url,[
                'timeout' => 5,
                'query' => $params
            ]);
            $content = self::responseProcess($response);

        } catch (\Exception $exception) {
            $content = [
                'errcode' => 40000,
                'errmsg' => '['.$exception->getCode().']'.$exception->getMessage()
            ];
        }


        if(!isset($content['errcode']) || $content['errcode'] == 0) {
            $content['errcode'] = 0;
            return $content;
        } else{
            Log::error($action,$content);
            throw new \Exception($content['errmsg'],$content['errcode']);
        }
        return false;
    }


    /**
     * 处理请求结果
     *
     * @param $response
     * @return array|mixed
     */
    public static function responseProcess($response)
    {
        $response = $response->getBody()->getContents();
        if($content = json_decode($response,true)) {
            if(!isset($content['errcode'])) {
                $content['errcode'] = 0;
            }
            return $content;
        } else {
            return [
                'errcode' => 40000,
                'errmsg' => '请求解析失败'
            ];
        }

    }

    /**
     * 处理请求参数
     * @param $params
     * @return string
     */
    public static function convertUrlParams($params)
    {
        $buff = "";
        foreach ($params as $k => $v) {
            if ($k != "sign") {
                $buff .= $k . "=" . $v . "&";
            }
        }
        $buff = trim($buff, "&");
        return $buff;
    }

    /**
     * 获取原生session
     *
     * @param int $length
     * @param bool $normal
     * @return bool|string
     */
    public static function randString($length = 128, $normal = true)
    {
        if ($normal) {
            $char = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        } else {
            $char = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ~!@#$%^&*()_+';
        }
        if (!is_int($length) || $length < 0) {
            return false;
        }
        $string = '';
        for ($i = $length; $i > 0; $i--) {
            $string .= $char[mt_rand(0, strlen($char) - 1)];
        }

        return $string;
    }
}