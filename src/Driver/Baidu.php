<?php

namespace Huoshaotuzi\Sociate\Driver;

use Huoshaotuzi\Sociate\Handler;

class Baidu extends Handler
{
    protected $name = 'baidu';
    protected $authoriteCodeUrl = 'http://openapi.baidu.com/oauth/2.0/authorize';
    protected $authoriteTokenUrl = 'https://openapi.baidu.com/oauth/2.0/token';
    protected $userInfoUrl = 'https://openapi.baidu.com/rest/2.0/passport/users/getInfo';
}
