# sociate for laravel
基于Laravel开发的第三方登录插件，支持QQ、新浪微博、百度登录。

# 安装
```
composer require huoshaotuzi/sociate
```

然后在 `config/app.php` 注册服务器提供者,
```
Huoshaotuzi\Sociate\SociateServiceProvider::class,
```

在 `.env` 文件添加第三方应用配置信息,目前支持QQ、新浪微博、百度登录,不需要的可以不用添加。
```
QQ_KEY=
QQ_SECRET=
QQ_REDIRECT_URI=

BAIDU_KEY=
BAIDU_SECRET=
BAIDU_REDIRECT=

WEIBO_KEY=
WEIBO_SECRET=
WEIBO_REDIRECT=
```

然后在 `config/services.php` 添加获取配置参数,
```
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
```

以上,配置完成。

# 基本方法
生成第三方登录引导链接:
```
$loginUrl = app('sociate')->driver('qq')->getLoginUrl();
```
`driver()` 方法接收第三方平台参数,该值可以是 `qq`、`weibo`、`baidu`,不区分大小写。
`getLoginUrl($state)` 方法支持一个参数 `state` 作为返回参数，授权成功后该值会原样返回,因此可以作为重定向到登录前页面的地址，如果不传该值则默认为空。

你可以直接在视图的第三方登录按钮中,使用如下代码:
```
<a href="{{ app('sociate')->driver('qq')->getLoginUrl(url()->full()) }}">QQ登录</a>
```
这样,不管用户从哪个页面进行登录,在登录成功之后都可以使用 `request('state')` 获取到登录前的页面,再进行重定向跳转,可以更好的增加用户体验。

# 在控制器中使用
```
<?php

namespace App\Http\Controllers\Oauth;

use App\Http\Controllers\Controller;
use Huoshaotuzi\Sociate\Sociate;

class OauthController extends Controller
{
    // 授权回调页路由的方法
    public function qq()
    {
        $user = Socialite::driver('qq')->getUserInfo();
        dd($user);
    }
}
```

如果不想使用 `use` 引入,或者在视图里面不方便使用命名空间,则可以使用 `Laravel` 提供的辅助方法 `app()` 来调用,比较推荐用这种方法。
```
namespace App\Http\Controllers\Oauth;

use App\Http\Controllers\Controller;

class OauthController extends Controller
{
    public function qq()
    {
        $user = app('sociate')->driver('qq')->getUserInfo();
        dd($user);
    }
}
```
