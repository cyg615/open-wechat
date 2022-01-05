###公众号第三方平台开发

####一、公众号第三方平台授权相关
```
    通用配置
    $config = [
        'open' => [
            'app_id' => 'wx3c7ae204**', //第三方平台appid
            'app_secret' => '52527e***a3acb55',//第三方平台appsecret
            'token' => 'op***', //第三方平台Token
            'encoding_aes_key' => '**a356e5949d', //数据处理秘钥（加密解密）
            'auth_redirect' => 'http://domain/open/auth/redirect',//授权回调地址
            'auth_page' => 'http://domain/', //授权发起页面地址（拥有回调成功后页面刷新）
        ],
         //公众号信息
        'weChat' => [
            'app_id' => 'wxcdc43d8**045c',
            'app_secret' => '95f15f6e553***e3fa8',
            'token' => '86ce8e1bddbf**c3eea0',
            'redirect_uri' => env('APP_URL').'/',
            'response_type' => 'code',
            'scope' => 'snsapi_base',
            'use_proxy' => 1,
        ],
        'log' => [//日志
            'file' => './Logs/log.log',
            'level' => 'debug',
            'type' => 'daily',
            'max_file' => 30
        ]
    ];
```
#####1，微信Verify Ticket处理（用于接收取消授权通通知、授权更新通知，也用于接收ticket，ticket是验证平台方的重要凭据。）
```
        注：verifyTicke微信服务器每隔10分机进行推送一次，请妥善保存verifyTicke
        

        $ticket = new Ticket($config);
        $verifyTicket = $ticket->verifyTicket();
        
        $verifyTicket格式：
        [
            "AppId" => "wx3c7ae2***",
            "CreateTime" => "1561347917",
            "InfoType" => "component_verify_ticket",
            "ComponentVerifyTicket" => "ticket@@@**RLZvrPHLD0AJq7oT_nqDrpa_xhO40sY08P77A",
        ]

```

#####2，通过Verify Ticket 获取或刷新Component Access Token
```
    注：component_access_token:是第三方平台的下文中接口的调用凭据，也叫做令牌（component_access_token）。
    每个令牌是存在有效期（2小时）的，且令牌的调用不是无限制的，请第三方平台做好令牌的管理，
    在令牌快过期时（比如1小时50分）再进行刷新
    
    $response = OpenApiServer::componentAccessToken($verifyTicket)
    $response格式：
    [
        "errcode" => 0, //errcode 不为0的时候说明接口请求出错了，详细看errmsg
        "expires_in" => 7200,
        "component_access_token" => "oOm6bXeEVzYJed0eOXlI",
    ]
    
```


#####3，获取Pre Auth Code预授权码
```
    注：pre_auth_code 预授权码用于公众号或小程序授权时的第三方平台方安全验证。
    
    $response = OpenApiServer::preAuthCode($componentAccessToken)
    response格式：
    [
        "errcode" => 0, //errcode 不为0的时候说明接口请求出错了，详细看errmsg
        "expires_in" => 1800,
        "pre_auth_code" => "preauthcode@@@6Kf8G",
    ]
    
```

####二、第三方平台授权地址生成

```
    注：pre_auth_code 预授权码用于公众号或小程序授权时的第三方平台方安全验证。
    $response = OpenApiServer::createAuthUrl($preAuthCode)
    
    $response格式：
    [
        "errcode" => 0, //errcode 不为0的时候说明接口请求出错了，详细看errmsg
        "authUrl" => "https://mp.weixin.qq.com/cgi-bin/componentloginpage?component_appid=wx3c7a**&pre_auth_code=preauthcode-_WZYBLfHQZC8mrYCrhdlNSrNM_CX2BJP5F&auth_type=1&redirect_uri=%2Fopen%2Fauth%2Fredirect",
    ]
    
```

####三、授权公众号相关消息处理

#####1、获取公众号授权信息包括Access Token
```
        请求access token 的接口有调用次数限制，所以请妥善保管和处理好access token,全局处理
        注：请妥善保管access_token(有效期2小时)和refresh_access_token，当access_token将要过期的时候可以通过refresh_access_tokek
        刷新token,请提前几分钟刷新token
        
        $response = OpenApiServer::authAccessToken($componentAccessToken,$authCode)

        $response返回结构：(参数格式等同于开发文档)
        [
            "errcode" => 0, //errcode 不为0的时候说明接口请求出错了，详细看errmsg
            "authorization_info" => [
                "authorizer_appid" => "appId",
                "authorizer_access_token" => "authorizer_access_token",
                "expires_in" => 7200,
                "authorizer_refresh_token" => "authorizer_refresh_token",
                "func_info" => [
                    ["funcscope_category=>["id":1]],
                    ["funcscope_category=>["id":2]],
                    ["funcscope_category=>["id":3]],
                ]
                
            ]
        ]
```

#####2、刷新公众号的Access Token
```
        注：请妥善保管access_token(有效期2小时)和refresh_access_token，当access_token将要过期的时候可以通过refresh_access_tokek
        刷新token,请提前几分钟刷新token
        
        $response = OpenApiServer::refreshAuthAccessToken($componentAccessToken,$authAppId,$refreshToken)
    
        $response返回结构：(参数格式等同于开发文档)
        [
            "errcode" => 0, //errcode 不为0的时候说明接口请求出错了，详细看errmsg
            "authorizer_access_token" => "authorizer_access_token",
            "expires_in" => 7200,
            "authorizer_refresh_token" => "authorizer_refresh_token",
        ]
```

#####3、获取授权公众号信息
```
        $response = OpenApiServer::authorizeInfo($componentAccessToken,$authAppId)

        $response返回结构：(参数格式等同于开发文档)
        [
            "errcode" => 0, //errcode 不为0的时候说明接口请求出错了，详细看errmsg
            "authorizer_info" => [
                "nick_name"=> "微信SDK Demo Special", 
                "head_img"=> "http://wx.qlogo.cn/mmopen/GPy", 
                "service_type_info"=> [ "id"=> 2 ], 
                "verify_type_info"=> [ "id"=> 0 ],
                "user_name"=>"gh_eb5e3a772040",
                "principal_name"=>"腾讯计算机系统有限公司",
                "business_info"=> [
                    "open_store"=> 0, 
                    "open_scan"=> 0, 
                    "open_pay"=> 0, 
                    "open_card"=> 0, 
                    "open_shake"=> 0
                ],
                "alias"=>"paytest01"
                "qrcode_url"=>"URL",
            ]
            "authorization_info" => [
                "authorizer_appid" => "appId",
                "func_info" => [
                   ["funcscope_category=>["id":1]],
                   ["funcscope_category=>["id":2]],
                   ["funcscope_category=>["id":3]],
                ]
                
            ]
        ]
```

#####4、代替公众号调用接口

```
    //第三方平台根据授权的公众号，获取授权码（access_token）,代替公众号调用接口，
    前提条件，公众号需要有接口权限，同时需要授权给第三方平台
    示例：
    
    //1，代公众号调用接口调用次数清零 API 的权限。
    $response = OpenApiServer::clearWeChatQuota($appId,$accessToken)
    $response格式：
    [
        "errcode" => 0, //errcode 不为0的时候说明接口请求出错了，详细看errmsg
        "errmsg" => "ok",
    ]
    //2，清零第三方平台接口调用次数。
    $response = OpenApiServer::clearComponentQuota($componentAccessToken);
    $response格式：
    [
        "errcode" => 0, //errcode 不为0的时候说明接口请求出错了，详细看errmsg
        "errmsg" => "ok",
    ]
```
######4.1、代替公众号发起网页授权,;根据access token和openid 获取用户微信信息

```
    //1，获取授权地址。
    $response = OpenApiServer::getWebAuthorizeUrl($appId, $redirectUrl, $scope);
    $response格式：
    [
        "errcode" => 0, //errcode 不为0的时候说明接口请求出错了，详细看errmsg
        "authUrl" => "http://....",
    ]
    
    //2，根据授权地址回调返回的CODE，获取用户的openid和access token。
    
    $response = OpenApiServer::getWebAuthorizeUrl($appId,$code,$componentAccessToken);
    $response :
    {
        "errcode" => 0,
        "access_token"=>  "ACCESS_TOKEN",
        "expires_in"=>  7200,
        "refresh_token"=>  "REFRESH_TOKEN",
        "openid"=>  "OPENID",
        "scope"=>  "SCOPE"
    }
    
    //3，刷新用户信息获取的access_token
    $response = OpenApiServer::getWebAccessTokenRefresh($appId,$code,$componentAccessToken);
    $response :
    {
        "errcode" => 0,
        "access_token"=>  "ACCESS_TOKEN",
        "expires_in"=>  7200,
        "refresh_token"=>  "REFRESH_TOKEN",
        "openid"=>  "OPENID",
        "scope"=>  "SCOPE"
    }
```

###公众相关调用
```
    1，通过access_token 和openID 获取用户信息
    2，通过公众号授权的access token调用公众号相关的接口（群发，自动回复，自定义菜单等等）
    
    
    预计接口：https://i.mp.fkw.com/?openSourceId=303#/template-message
    1，消息模板
    2，自动回复
    3，自定菜单
    4，粉丝列表
    5，资源库管理
```
公众号开发
```
    1,网页授权
    2,获取openID
    3,获取用户信息
    4,获取基础access token
    5,获取jsapi ticket
    6,获取分享配置

```
### 1,网页授权
```
    //1,获取授权地址，跳转活动code
    $response = WeChatApiServer::getAuthCodeUrl();
    $response :
    [
        "errcode" => 0,
        "authUrl" => "http://....."
    ]
    
    //2,根据code获取access Token 和 用户openId
    $response = WeChatApiServer::getOpenIdAccessToken($code);
    $response :
        [
            "errcode" => 0
            "access_token" =>"ACCESS_TOKEN",
            "expires_in" =>7200,
            "refresh_token" =>"REFRESH_TOKEN",
            "openid" =>"OPENID",
            "scope" =>"SCOPE"
        ]
        
    //3,刷新AccessToken
    $response = WeChatApiServer::refreshAccessToken($refresToken);
    $response :
    [
        "errcode" => 0
        "access_token" =>"ACCESS_TOKEN",
        "expires_in" =>7200,
        "refresh_token" =>"REFRESH_TOKEN",
        "openid" =>"OPENID",
        "scope" =>"SCOPE"
    ]
    
    //4,根据Access Token 和OpenId 获取用户信息
    $response = WeChatApiServer::getUserInfo($openId,$accessToken)
    $response :
    [
        "errcode" => 0
        "openid" => " OPENID",
        " nickname" => NICKNAME,
        "sex" => "1",
        "province" => "PROVINCE"
        "city" => "CITY",
        "country" => "COUNTRY",
        "headimgurl" => "http://thirdwx.qlogo.cn/mmopen/g3MonUZtNHkdmzicIlibx6iaFqAc56vxLSUfpb6n5WKSYVY0ChQKkiaJSgQ1dZuTOgvLLrhJbERQQ4eMsv84eavHiaiceqxibJxCfHe/46",
        "privilege" => [ "PRIVILEGE1" "PRIVILEGE2"     ],
        "unionid" => "o6_bmasdasdsad6_2sgVt7hMZOPfL"
    ]
```

###2,获取基础AccessToken 和jsApiTicket
```
    //1,获取基础Access Token(全局保存，2小时有效期，提前刷新)
    $response = WeChatApiServer::getBasicAccessToken()
    $response ：
    [
        "errcode" => 0
        "access_token" => "ACCESS_TOKEN",
        "expires_in" => 7200
    ]
    
    //2,获取jsApiTicket(全局保存，2小时有效期，提前刷新)
    $response = WeChatApiServer::getJsApiTicket($accessToken)
    $response ：
    [
        "errcode" => 0,
        "errmsg" => "ok",
        "ticket" => "bxLdikRXVbTPdHSM05e5u5sUoXNKd8-41ZO3MhKoyN5OfkWITDGgnr2fwJ0m9E8NYzWKVZvdVtaUgWvsdshFKA",
        "expires_in" => 7200
    ]
    
    //3,根据jsApiTicket获取分享配置
    $response = WeChatApiServer::getShareSetting($jsApiTicket,$shareUrl)
    $response ：
    [
        "errcode" => 0,
        'timeStamp' => time(),
        'nonceStr' => 'ddddd',
        'signUrl' => $shareUrl,
        'signature' => $signature,
    ]
```