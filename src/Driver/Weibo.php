<?php

namespace Huoshaotuzi\Sociate\Driver;

use Huoshaotuzi\Sociate\Handler;
use GuzzleHttp\Client;

class Weibo extends Handler
{
    protected $name = 'weibo';
    protected $authoriteCodeUrl = 'https://api.weibo.com/oauth2/authorize';
    protected $authoriteTokenUrl = 'https://api.weibo.com/oauth2/access_token';
    protected $userInfoUrl = '';
}
