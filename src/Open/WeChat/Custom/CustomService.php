<?php
//客服服务
namespace Open\WeChat\Custom;

use Open\Support\Request\ApiRequest;

/**
 * error code 说明.
 * <ul>
 *    <li> 40001: 签名验证错误</li>
 *    <li> 40002: xml解析失败</li>
 *    <li> 40003: sha加密生成签名失败</li>
 *    <li> 40004: encodingAesKey 非法</li>
 *    <li> 40005: appid 校验错误</li>
 *    <li> 40006: aes 加密失败</li>
 *    <li> 40007: aes 解密失败</li>
 *    <li> 40008: 解密后得到的buffer非法</li>
 *    <li> 40009: base64加密失败</li>
 *    <li> 40010: base64解密失败</li>
 *    <li> 40011: 生成xml失败</li>
 * </ul>
 */
class CustomService
{
    const URL = 'https://api.weixin.qq.com/cgi-bin/message/custom/send';

    const TEXT1 = 1;
    const TEXT2 = 1;

    protected $accessToken;

    public function __construct($accessToken)
    {
        $this->accessToken = $accessToken;
    }

    /**
     * 获取客服接口url
     *
     * @access private
     * @return string url
     */
    private function getUrl(){
        return self::URL.'?'.'access_token='.$this->accessToken;
    }

    /**
     * 客服接口-发消息
     *
     * @param $toUserName
     * @param $message
     * @param int $message_type
     * @return mixed
     */
    public function sendMessage($toUserName,$message,$message_type = self::TEXT1){
        $url = $this->getUrl();
        switch ($message_type) {
            case self::TEXT1:
                $post = [
                    'touser' => $toUserName,
                    'msgtype' => 'text',
                    'text' => $message
                ];
                break;
            default:
                break;
        }

        return ApiRequest::postRequest('customService',$url,$post);
    }

}
