<?php

namespace Huoshaotuzi\Sociate\Driver;

use Huoshaotuzi\Sociate\Driver;
use Huoshaotuzi\Sociate\Config;

class Qq extends Driver
{
    protected $name = 'qq';
    protected $authoriteCodeUrl = 'https://graph.qq.com/oauth2.0/authorize';
    protected $authoriteTokenUrl = 'https://graph.qq.com/oauth2.0/token';
    protected $userOpenIdUrl = 'https://graph.qq.com/oauth2.0/me';
    protected $userInfoUrl = 'https://graph.qq.com/user/get_user_info';

    public function __construct()
    {
        $this->config = new Config($this->name);
    }

    public function getAuthoriteCodeUrl($state = '')
    {
        $params = [
            'client_id' => $this->config->getClientId(),
            'response_type' => 'code',
            'redirect_uri' => $this->config->getRedirect(),
            'state' => $state,
        ];

        return $this->authoriteCodeUrl . '?' . http_build_query($params);
    }

    /**
     * 获取用户信息.
     *
     * @return array
     */
    public function getUser($response)
    {
        $me = $this->getOpenId($response['access_token']);

        $params = [
            'access_token' => $response['access_token'],
            'openid' => $me['openid'],
            'oauth_consumer_key' => $this->config->getClientId(),
        ];

        $response = $this->request('get', $this->userInfoUrl, $params);
        $info = json_decode($response, true);
        $info['openid'] = $me['openid'];

        return $info;
    }

    /**
     * 获取openid.
     *
     * @param string $token
     *
     * @return array
     */
    public function getOpenId($token)
    {
        $params = [
            'access_token' => $token,
            'format' => 'json',
        ];

        $response = $this->request('get', $this->userOpenIdUrl, $params);

        return $this->jsonpToArray($response);
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
            'client_id' => $this->config->getClientId(),
            'client_secret' => $this->config->getClientSecret(),
            'redirect_uri' => $this->config->getRedirect(),
        ];

        $response = $this->request('get', $this->authoriteTokenUrl, $params);

        return $this->queryToArray($response);
    }

    protected function jsonpToArray($response)
    {
        $start = strpos($response, '{');
        $end = strpos($response, '}');
        $jsonStr = substr($response, $start, $end - $start + 1);

        return json_decode($jsonStr, true);
    }
}
