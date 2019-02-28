<?php

namespace Huoshaotuzi\Sociate\Driver;

use Huoshaotuzi\Sociate\Driver;
use Huoshaotuzi\Sociate\Config;

class Github extends Driver
{
    protected $name = 'github';
    protected $authoriteCodeUrl = 'https://github.com/login/oauth/authorize';
    protected $authoriteTokenUrl = 'https://github.com/login/oauth/access_token';
    protected $userInfoUrl = 'https://api.github.com/user';

    public function __construct()
    {
        $this->config = new Config($this->name);
    }

    public function getAuthoriteCodeUrl($state = '')
    {
        $params = [
            'client_id' => $this->config->getClientId(),
            'scope' => $state,
        ];

        return $this->authoriteCodeUrl . '?' . http_build_query($params);
    }

    public function getUser($response)
    {
        $params = ['access_token' => $response['access_token']];
        $response = $this->request('get', $this->userInfoUrl, $params);

        return json_decode($response, true);
    }

    public function getAccessToken()
    {
        $code = request('code');
        $params = [
            'code' => $code,
            'client_id' => $this->config->getClientId(),
            'client_secret' => $this->config->getClientSecret(),
        ];
        $response = $this->request('post', $this->authoriteTokenUrl, $params);

        return $this->queryToArray($response);
    }
}
