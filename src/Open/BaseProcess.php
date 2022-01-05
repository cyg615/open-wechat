<?php

namespace Open;

use Exception;
use Open\Support\Config\Config;
use Open\Support\Crypt\Crypt;
use Open\Support\Crypt\SHA1;
use Open\Support\Log\Log;
use Symfony\Component\HttpFoundation\Request;

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
class BaseProcess
{
    protected $config;
    protected $crypt;

    /**
     *
     *
     * BaseProcess constructor.
     *
     * BaseProcess constructor.
     * @param $config
     */
    public function __construct($config)
    {
        try{
            $this->config = new Config($config);
            $this->registerLogService();
            $this->config->set('open.vi',base64_decode($this->config->get('open.encoding_aes_key') . "=")) ;
            $this->crypt = new Crypt($this->config);

        } catch (Exception $exception) {
        }

    }

    /**
     * 获取原始数据
     */
    public function getOriginContent()
    {
        return Request::createFromGlobals()->getContent();
    }

    /**
     * 获取原始XML数据
     *
     * @return mixed
     */
    protected function dataFromXml()
    {
        $orContent = $this->getOriginContent();
        return $this->crypt->dataFromXml($orContent);
    }

    /**
     * 获取query参数
     *
     * @return array
     */
    public function getQuery()
    {
        $request = Request::createFromGlobals()->query;

        $msgSignature = $request->get('msg_signature');
        $timestamp = $request->get('timestamp');
        $nonce = $request->get('nonce');
        $sign = $request->get('signature');
        $encryptType = $request->get('encrypt_type');

        return  [
            'msg_signature' => $msgSignature,
            'timestamp' => $timestamp,
            'nonce' => $nonce,
            'signature' => $sign,
            'encrypt_type' => $encryptType,
        ];
    }

    /**
     * 解密数据
     *
     *
     * @param $content
     * @return int
     */
    protected function decryptContent($content)
    {
        $signInfo = $this->getQuery();
        $decrypt = $this->crypt->decryptMsg($signInfo['msg_signature'], $signInfo['timestamp'], $signInfo['nonce'], $content);
        if($decrypt['errcode'] == 0){
            Log::debug('decryptContent',$signInfo);
            return $this->crypt->dataFromXml($decrypt['decrypt']);
        } else {
            Log::error('decryptContent',array_merge($decrypt,$signInfo));
            return $decrypt;
        }
    }

    /**
     * 公众号开发者，校验服务器配置
     *
     * @return mixed|string
     */
    protected function serverValidate()
    {
        $request = Request::createFromGlobals()->request;
        $signature = $request->get('signature');
        $timestamp = $request->get('timestamp');
        $nonce = $request->get('nonce');

        $sign = SHA1::getSHA1($this->config->get('open.token'), $timestamp, $nonce, '');
        if ($sign['errcode'] == '0' && $signature == $sign['sha1']) {
            return $request->get('echostr');
        } else {
            return '';
        }
    }

    /**
     * Register log service.
     *
     * @author yansongda <me@yansongda.cn>
     *
     * @throws Exception
     */
    protected function registerLogService()
    {
        $logger = Log::createLogger(
            $this->config->get('log.file'),
            'open.wechat',
            $this->config->get('log.level', 'warning'),
            $this->config->get('log.type', 'daily'),
            $this->config->get('log.max_file', 30)
        );

        Log::setLogger($logger);
    }
}
