<?php
/**
 * Created by PhpStorm.
 * User: owner
 * Date: 2019-10-15
 * Time: 16:58
 * Project Name: openWeChat
 */

namespace Open\LocalTraits;


use Open\Support\Log\Log;

trait BasicProcess
{
    /**
     * 获取参数
     *
     * @param $action
     * @param $index
     * @return bool
     */
    protected function getRequestParams($action, $index)
    {
        if (isset($this->params[$index]) && ($this->params[$index] || $this->params[$index] === 0)) {
            return $this->params[$index];
        }

        Log::error($action, ['errcode' => 40001, 'errmsg' => $index . ' 参数不存在']);

        throw new \Exception($key . '=>参数不正确，请确认', 40001);

    }

    /**
     * 按需获取公共参数
     *
     * @param $params
     * @return array
     */
    public function globalParams($params)
    {
        $origin = [
            'component_appid' => $this->config->get('open.app_id'),
            'component_appsecret' => $this->config->get('open.app_secret'),
        ];

        return array_intersect_key($origin,$params);

    }

    /**
     * 参数检查
     *
     * @param $params
     * @return mixed
     * @throws \Exception
     */
    protected function checkParams($params)
    {
        $throw = false;
        foreach ($params as $key => $val) {
            switch ($key) {
                case 'appid':
                    if (!$val) {
                        $throw = true;
                    }
                    break;
                case 'redirect_uri':
                    if (!$val) {
                        $throw = true;
                    }
                    break;
                case 'response_type':
                    if (!$val) {
                        $throw = true;
                    }
                    break;
                case 'scope':
                    if (!$val || !in_array($val,['snsapi_base','snsapi_userinfo'])) {
                        $throw = true;
                    }
                    break;
                case 'secret':
                    if (!$val) {
                        $throw = true;
                    }
                    break;
                case 'code':
                    if (!$val) {
                        $throw = true;
                    }
                    break;
                case 'access_token':
                    if (!$val) {
                        $throw = true;
                    }
                    break;
                case 'sign_url':
                    if (!$val) {
                        $throw = true;
                    }
                    break;
                case 'openid':
                    if (!$val) {
                        $throw = true;
                    }
                    break;
                case 'jsapi_ticket':
                    if (!$val) {
                        $throw = true;
                    }
                    break;
            }

            if($throw) {
                if(($check = $this->checkParams($params)) !== true) {
                    throw new \Exception($key.'=>参数不正确，请确认',40001);
                }
            }
        }

        return true;
    }

}