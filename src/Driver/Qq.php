<?php

namespace Huoshaotuzi\Sociate\Driver;

use GuzzleHttp\Client;
use Huoshaotuzi\Sociate\Handler;

class Qq extends Handler
{
    protected $name = 'qq';
    protected $authoriteCodeUrl = 'https://graph.qq.com/oauth2.0/authorize';
    protected $authoriteTokenUrl = 'https://graph.qq.com/oauth2.0/token';
    protected $userOpenIdUrl = 'https://graph.qq.com/oauth2.0/me';
    protected $userInfoUrl = 'https://graph.qq.com/user/get_user_info';

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
            'oauth_consumer_key' => $this->getConfig()->getClientId(),
        ];

        $response = $this->_get($this->userInfoUrl, $params);
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

        $response = $this->_get($this->userOpenIdUrl, $params);

        return $this->_jsonpToArray($response);
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
            'client_id' => $this->getConfig()->getClientId(),
            'client_secret' => $this->getConfig()->getClientSecret(),
            'redirect_uri' => $this->getConfig()->getRedirect(),
        ];

        $response = $this->_get($this->authoriteTokenUrl, $params);

        return $this->_toArray($response);
    }

    private function _jsonpToArray($response)
    {
        $start = strpos($response, '{');
        $end = strpos($response, '}');
        $jsonStr = substr($response, $start, $end - $start + 1);

        return json_decode($jsonStr, true);
    }

    private function _toArray($response)
    {
        parse_str($response, $params);

        return $params;
    }

    private function _get($url, $params)
    {
        $options = [
            'query' => $params,
            'verify' => false,
        ];

        $client = new Client();
        $response = $client->get($url, $options)->getBody()->getContents();

        return $response;
    }
}