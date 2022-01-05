<?php

namespace Open\Support\Crypt;

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
class ErrorCode
{

    public static $OK = ['errcode' => 0, 'errmsg' => '成功'];
    public static $ValidateSignatureError = ['errcode' => 40001, 'errmsg' => '签名验证错误'];
    public static $ParseXmlError = ['errcode' => 40002, 'errmsg' => 'xml解析失败'];
    public static $ComputeSignatureError = ['errcode' => 40003, 'errmsg' => 'sha加密生成签名失败'];
    public static $IllegalAesKey = ['errcode' => 40004, 'errmsg' => 'encodingAesKey 非法'];
    public static $ValidateAppidError = ['errcode' => 40005, 'errmsg' => 'appid 校验错误'];
    public static $EncryptAESError = ['errcode' => 40006, 'errmsg' => 'aes 加密失败'];
    public static $DecryptAESError = ['errcode' => 40007, 'errmsg' => '解密失败'];
    public static $IllegalBuffer = ['errcode' => 40008, 'errmsg' => '解密后得到的buffer非法'];
    public static $EncodeBase64Error = ['errcode' => 40009, 'errmsg' => 'base64加密失败'];
    public static $DecodeBase64Error = ['errcode' => 40010, 'errmsg' => 'base64解密失败'];
    public static $GenReturnXmlError = ['errcode' => 40011, 'errmsg' => '生成xml失败'];
    public static $PKCS7EncoderError = ['errcode' => 40012, 'errmsg' => 'PKCS7Encoder解析错误'];

}
