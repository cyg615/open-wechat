<?php
/**
 * Created by PhpStorm.
 * User: owner
 * Date: 2019-06-25
 * Time: 17:43
 * Project Name: openWeChat
 */

namespace Open\WeChat\Response;


use Open\Support\Crypt\Crypt;
use Open\Support\Crypt\PrpCrypt;

class MsgResponse
{
    protected $config;
    protected $params;

    public function __construct($config,$params)
    {
        $this->config = $config;
        $this->params = $params;
    }

    /**
     * 消息回复
     *
     * @param Crypt $crypt
     * @return string
     */
    public function convertResponse(Crypt $crypt)
    {
        $msgType = strtolower($this->params['MsgType']);
        $contents = '';
        if (method_exists($this, $msgType . 'Content')) {
            $contents = $this->{$msgType . 'Content'}();
        }
        $timestamp = time();

        $content = '<xml>' .
            '<ToUserName><![CDATA[' . $this->params['FromUserName'] . ']]></ToUserName>' .
            '<FromUserName><![CDATA[' . $this->params['ToUserName'] . ']]></FromUserName>' .
            '<CreateTime>' . $timestamp . '</CreateTime>' .
            '<MsgType><![CDATA[' . $this->params['MsgType'] . ']]></MsgType>' .
            $contents .
            '</xml>';

        //加密密文
        if(isset($this->params['encrypt']) && $this->params['encrypt']) {
            $nonce =PrpCrypt::getRandomStr();
            $encrypt = $crypt->encryptMsg($content,$timestamp,$nonce);
            if($encrypt['errcode'] == '0') {
                return $this->generate($encrypt['encrypt'], $encrypt['signature'], $timestamp, $nonce);
            } else {
                return $encrypt['errmsg'];
            }
        }

        return $content;
    }



    /**
     * 生成xml消息
     * @param  $encrypt 加密后的消息密文
     * @param  $signature 安全签名
     * @param  $timestamp 时间戳
     * @param  $nonce 随机字符串
     *
     * @return string
     */
    public function generate($encrypt, $signature, $timestamp, $nonce)
    {
        $format = "<xml>
            <Encrypt><![CDATA[%s]]></Encrypt>
            <MsgSignature><![CDATA[%s]]></MsgSignature>
            <TimeStamp>%s</TimeStamp>
            <Nonce><![CDATA[%s]]></Nonce>
            </xml>";
        return sprintf($format, $encrypt, $signature, $timestamp, $nonce);
    }

    /**
     * 文本消息组装
     * @return string
     */
    public function textContent()
    {
        return '<Content><![CDATA[' . $this->params['content'] . ']]></Content>';
    }

    /**
     * 文本消息组装
     * @return string
     */
    public function linkContent()
    {
        return '<Title><![CDATA[' . $this->params['title'] . ']]></Title>
                <Description><![CDATA[' . $this->params['description'] . ']]></Description>
                <Url><![CDATA[' . $this->params['url'] . ']]></Url>';
    }

    /**
     * 处理图片
     * @return string
     */
    public function imageContent()
    {
        return '<Image><MediaId><![CDATA[' . $this->params['media_id'] . ']]></MediaId></Image>';
    }

    /**
     * 语音图片
     * @return string
     */
    public function voiceContent()
    {
        return '<Voice><MediaId><![CDATA[' . $this->params['media_id'] . ']]></MediaId></Voice>';
    }


    /**
     * 语音图片
     * @return string
     */
    public function videoContent()
    {
        return '<Video>
                <MediaId><![CDATA[' . $this->params['media_id'] . ']]></MediaId>
                <Title><![CDATA[' . $this->params['title'] . ']]></Title>
                <Description><![CDATA[' . $this->params['description'] . ']]></Description>
                </Video> ';
    }

    /**
     * 语音图片
     * @return string
     */
    public function musicContent()
    {
        return '<Music>
                <ThumbMediaId><![CDATA[' . $this->params['media_id'] . ']]></ThumbMediaId>
                <Title><![CDATA[' . $this->params['title'] . ']]></Title>
                <Description><![CDATA[' . $this->params['description'] . ']]></Description>
                <HQMusicUrl><![CDATA[' . $this->params['hq_music_url'] . ']]></HQMusicUrl>
                <MusicUrl><![CDATA[' . $this->params['music_url'] . ']]></MusicUrl>
                </Music> ';
    }
    /**
     *
     * 回复图文信息
     *
     *
     * @return mixed
     */
    public function newsContent()
    {
        $news = $this->params['news'];
        $new = '<ArticleCount>' . count($news) . '</ArticleCount>';
        $new .= '<Articles>';
        foreach ($news as $item) {
            $new .= '<item>
                        <Title><![CDATA[' . $item['title'] . ']]></Title> 
                        <Description><![CDATA[' . $item['description'] . ']]></Description>
                        <PicUrl><![CDATA[' . $item['picurl'] . ']]></PicUrl>
                        <Url><![CDATA[' . $item['url'] . ']]></Url>
                    </item>';
        }
        $new .= '</Articles>';
        return $new;
    }
}