<?php

namespace Huoshaotuzi\Sociate;

use Huoshaotuzi\Sociate\Config;
use GuzzleHttp\Client;

class Handler
{
    private $_config;
    private $_client;

    /**
     * 平台名称
     * @var string
     */
    protected $name;

    /**
     * 引导登录链接
     * @var string
     */
    protected $authoriteCodeUrl;

    /**
     * access token api
     * @var string
     */
    protected $authoriteTokenUrl;

    /**
     * 用户资料api
     * @var string
     */
    protected $userInfoUrl;

    public function __construct()
    {
        $this->_client = new Client();
        $this->_config = new Config($this->name);
    }

    /**
     * 获取登录链接
     * @param string $state 自定义字段
     * @return string
     */
    public function getAuthoriteCodeUrl($state = '')
    {
        $params = [
            'client_id' => $this->_config->getClientId(),
            'response_type' => 'code',
            'redirect_uri' => $this->_config->getRedirect(),
            'state' => $state,
        ];

        return $this->authoriteCodeUrl . '?' . http_build_query($params);
    }

    /**
     * 获取用户信息
     * @return obj
     */
    public function getUser()
    {
        $code = request('code');
        $token = $this->_getAccessToken($code);
        $params = ['access_token' => $token];

        return $this->_post($this->userInfoUrl, $params);
    }

    private function _getAccessToken($code)
    {
        $params = [
            'grant_type' => 'authorization_code',
            'code' => $code,
            'client_id' => $this->_config->getClientId(),
            'client_secret' => $this->_config->getClientSecret(),
            'redirect_uri' => $this->_config->getRedirect(),
        ];

        return $this->_post($this->authoriteTokenUrl, $params);
    }

    private function _post($url, $params)
    {
        $response = $this->_client->post($url, [
            'query' => $params,
            'verify' => false,
        ]);

        return $response->getBody()->getContents();
    }
}
