<?php

namespace Huoshaotuzi\Sociate;

use Huoshaotuzi\Sociate\Exception\ConfigException;

class Config
{
    /**
     * 应用key.
     *
     * @var string
     */
    private $_clientId;

    /**
     * 应用secret.
     *
     * @var string
     */
    private $_clientSecret;

    /**
     * 授权回调页地址
     *
     * @var string
     */
    private $_redirect;

    public function __construct($type)
    {
        $this->_initParams($type);
    }

    /**
     * 返回数组格式.
     *
     * @return array
     */
    public function toArray()
    {
        $configs = [
            'cliend_id' => $this->_clientId,
            'client_scret' => $this->_clientSecret,
            'redirect' => $this->_redirect,
        ];

        return $configs;
    }

    /**
     * 获取client_id.
     *
     * @return string
     */
    public function getClientId()
    {
        return $this->_clientId;
    }

    /**
     * 获取secret.
     *
     * @return string
     */
    public function getClientSecret()
    {
        return $this->_clientSecret;
    }

    /**
     * 获取redirect.
     *
     * @return string
     */
    public function getRedirect()
    {
        return $this->_redirect;
    }

    /**
     * 初始化配置参数.
     *
     * @param string $type
     */
    private function _initParams($type)
    {
        $configs = config("sociate.{$type}");
        if (empty($configs)) {
            throw new ConfigException("{$type} 参数获取失败");
        }

        $this->_clientId = $configs['client_id'];
        $this->_clientSecret = $configs['client_secret'];
        $this->_redirect = $configs['redirect'];
    }
}
