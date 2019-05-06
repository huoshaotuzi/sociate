<?php

namespace Huoshaotuzi\Sociate\Driver;

use Huoshaotuzi\Sociate\Config;
use Huoshaotuzi\Sociate\Driver;
use Huoshaotuzi\Sociate\Exception\DriverNotSupportException;

class Wechat extends Driver
{
    protected $name = 'wechat';
    protected $authoriteCodeUrl = 'https://open.weixin.qq.com/connect/oauth2/authorize';
    protected $authoriteTokenUrl = 'https://api.weixin.qq.com/sns/oauth2/access_token';
    protected $userInfoUrl = 'https://api.weixin.qq.com/sns/userinfo';

    public function __construct()
    {
        $this->config = new Config($this->name);
    }

    public function getVerifyCodeUrl($scope, $state)
    {
        $params = [
            'appid' => $this->config->getClientId(),
            'redirect_uri' => $this->config->getRedirect(),
            'response_type' => 'code',
            'scope' => $scope,
            'state' => $state,
        ];

        return $this->authoriteCodeUrl . '?' . http_build_query($params) . '#wechat_redirect';
    }

    public function getAuthoriteCodeUrl($state = '') {
        throw new DriverNotSupportException('微信公众号不支持调用该方法，请使用 getVerifyUrl($scope, $state)。');
    }

    public function getAccessToken()
    {
        $code = request('code');
        $params = [
            'appid' => $this->config->getClientId(),
            'secret' => $this->config->getClientSecret(),
            'code' => $code,
            'grant_type' => 'authorization_code',
        ];

        $response = $this->request('get', $this->authoriteTokenUrl, $params);

        return json_decode($response, true);
    }

    public function getUser($response)
    {
        $params = [
            'access_token' => $response['access_token'],
            'openid' => $response['openid'],
            'lang' => 'zh_CN',
        ];

        $response = $this->request('get', $this->userInfoUrl, $params);
        $info = json_decode($response, true);

        return $info;
    }
}
