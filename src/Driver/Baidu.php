<?php

namespace Huoshaotuzi\Sociate\Driver;

use Huoshaotuzi\Sociate\Driver;
use Huoshaotuzi\Sociate\Config;

class Baidu extends Driver
{
    protected $name = 'baidu';
    protected $authoriteCodeUrl = 'http://openapi.baidu.com/oauth/2.0/authorize';
    protected $authoriteTokenUrl = 'https://openapi.baidu.com/oauth/2.0/token';
    protected $userInfoUrl = 'https://openapi.baidu.com/rest/2.0/passport/users/getInfo';

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

    public function getUser($response)
    {
        $params = ['access_token' => $response['access_token']];
        $response = $this->request('post', $this->userInfoUrl, $params);

        return json_decode($response, true);
    }

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
        $response = $this->request('post', $this->authoriteTokenUrl, $params);

        return json_decode($response, true);
    }
}
