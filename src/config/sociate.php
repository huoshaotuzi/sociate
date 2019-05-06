<?php
return [
    'qq' => [
        'client_id' => env('QQ_KEY'),
        'client_secret' => env('QQ_SECRET'),
        'redirect' => env('QQ_REDIRECT'),
    ],
    'baidu' => [
        'client_id' => env('BAIDU_KEY'),
        'client_secret' => env('BAIDU_SECRET'),
        'redirect' => env('BAIDU_REDIRECT'),
    ],
    'weibo' => [
        'client_id' => env('WEIBO_KEY'),
        'client_secret' => env('WEIBO_SECRET'),
        'redirect' => env('WEIBO_REDIRECT'),
    ],
    'github' => [
        'client_id' => env('GITHUB_KEY'),
        'client_secret' => env('GITHUB_SECRET'),
        'redirect' => env('GITHUB_REDIRECT'),
    ],
    'wechat' => [
        'client_id' => env('WECHAT_KEY'),
        'client_secret' => env('WECHAT_SECRET'),
        'redirect' => env('WECHAT_REDIRECT'),
    ],
];
