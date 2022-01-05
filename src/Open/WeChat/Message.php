<?php

namespace Open\WeChat;

use Open\BaseProcess;
use Open\WeChat\Response\MsgResponse;

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
class Message extends BaseProcess
{
    /**
     * 将消息处理为数组格式，如果需要解密处理，将解密结果数组返回
     *
     * 消息：
     * msgType :text,image,voice,video,shortvideo,location,link
     * 事件：
     * msgType :event
     * Event:subscribe关注，
     *      EventKey：qrscene_123123 扫码关注，
     *
     * Event：SCAN
     *      EventKey：SCENE_VALUE 扫码已关注
     *
     * Event：LOCATION 上报地理位置后微信推送
     *
     * //自定义菜单时候的事件推送
     * Event:CLICK 菜单点击事件
     *      EventKey：EVENTKEY 菜单设置的key
     *
     * Event:VIEW 菜单链接点击事件
     *      EventKey：www.qq.com 菜单设置的链接地址
     *
     * Event:CLICK 菜单点击事件
     *      EventKey：EVENTKEY 菜单设置的key
     *
     * Event:CLICK 菜单点击事件
     *      EventKey：EVENTKEY 菜单设置的key
     *
     *
     *
     *
     * @return string
     */
    public function messageContent()
    {
        $content = $this->dataFromXml();
        if(isset($content['Encrypt'])) {
            return $this->decryptContent($content['Encrypt']);
        }
        return $content;
    }

    /**
     * 公众号开发者服务校验
     *
     * @return mixed|string
     */
    public function validate()
    {
        return $this->serverValidate();
    }

    /**
     * @param $params
     * 被动回复：
     * MsgType:text,image,voice,video,music,news
     *  [
     *      'MsgType' => 'text|event',
     *      'data' => [
     *          'encrypt' => true,
     *          'FromUserName' => 'FromUserName',
     *          'ToUserName' => 'ToUserName',
     *          'title' => 'title',
     *          'description' => 'description',
     *          'content' => 'content',
     *          'url' => 'url'
     *          'media_id' => 'media_id',
     *          'news' => [
     *              [
     *                  'title' => 'title',
     *                  'description' => 'description',
     *                  'picurl' => 'picurl',
     *                  'url' => 'url',
     *              ]
 *              ]
     *      ],
     * ]
     *
     * @return string
     */
    public function response($params)
    {
        $response = new MsgResponse($this->config,$params);
        return $response->convertResponse($this->crypt);

    }
}
