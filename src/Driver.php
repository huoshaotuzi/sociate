<?php

namespace Huoshaotuzi\Sociate;

use GuzzleHttp\Client;

abstract class Driver
{
    protected $config;

    abstract public function getAccessToken();
    abstract public function getUser($response);
    abstract public function getAuthoriteCodeUrl($state = '');

    /**
     * 基础请求方法
     *
     * @param string $method
     * @param string $url
     * @param array $params
     * @return mixed
     */
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

    /**
     * 转化 query 格式字符串为数组格式
     *
     * @param string $queryStr
     * @return array
     */
    protected function queryToArray($queryStr)
    {
        parse_str($queryStr, $params);

        return $params;
    }
}
