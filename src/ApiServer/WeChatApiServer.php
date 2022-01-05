<?php
/**
 * 公众号第三方平台接口调用服务
 *
 */

namespace ApiServer;


use Open\Auth\WeChat;

class WeChatApiServer
{
    protected static $weChat;

    /**
     *
     *
     * @return mixed
     * @throws \Exception
     */
    public static function newWeChat()
    {
        if (!(static::$weChat instanceof WeChat)) {
            static::$weChat = new WeChat(config('open'));
        }
        return static::$weChat;
    }

    /**
     * 获取用户openID
     *
     *
     * @return mixed
     * @throws \Exception
     */
    public static function getAuthCodeUrl()
    {
        $weChat = static::newWeChat();
        $params = [
            'appId' => config('open.weChat.app_id'),
            'redirectUri' => config('open.weChat.redirect_uri'),
            'responseType' => config('open.weChat.response_type'),
            'scope' => config('open.weChat.scope'),
            'useProxy' => config('open.weChat.use_proxy'),
        ];

        $weChat->setRequestParams($params);

        return $weChat->getAuthCodeUrl();
    }

    /**
     * 通过$code 获取access token和OpenId
     *
     * @param $code
     * @return mixed
     * @throws \Exception
     */
    public static function getOpenIdAccessToken($code)
    {
        $weChat = static::newWeChat();

        $params = [
            'appId' => config('open.weChat.app_id'),
            'secret' => config('open.weChat.app_secret'),
            'code' => $code
        ];

        $weChat->setRequestParams($params);
        return $weChat->getAccessTokenOpenId();
    }

    /**
     * 刷新网页授权Access Token
     *
     *
     * @param $refreshToken
     * @return mixed
     * @throws \Exception
     */
    public static function refreshAccessToken($refreshToken)
    {
        $weChat = static::newWeChat();

        $params = [
            'appId' => config('open.weChat.app_id'),
            'refreshToken' => $refreshToken
        ];

        $weChat->setRequestParams($params);
        return $weChat->refreshAccessToken();
    }

    /**
     * 根据用户openId 和access token 获取用户信息
     *
     * @param $openId
     * @param $accessToken
     * @return mixed
     * @throws \Exception
     */
    public static function getUserInfo($openId,$accessToken)
    {
        $weChat = static::newWeChat();
        $params = [
            'appId' => config('open.weChat.app_id'),
            'accessToken' => $accessToken,
            'openId' => $openId,
        ];

        $weChat->setRequestParams($params);
        return $weChat->getUserInfo();
    }

    /**
     * 获取去公众号的基础Access Token
     *
     *
     * @return mixed
     * @throws \Exception
     */
    public static function getBasicAccessToken()
    {
        $weChat = static::newWeChat();
        $params = [
            'appId' => config('open.weChat.app_id'),
            'secret' => config('open.weChat.app_secret'),
        ];

        $weChat->setRequestParams($params);
        return $weChat->getBasicAccessToken();
    }

    /**
     * 获取jsApiTicket
     *
     * @param string $accessToken 公众号开发基础token
     * @return mixed
     * @throws \Exception
     */
    public static function getJsApiTicket($accessToken)
    {
        $weChat = static::newWeChat();
        $params = [
            'accessToken' => $accessToken,
        ];

        $weChat->setRequestParams($params);
        return $weChat->getJsTicketToken();
    }

    /**
     * 获取分享配置
     *
     *
     * @param $jsApiTicket
     * @param $signUrl
     * @return mixed
     * @throws \Exception
     */
    public static function getShareSetting($jsApiTicket, $signUrl)
    {
        $weChat = static::newWeChat();

        $params = [
            'jsApiTicket' => $jsApiTicket,
            'signUrl' => $signUrl,
        ];
        $weChat->setRequestParams($params);
        return $weChat->getShareSetting();
    }


}