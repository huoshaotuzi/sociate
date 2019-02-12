<?php

namespace Huoshaotuzi\Sociate;

use Huoshaotuzi\Sociate\Exception\ConfigException;

class Config
{
    /**
     * 应用ID
     * @var string
     */
    private $_clientId;

    /**
     * 应用secret
     * @var string
     */
    private $_clientSecret;

    /**
     * 授权回调页地址
     * @var string
     */
    private $_redirect;

    public function __construct($type)
    {
        $this->_initParams($type);
    }

    public function getClientId()
    {
        return $this->_clientId;
    }

    public function getClientSecret()
    {
        return $this->_clientSecret;
    }

    public function getRedirect()
    {
        return $this->_redirect;
    }

    private function _initParams($type)
    {
        $configs = config("services.{$type}");

        if (empty($configs)) {
            throw new ConfigException();
        }

        $this->_clientId = $configs['client_id'];
        $this->_clientSecret = $configs['client_secret'];
        $this->_redirect = $configs['redirect'];
    }
}
