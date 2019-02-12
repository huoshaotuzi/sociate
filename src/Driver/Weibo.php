<?php

namespace Huoshaotuzi\Sociate\Driver;

use GuzzleHttp\Client;
use Huoshaotuzi\Sociate\Handler;

class Weibo extends Handler
{
    protected $name = 'weibo';
    protected $authoriteCodeUrl = 'https://api.weibo.com/oauth2/authorize';
    protected $authoriteTokenUrl = 'https://api.weibo.com/oauth2/access_token';
    protected $userInfoUrl = 'https://api.weibo.com/2/users/show.json';

    /**
     * 获取用户信息.
     *
     * @return array
     */
    public function getUser()
    {
        $code = request('code');
        $response = $this->getAccessToken($code);
        $params = [
            'access_token' => $response['access_token'],
            'uid' => $response['uid'],
        ];

        return $this->_get($this->userInfoUrl, $params);
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
