<?php
/**
 * 公众号第三方平台接口调用服务
 *
 */
namespace Open\Support\Apis;

use Open\LocalTraits\BasicProcess;
use Open\LocalTraits\ShareSetting;
use Open\LocalTraits\WebAuth;
use Open\Support\Config\Config;
use Open\Support\Request\ApiRequest;

class WeChatApi
{
    use BasicProcess,ShareSetting;

    protected $config;
    protected $params;

    //获取授权码链接
    protected $authCodeUrl = 'https://open.weixin.qq.com/connect/oauth2/authorize';

    //获取Access Token
    protected $webAccessToken = 'https://api.weixin.qq.com/sns/oauth2/access_token';

    //刷新Access Token
    protected $refreshAccessToken = 'https://api.weixin.qq.com/sns/oauth2/refresh_token';

    //获取用户信息
    protected $userInfo = "https://api.weixin.qq.com/sns/userinfo";

    //获取公众号基础Access Token
    protected $basicAccessToken = "https://api.weixin.qq.com/cgi-bin/token";

    //js-sdk调用凭证jsapi ticket
    protected $jsApiTicket = "https://api.weixin.qq.com/cgi-bin/ticket/getticket";

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
     * 获取授权地址
     *
     * @return array
     * @throws \Exception
     */
    public function getAuthCodeUrl()
    {
        $params = [
            'appid' => $this->getRequestParams('getAuthCodeUrl','appId'),
            'redirect_uri' => urlencode($this->getRequestParams('getAuthCodeUrl','redirectUri')),
            'response_type' => $this->getRequestParams('getAuthCodeUrl','responseType')?:'code',
            'scope' => $this->getRequestParams('getAuthCodeUrl','scope')?:'snsapi_base',
            'useProxy' => intval($this->getRequestParams('getAuthCodeUrl','useProxy')),
        ];

        if ($params['useProxy'] == 1) {
            $params["state"] = "STATE";
            $authUrl = 'https://www.juhe.cn/weixin/proxy/auth?';
        } else {
            $params['state'] = "STATE#wechat_redirect";
            $authUrl = $this->authCodeUrl.'?';
        }
        unset($params['useProxy']);
        $bizString = ApiRequest::convertUrlParams($params);
        //这里对授权回调进行了代理处理，因为一个公众号只能添加一个回调域名，所以做一个代理
        return [
            "errcode" => 0,
            "authUrl" => $authUrl . $bizString
        ];
    }

    /**
     * 获取用户的access token 和OpenId
     */
    public function getAccessTokenOpenId()
    {
        $params = [
            'appid' => $this->getRequestParams('getUserInfo','appId'),
            'secret' => $this->getRequestParams('getUserInfo','secret'),
            'code' => $this->getRequestParams('getUserInfo','code'),
            'grant_type' => 'authorization_code'
        ];
        return ApiRequest::getRequest('getOpenId',$this->webAccessToken,$params);
    }

    /**
     * 刷新网页授权Access Token
     *
     * @return array|bool|mixed
     * @throws \Exception
     */
    public function refreshAccessToken()
    {
        $params = [
            'appid' => $this->getRequestParams('getUserInfo','appId'),
            'refresh_token' => $this->getRequestParams('getUserInfo','refreshToken'),
            'grant_type' => 'refresh_token'
        ];
        return ApiRequest::getRequest('getOpenId',$this->refreshAccessToken,$params);
    }

    /**
     * 获取用户信息
     *
     * @return array|mixed
     * @throws \Exception
     */
    public function getUserInfo()
    {
        $params = [
            'appid' => $this->getRequestParams('getUserInfo','appId'),
            'openid' => $this->getRequestParams('getUserInfo','openId'),
            'access_token' => $this->getRequestParams('getUserInfo','accessToken'),
            'lang' => 'zh_CN',
        ];

        return ApiRequest::getRequest('getUserInfo',$this->userInfo,$params);
    }


    /**
     * 获取分享配置
     *
     * @return array|bool|mixed|string
     * @throws \Exception
     */
    public function getShareSetting()
    {
        $params = [
            'jsapi_ticket' => $this->getRequestParams('getShareSetting','jsApiTicket'),
            'sign_url' => $this->getRequestParams('getShareSetting','signUrl'),
        ];

        return $this->getWeChatShareSetting($params);
    }

    /**
     * 获取公众号基础access token
     *
     * @return array|bool|mixed
     * @throws \Exception
     */
    public function getBasicAccessToken()
    {
        $params = [
            'appid' => $this->getRequestParams('getBasicAccessToken','appId'),
            'secret' => $this->getRequestParams('getBasicAccessToken','secret'),
            'grant_type' => 'client_credential',
        ];

        return ApiRequest::getRequest('getBasicAccessToken',$this->basicAccessToken,$params);
    }

    /**
     * 获取JS-SDK Ticket
     *
     * @return array|bool|mixed
     * @throws \Exception
     */
    public function getJsTicketToken()
    {
        $params = [
            'access_token' => $this->getRequestParams('getJsTicketToken','accessToken'),
            'type' => 'jsapi',
        ];

        return ApiRequest::getRequest('getJsTicketToken',$this->jsApiTicket,$params);
    }

}