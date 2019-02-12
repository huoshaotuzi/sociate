<?php

namespace Huoshaotuzi\Sociate;

use Huoshaotuzi\Sociate\Exception\DriverNotSupportException;
use Huoshaotuzi\Sociate\Exception\DriverNullException;

class Sociate
{
    /**
     * 支持的平台类型.
     *
     * @var array
     */
    private $_support = ['qq', 'weibo', 'baidu'];

    /**
     * 平台名称.
     *
     * @var string
     */
    private $_driver;

    /**
     * 类对象
     *
     * @var \Huoshaotuzi\Sociate\Handler
     */
    private $_class;

    /**
     * 设置平台.
     *
     * @param string $driver
     */
    public function driver($driver)
    {
        $className = 'Huoshaotuzi\\Sociate\\Driver\\'.ucwords(strtolower($driver));
        $this->_driver = $driver;

        $this->_checkDriverSupport();

        $this->_class = new $className();

        return $this;
    }

    /**
     * 获取支持的所有平台.
     *
     * @return array
     */
    public function getSupport()
    {
        return $this->_support;
    }

    /**
     * 获取当前平台名称.
     *
     * @return string
     */
    public function getDriver()
    {
        return $this->_driver;
    }

    /**
     * 获取平台引导登录链接.
     *
     * @return string
     */
    public function getLoginUrl($state = '')
    {
        $this->_checkDriver();

        return $this->_class->getAuthoriteCodeUrl($state);
    }

    /**
     * 获取平台用户信息.
     *
     * @return
     */
    public function getUserInfo()
    {
        $this->_checkDriver();

        return $this->_class->getUser();
    }

    /**
     * 检验driver参数.
     *
     * @throws Exception
     */
    private function _checkDriver()
    {
        if (empty($this->_driver)) {
            throw new DriverNullException('未设置平台,请先调用driver()方法传入平台参数');
        }

        $this->_checkDriverSupport();
    }

    /**
     * 检验平台是否支持
     *
     * @throws Exception
     */
    private function _checkDriverSupport()
    {
        $driver = strtolower($this->_driver);

        if (!in_array($driver, $this->_support)) {
            throw new DriverNotSupportException("暂不支持 {$driver} 平台,使用getSupport()方法可查询所有支持平台");
        }
    }
}
