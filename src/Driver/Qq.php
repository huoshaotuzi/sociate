<?php

namespace Huoshaotuzi\Sociate\Driver;

use Huoshaotuzi\Sociate\Handler;
use GuzzleHttp\Client;

class Qq extends Handler
{
    protected $name = 'qq';
    protected $authoriteCodeUrl = 'https://graph.qq.com/oauth2.0/authorize';
    protected $authoriteTokenUrl = 'https://graph.qq.com/oauth2.0/token';
    protected $userOpenIdUrl = 'https://graph.qq.com/oauth2.0/me';
    protected $userInfoUrl = 'https://graph.qq.com/user/get_user_info';

    /**
     * 获取用户信息
     * @return array
     */
    public function getUser()
    {
        $code = request('code');
        $response = $this->getAccessToken($code);
        $me = $this->getOpenId($response['access_token']);

        $params = [
            'access_token' => $response['access_token'],
            'openid' => $me['openid'],
        ];

        return $this->_get($this->userInfoUrl, $params);
    }

    /**
     * 获取openid
     *
     * @param string $token
     * @return array
     */
    public function getOpenId($token)
    {
        $params = [
            'access_token' => $token,
        ];

        return $this->_get($this->userOpenIdUrl, $params);
    }

    private function _get($url, $params)
    {
        $options = [
            'query' => $params,
            'verify' => false,
        ];

        $client = new Client();
        $response = $client->get($url, $options)->getBody()->getContents();

        return json_decode($response, true);
    }
}
