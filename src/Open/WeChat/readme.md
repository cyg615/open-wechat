###公众号开发消息处理

####一、服务器校验
```
    通用配置
    $config = [
        'open' => [
            'app_id' => 'wx3c7ae204**', //公众号appid
            'app_secret' => '52527e***a3acb55',//公众号appsecret
            'token' => 'op***', //公众号Token
            'encoding_aes_key' => '**a356e5949d', //数据处理秘钥（加密解密）
        ],
        'log' => [//日志
            'file' => './Logs/log.log',
            'level' => 'debug',
            'type' => 'daily',
            'max_file' => 30
        ]
    ];
```
#####1，服务校验
```

        $message = new Message($config);
        return $message->validate();
        校验成功，返回随机码，失败返回空字符

```
####二、第三方平台或公众号开发者，消息公用处理
#####1，消息处理
```
    
    $message= new Message($this->config);
    $content = $message->messageContent();
    
    $content格式：
    [
        "ToUserName" => "gh_bb13458fe0d8",
        "FromUserName" => "o2GWBt5LRdC9VRFiJlIIhQQzXAMw",
        "CreateTime" => "1561446884",
        "MsgType" => "event",
        "Event" => "unsubscribe",
        "EventKey" => [],
    ]
    
```


#####2，消息被动回复

   | MsgType | 说明 | 参数 | 返回值 |
    | :---: | :---: | :---: | :---: |
    | text  | 回复文本消息 | [content] | string |
    | link  | 回复链接图文 | [title,description,url] | string |
    | image | 回复图片消息 | [media_id] | string |
    | voice | 回复语音消息 | [media_id] | string |
    | video | 回复视频消息 | [media_id,title,description] | string |
    | music | 回复音乐消息 | [media_id,title,description,hq_music_url,music_url] | string |
    | news  | 回复图文消息 | [news] | string |
    
```
    参数根据上面按需传递，除了前面四个必传参数
    $params = [
                'encrypt' => false,
                'FromUserName' => 'FromUserName',
                'ToUserName' => 'ToUserName',
                'MsgType' => 'text',
    
                'title' => 'title',
                'description' => 'description',
                'content' => 'content',
                'url' => 'url',
                'media_id' => 'media_id',
                'hq_music_url' => 'hq_music_url',
                'music_url' => 'music_url',
                'news' => [
                    [
                        'title' => 'title',
                        'description' => 'description',
                        'picurl' => 'picurl',
                        'url' => 'url',
                    ]
                ]
            ];
            
            $response = new Message($this->config);
            $content = $response->response($params);//直接输出
```
