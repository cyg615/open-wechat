<?php

namespace Open\Support\Crypt;

use Open\Support\Config\Config;

/**
 * 对公众平台发送给公众账号的消息加解密示例代码.
 *
 * @copyright Copyright (c) 1998-2014 Tencent Inc.
 */

/**
 * 1.第三方回复加密消息给公众平台；
 * 2.第三方收到公众平台发送的消息，验证消息的安全性，并对消息进行解密。
 */
class Crypt
{
    private $token;
    private $encodingAesKey;
    private $appId;
    private $viKey;


    /**
     * 构造函数
     *
     * Crypt constructor.
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
        $this->token = $config->get('open.token');
        $this->encodingAesKey = $config->get('open.encoding_aes_key');
        $this->appId = $config->get('open.app_id');
        $this->viKey = $config->get('open.vi');
    }

    /**
     * xml转换成数组或其他对象
     *
     * @param $content
     * @return mixed
     */
    public function dataFromXml($content)
    {
        libxml_disable_entity_loader(true);

        $xml = simplexml_load_string($content, 'SimpleXMLElement', LIBXML_NOCDATA);

        return json_decode(json_encode($xml, JSON_UNESCAPED_UNICODE), true);
    }

    /**
     * 将公众平台回复用户的消息加密打包.
     * <ol>
     *    <li>对要发送的消息进行AES-CBC加密</li>
     *    <li>生成安全签名</li>
     *    <li>将消息密文和安全签名打包成xml格式</li>
     * </ol>
     *
     * @param $replyMsg string 公众平台待回复用户的消息，xml格式的字符串
     * @param $timeStamp string 时间戳，可以自己生成，也可以用URL参数的timestamp
     * @param $nonce string 随机串，可以自己生成，也可以用URL参数的nonce
     * @param &$encryptMsg string 加密后的可以直接回复用户的密文，包括msg_signature, timestamp, nonce, encrypt的xml格式的字符串,
     *                      当return返回0时有效
     *
     * @return int 成功0，失败返回对应的错误码
     */
    public function encryptMsg($replyMsg, $timeStamp, $nonce)
    {
        //加密
        $encryptMsg = PrpCrypt::encrypt($replyMsg, $this->appId, $this->viKey);
        if ($encryptMsg['errcode'] != '0') {
            return $encryptMsg;
        }

        if ($timeStamp == null) {
            $timeStamp = time();
        }

        //生成安全签名
        $signature = SHA1::getSHA1($this->token, $timeStamp, $nonce, $encryptMsg['encrypt']);
        if ($signature['errcode'] != '0') {
            return $signature;
        }

        return [
            'errcode' => 0,
            'encrypt' => $encryptMsg['encrypt'],
            'signature' => $signature['sha1']
        ];
    }


    /**
     * 检验消息的真实性，并且获取解密后的明文.
     * <ol>
     *    <li>利用收到的密文生成安全签名，进行签名验证</li>
     *    <li>若验证通过，则提取xml中的加密消息</li>
     *    <li>对消息进行解密</li>
     * </ol>
     *
     * @param $msgSignature string 签名串，对应URL参数的msg_signature
     * @param $timestamp string 时间戳 对应URL参数的timestamp
     * @param $nonce string 随机串，对应URL参数的nonce
     * @param $postData string 密文，对应POST请求的数据
     * @param &$msg string 解密后的原文，当return返回0时有效
     *
     * @return int 成功0，失败返回对应的错误码
     */
    public function decryptMsg($msgSignature, $timestamp, $nonce, $encrypt)
    {
        if (strlen($this->encodingAesKey) != 43) {
            return ErrorCode::$IllegalAesKey;
        }

        //验证安全签名
        $signature = SHA1::getSHA1($this->token, $timestamp, $nonce, $encrypt);
        if ($signature['errcode'] != '0') {
            return $signature;
        }

        if ($signature['sha1'] != $msgSignature) {
            return ErrorCode::$ValidateSignatureError;
        }

        return PrpCrypt::decrypt($encrypt, $this->appId, $this->viKey);
    }


}

