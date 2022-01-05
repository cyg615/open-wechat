<?php
/**
 * Created by PhpStorm.
 * User: owner
 * Date: 2019-10-15
 * Time: 16:58
 * Project Name: openWeChat
 */

namespace Open\LocalTraits;


use Open\Support\Request\ApiRequest;

trait ShareSetting
{
    /**
     * 获取微信分享数据
     *
     *\
     * @param $params
     * @return array|bool|mixed|string
     */
    protected function getWeChatShareSetting($params)
    {

        $share = [
            'timeStamp' => time(),
            'nonceStr' => ApiRequest::randString(32),
            'signUrl' => $params['sign_url'],
        ];

        //加密签名
        $query = [
            'jsapi_ticket' => $params['jsapi_ticket'],
            'noncestr' => $share['nonceStr'],
            'timestamp' => $share['timeStamp'],
            'url' => $share['signUrl'],
        ];

        $query = ApiRequest::convertUrlParams($query);

        $share ['signature'] = sha1($query);
        $share ['errcode'] = 0;

        return $share;
    }


}