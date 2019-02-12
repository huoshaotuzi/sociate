<?php

namespace Huoshaotuzi\Sociate;

use GuzzleHttp\Client;

class Handler
{
    /**
     * 参数配置.
     *
     * @var object
     */
    private $_config;

    /**
     * http请求类.
     *
     * @var object
     */
    private $_client;

    /**
     * 平台名称.
     *
     * @var string
     */
    protected $name;

    /**
     * 引导登录链接.
     *
     * @var string
     */
    protected $authoriteCodeUrl;

    /**
     * access token api.
     *
     * @var string
     */
    protected $authoriteTokenUrl;

    /**
     * 用户资料api.
     *
     * @var string
     */
    protected $userInfoUrl;

    public function __construct()
    {
        $this->_client = new Client();
        $this->_config = new Config($this->name);
    }

    /**
     * 获取当前平台配置参数.
     *
     * @return object
     */
    public function getConfig()
    {
        return $this->_config;
    }

    /**
     * 获取登录链接.
     *
     * @param string $state 自定义字段
     *
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

        return $this->authoriteCodeUrl.'?'.http_build_query($params);
    }

    /**
     * 获取用户信息.
     *
     * @param string $accessToken
     *
     * @return array
     */
    public function getUser($accessToken)
    {
        $params = ['access_token' => $accessToken];

        return $this->_post($this->userInfoUrl, $params);
    }

    /**
     * 获取access token.
     *
     * @return array
     */
    public function getAccessToken()
    {
        $code = request('code');
        $params = [
            'grant_type' => 'authorization_code',
            'code' => $code,
            'client_id' => $this->_config->getClientId(),
            'client_secret' => $this->_config->getClientSecret(),
            'redirect_uri' => $this->_config->getRedirect(),
        ];

        return $this->_post($this->authoriteTokenUrl, $params);
    }

    /**
     * 基础请求方法.
     *
     * @param string $url
     * @param array  $params
     *
     * @return array
     */
    private function _post($url, $params)
    {
        $options = [
            'query' => $params,
            'verify' => false,
        ];

        $response = $this->_client->post($url, $options)->getBody()->getContents();

        return json_decode($response, true);
    }
}
