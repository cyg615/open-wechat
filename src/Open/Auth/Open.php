<?php
/**
 * Created by PhpStorm.
 * User: owner
 * Date: 2019-06-25
 * Time: 10:19
 * Project Name: openWeChat
 */

namespace Open\Auth;


use Exception;
use Open\Support\Apis\OpenApi;
use Open\Support\Log\Log;
use Open\Support\Config\Config;


/**
 * @method array setRequestParams($params) 设置请求参数
 * @method array componentAccessToken() 平台接口条用凭证获取：参数verifyTicket该token2小时有效期，存储并提前通过verifyTicket刷新
 * @method array preAuthCode()
 * @method array createAuthUrl()
 * @method array authAccessToken()
 * @method array refreshAuthAccessToken()
 * @method array authorizeInfo()
 */
class Open
{
    protected $api;

    /**
     * OpenAuth constructor.
     * @param array $config [
     * 'open' =>
     *      'appId' => 'appId',
     *      'token' => 'token',
     *      'secret' => 'secret',
     *      'msgSecret' => 'msgSecret',
     *      'authRedirect' => 'authRedirect',
     *      'authPage' => 'authPage',
     * ]
     * 'log' => [
     *      'file' => '',
     *      'level' => '',
     *      'type' => '',
     *      'max_file' => '',
     * ]
     * ]
     * @throws Exception
     */
    public function __construct($config)
    {
        $this->config = new Config($config);
        $this->registerLogService();
        $this->api = new OpenApi($this->config);

    }

    /**
     * Register log service.
     *
     * @throws Exception
     * @author yansongda <me@yansongda.cn>
     *
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

    /**
     * @param $handler
     * @param array $params
     * @return array|mixed
     */
    public function __call($handler, $params = [])
    {
        // TODO: Implement __call() method.
        try {
            return call_user_func_array([$this->api, $handler], $params);
        } catch (Exception $exception) {
            $log = [
                'errcode' => $exception->getCode(),
                'errmsg' => $exception->getCode() . ":" . $exception->getMessage()
            ];
            Log::error($handler, $log);

            throw new \Exception($log['errmsg'], $log['errcode']);

        }

    }


}