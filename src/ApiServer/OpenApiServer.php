<?php
/**
 * 公众号第三方平台接口调用服务
 *
 */

namespace ApiServer;

use Open\Auth\Open;

class OpenApiServer
{
    protected static $open;

    /**
     *
     * @param $config
     * @return mixed
     */
    public static function newOpen()
    {
        if (!(static::$open instanceof Open)) {
            static::$open = new Open(config('open'));
        }
        return static::$open;
    }
    
    /**
     * 刷新第三方平台Access Token
     *
     * @return array|bool|mixed
     */
    public static function componentAccessToken($verifyTicket)
    {
        $open = static::newOpen();
        $open->setRequestParams(['verifyTicket' => $verifyTicket]);
        return $open->componentAccessToken();
    }

    /**
     * 获取预授权码
     *
     * @return array|bool|mixed
     */
    public static function preAuthCode($comAccToken)
    {
        $open = static::newOpen();
        $open->setRequestParams(['componentAccessToken' => $comAccToken]);
        return $open->preAuthCode();
    }

    /**
     * 生成授权链接
     *
     *
     * @param $preAuthCode
     * @return mixed
     */
    public static function createAuthUrl($preAuthCode)
    {
        $open = static::newOpen();
        $open->setRequestParams(['preAuthCode' => $preAuthCode]);
        $response = $open->createAuthUrl();
        if ($response['errcode'] == 0) {
            return $response['authUrl'];
        }

    }

    /**
     * 获取公众号授权信息
     *
     *
     * @param $comAccToken
     * @param $authCode
     * @return mixed
     */
    public static function authAccessToken($comAccToken,$authCode)
    {
        $open = static::newOpen();
        $open->setRequestParams([
            'componentAccessToken' => $comAccToken,
            'authCode' => $authCode
        ]);
        return $open->authAccessToken();
    }

    /**
     * 刷新授权Access Token
     *
     * @param $authAppId
     * @param $refreshToken
     * @return bool
     */
    public static function refreshAuthAccessToken($comAccToken,$authAppId, $refreshToken)
    {
        $open = static::newOpen();
        $open->setRequestParams([
            'componentAccessToken' => $comAccToken,
            'authAppId' => $authAppId,
            'refreshToken' => $refreshToken,
        ]);
        return $open->refreshAuthAccessToken();
    }

    /**
     * 获取授权方的帐号基本信息
     * @param $authAppId
     * @return array|bool|mixed
     */
    public static function authorizeInfo($comAccToken,$authAppId)
    {
        $open = static::newOpen();
        $open->setRequestParams([
            'componentAccessToken' => $comAccToken,
            'authAppId' => $authAppId,
        ]);
        return $open->authorizeInfo();
    }


    /**
     * 获取第三方平台代公众号清理接口请求次数
     *
     * @param $params
     * @return mixed
     */
    public static function clearWeChatQuota($appId, $accessToken)
    {
        $open = static::newOpen();
        $params = [
            'appId' => $appId,
            'accessToken' => $accessToken,
        ];
        $open->setRequestParams($params);
        return $open->clearWeChatQuota();
    }

    /**
     * 获取第三方平台清零接口调用次数
     *
     * @param $componentAccessToken
     * @return mixed
     */
    public static function clearComponentQuota($componentAccessToken)
    {
        $open = static::newOpen();
        $params = [
            'componentAccessToken' => $componentAccessToken,
        ];
        $open->setRequestParams($params);
        return $open->createWebAuthorizeUrl();
    }

    /**
     * 获取第三方平台代公众号发起授权，授权url
     *
     *
     * @param $appId
     * @param $redirectUrl
     * @param $scope
     * @return mixed
     */
    public static function getWebAuthorizeUrl($appId, $redirectUrl, $scope)
    {
        $open = static::newOpen();
        $params = [
            'appId' => $appId,
            'redirectUrl' => $redirectUrl,
            'scope' => $scope,
        ];
        $open->setRequestParams($params);
        return $open->createWebAuthorizeUrl();
    }


    /**
     * 通过 code 换取 access_token
     * 通过code获取访问公众号用户信息的access_code
     *
     * 会返回：{
     * "access_token":"ACCESS_TOKEN",
     * "expires_in":7200,
     * "refresh_token":"REFRESH_TOKEN",
     * "openid":"OPENID",
     * "scope":"SCOPE"
     * }
     *
     *
     * @param $appId
     * @param $code
     * @param $componentAccessToken
     * @return mixed
     */
    public static function getWebAccessToken($appId,$code,$componentAccessToken)
    {
        $open = static::newOpen();
        $params = [
            'appId' => $appId,
            'componentAccessToken' => $componentAccessToken,
            'code' => $code,
        ];
        $open->setRequestParams($params);
        return $open->getWebAccessToken();

    }

    /**
     * 刷新 access_token
     * 通过code获取访问公众号用户信息的access_code
     *
     */
    public static function getWebAccessTokenRefresh($appId,$refreshToken,$componentAccessToken)
    {
        $open = static::newOpen();
        $params = [
            'appId' => $appId,
            'refreshToken' => $refreshToken,
            'componentAccessToken' => $componentAccessToken,
        ];
        $open->setRequestParams($params);
        return $open->getWebAccessTokenRefresh();

    }

}