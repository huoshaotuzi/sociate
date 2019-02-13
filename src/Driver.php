<?php

namespace Huoshaotuzi\Sociate;

use GuzzleHttp\Client;

abstract class Driver
{
    protected $config;

    abstract public function getAccessToken();
    abstract public function getUser($response);
    abstract public function getAuthoriteCodeUrl($state = '');

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