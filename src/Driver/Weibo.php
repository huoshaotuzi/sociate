<?php

namespace Huoshaotuzi\Sociate\Driver;

use GuzzleHttp\Client;
use Huoshaotuzi\Sociate\Driver;

class Weibo extends Driver
{
    protected $name = 'weibo';
    protected $authoriteCodeUrl = 'https://api.weibo.com/oauth2/authorize';
    protected $authoriteTokenUrl = 'https://api.weibo.com/oauth2/access_token';
    protected $userInfoUrl = 'https://api.weibo.com/2/users/show.json';

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
        $params = [
            'access_token' => $response['access_token'],
            'uid' => $response['uid'],
        ];

        return $this->request('get', $this->userInfoUrl, $params);
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

        return $this->request('post', $this->authoriteTokenUrl, $params);
    }
}
