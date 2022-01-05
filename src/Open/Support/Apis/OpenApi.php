<?php
/**
 * 公众号第三方平台接口调用服务
 *
 */

namespace Open\Support\Apis;

use Open\LocalTraits\BasicProcess;
use Open\Support\Config\Config;
use Open\Support\Log\Log;
use Open\Support\Request\ApiRequest;

class OpenApi
{
    use BasicProcess;

    protected $config;
    protected $params;
    protected $authUrl = 'https://mp.weixin.qq.com/cgi-bin/componentloginpage';
    protected $comAccTk = 'https://api.weixin.qq.com/cgi-bin/component/api_component_token';
    protected $repAccTk = 'https://api.weixin.qq.com/cgi-bin/component/api_create_preauthcode';
    protected $authAccTk = 'https://api.weixin.qq.com/cgi-bin/component/api_query_auth';
    protected $refAuthAccTk = 'https://api.weixin.qq.com/cgi-bin/component/api_authorizer_token';
    protected $authInfo = 'https://api.weixin.qq.com/cgi-bin/component/api_get_authorizer_info';

    //代公众号调用接口调用次数清零 API 的权限。
    protected $clearQuota = 'https://api.weixin.qq.com/cgi-bin/clear_quota';

    //第三方平台对其所有 API 调用次数清零（只与第三方平台相关，与公众号无关，接口如 api_component_token）
    protected $componentClearQuota = 'https://api.weixin.qq.com/cgi-bin/component/clear_quota';

    //代替公众号发起网页授权请求
    protected $authorizeUrl = 'https://open.weixin.qq.com/connect/oauth2/authorize';

    //通过code获取访问公众号用户信息的access_code
    protected $webAuthorizeToken = 'https://api.weixin.qq.com/sns/oauth2/component/access_token';
    protected $webAuthorizeTokenRefresh = 'https://api.weixin.qq.com/sns/oauth2/component/refresh_token';


    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * 请求参数配置
     *
     * @param $params
     */
    public function setRequestParams($params)
    {
        $this->params = $params;
    }

    /**
     * 刷新第三方平台Access Token
     *
     *
     * @param $ticket
     * @return array|bool|mixed
     */
    public function componentAccessToken()
    {

        $ticket = $this->getRequestParams('componentAccessToken', 'verifyTicket');
        $params = $this->globalParams(['component_appid' => '', 'component_appsecret' => '']);
        $params['component_verify_ticket'] = $ticket;

        return ApiRequest::postRequest('componentAccessToken', $this->comAccTk, $params);
    }


    /**
     * 获取预授权码
     *
     * @return array|bool|mixed
     */
    public function preAuthCode()
    {
        $params = $this->globalParams(['component_appid' => '']);
        $comAccTk = $this->getRequestParams('componentAccessToken', 'componentAccessToken');

        $url = $this->repAccTk . '?component_access_token=' . $comAccTk;
        return ApiRequest::postRequest('preAuthCode', $url, $params);
    }

    /**
     * 生成授权链接
     *
     * @param $preAuthCode
     * @param $redirectUrl
     * @return string
     */
    public function createAuthUrl()
    {
        $params = $this->globalParams(['component_appid' => '']);

        $params['pre_auth_code'] = $this->getRequestParams('createAuthUrl', 'preAuthCode');
        $params['auth_type'] = 1;
        $params['redirect_uri'] = $this->config->get('open.auth_redirect', '');
        $query = http_build_query($params);

        if (!$params['redirect_uri']) {
            throw new \Exception('auth_redirect 不能为空', 40001);
        }

        return [
            'errcode' => 0,
            'authUrl' => $this->authUrl . '?' . $query
        ];
    }


    /**
     * 获取公众号授权信息
     *
     * @return array|bool|mixed
     */
    public function authAccessToken()
    {
        $comAccTk = $this->getRequestParams('authAccessToken', 'componentAccessToken');
        $params = $this->globalParams(['component_appid' => '']);
        $params['authorization_code'] = $this->getRequestParams('authAccessToken', 'authCode');

        $url = $this->authAccTk . '?component_access_token=' . $comAccTk;
        return ApiRequest::postRequest('authAccessToken', $url, $params);
    }

    /**
     * 刷新授权Access Token
     *
     * @return bool
     */
    public function refreshAuthAccessToken()
    {
        $comAccTk = $this->getRequestParams('refreshAuthAccessToken', 'componentAccessToken');
        $params = $this->globalParams(['component_appid' => '']);
        $params['authorizer_appid'] = $this->getRequestParams('refreshAuthAccessToken',
            'authAppId');
        $params['authorizer_refresh_token'] = $this->getRequestParams('refreshAuthAccessToken',
            'refreshToken');

        $url = $this->refAuthAccTk . '?component_access_token=' . $comAccTk;;
        return ApiRequest::postRequest('refreshAuthAccessToken', $url, $params);
    }

    /**
     * 获取授权方的帐号基本信息
     * @return array|bool|mixed
     */
    public function authorizeInfo()
    {
        $comAccTk = $this->getRequestParams('authorizeInfo', 'componentAccessToken');
        $params = $this->globalParams(['component_appid' => '']);
        $params['authorizer_appid'] = $this->getRequestParams('authorizeInfo', 'authAppId');

        $url = $this->authInfo . '?component_access_token=' . $comAccTk;
        return ApiRequest::postRequest('authAccessToken', $url, $params);
    }

    /**
     * 代公众号调用接口调用次数清零 API 的权限。
     * 每个公众号每个月有 10 次清零机会，包括在微信公众平台上的清零以及调用 API 进行清零
     *
     * @return array|bool|mixed
     * @throws \Exception
     */
    public function clearWeChatQuota()
    {
        $params['appid'] = $this->getRequestParams('clearWeChatQuota', 'appId');
        $accessToken = $this->getRequestParams('clearWeChatQuota', 'accessToken');

        $url = $this->clearQuota . '?access_token=' . $accessToken;
        return ApiRequest::postRequest('clearWeChatQuota', $url, $params);

    }

    /**
     * 第三方平台对其所有 API 调用次数清零（只与第三方平台相关，与公众号无关，接口如 api_component_token）
     *
     * @return array|bool|mixed
     * @throws \Exception
     */
    public function clearComponentQuota()
    {

        $accessToken = $this->getRequestParams('clearComponentQuota', 'componentAccessToken');
        $params = $this->globalParams(['component_appid' => '']);

        $url = $this->clearComponentQuota() . '?component_access_token=' . $accessToken;
        return ApiRequest::postRequest('clearComponentQuota', $url, $params);
    }

    /**
     * 创建第三方平台代理发送授权地址
     *
     * @return string
     */
    public function createWebAuthorizeUrl()
    {
        $appId = $this->getRequestParams('createWebAuthorizeUrl', 'appId');
        $redirectUrl = $this->getRequestParams('createWebAuthorizeUrl', 'redirectUrl');
        $scope = $this->getRequestParams('createWebAuthorizeUrl', 'scope');

        $params = [
            'appid' => $appId,
            'redirect_uri' => urlencode($redirectUrl),
            'response_type' => 'code',
            'scope' => $scope ?: 'snsapi_base',
            'state' => 'STATE',
            'component_appid' => $this->globalParams(['component_appid' => ''])['component_appid']
        ];

        $query = ApiRequest::convertUrlParams($params);

        return [
            'errcode' => 0,
            'authUrl' => $this->authorizeUrl . '?' . $query . '#wechat_redirect'
        ];
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
     */
    public function getWebAccessToken()
    {
        $appId = $this->getRequestParams('createWebAuthorizeUrl', 'appId');
        $code = $this->getRequestParams('createWebAuthorizeUrl', 'code');
        $componentAccessToken = $this->getRequestParams('createWebAuthorizeUrl',
            'componentAccessToken');

        $params = [
            'appid' => $appId,
            'code' => $code,
            'grant_type' => 'authorization_code',
            'component_appid' => $this->globalParams(['component_appid' => ''])['component_appid'],
            'component_access_token' => $componentAccessToken
        ];

        return ApiRequest::getRequest('createWebAuthorizeUrl', $this->webAuthorizeToken, $params);
    }

    /**
     * 刷新 access_token
     * 通过code获取访问公众号用户信息的access_code
     *
     */
    public function getWebAccessTokenRefresh()
    {
        $appId = $this->getRequestParams('createWebAuthorizeUrl', 'appId');
        $refreshToken = $this->getRequestParams('createWebAuthorizeUrl', 'refreshToken');
        $componentAccessToken = $this->getRequestParams('createWebAuthorizeUrl',
            'componentAccessToken');

        $params = [
            'appid' => $appId,
            'grant_type' => 'refresh_token',
            'component_appid' => $this->globalParams(['component_appid' => ''])['component_appid'],
            'component_access_token' => $componentAccessToken,
            'refresh_token' => $refreshToken
        ];

        return ApiRequest::getRequest('createWebAuthorizeUrl', $this->webAuthorizeTokenRefresh,
            $params);
    }
}