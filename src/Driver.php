<?php

namespace Huoshaotuzi\Sociate;

use Guzzle\Client;

abstract class Driver
{
    protected $config;

    abstract public function getUser($response);
    abstract public function getAccessToken();

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

    protected function request($method, $url, $params)
    {
        $options = [
            'query' => $params,
            'verify' => false,
        ];

        $client = new Client();
        $response = $client->$method($url, $options)->getBody()->getContents();

        return $response;
    }
}