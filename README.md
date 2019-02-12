# sociate for laravel
基于Laravel开发的第三方登录插件，支持QQ、新浪微博、百度登录。

# 安装
```
composer require huoshaotuzi/sociate
```

在 `config/app.php` 注册服务器提供者:
```
/*
 * Package Service Providers...
 */
Huoshaotuzi\Sociate\SociateServiceProvider::class,
```

发布配置文件到 `config` 文件夹,这一步可以不操作:
```
php artisan vendor:publish --provider="Huoshaotuzi\Sociate\SociateServiceProvider"
```

在 `.env` 文件添加第三方应用配置信息,目前支持QQ、新浪微博、百度登录,不需要的可以不用添加:
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

以上,配置完成。

# 基本方法
`routes/web.php` 添加授权回调页路由,授权回调页的地址需要与你的应用配置一致,否则会提示 `redirect_uri` 错误:
```
// 我的回调页地址 http://xxx.com/auth/baidu
Route::namespace('Oauth')->prefix('auth')->group(function () {
    Route::get('/qq', 'OauthController@qq');
    Route::get('/weibo', 'OauthController@weibo');
    Route::get('/baidu', 'OauthController@baidu');
});
```

创建第三方登录控制器:
```
php artisan make:controller Oauth/OauthController
```

控制器添加代码:
```
public function qq()
{
    $driver = app('sociate')->driver('qq');
    $response = $driver->getAccessToken();
    $info = $driver->getUserInfo($response);

    dd($info);

}
public function weibo()
{
    $driver = app('sociate')->driver('qq');
    $response = $driver->getAccessToken();
    $info = $driver->getUserInfo($response);

    dd($info);
}
public function baidu()
{
    $driver = app('sociate')->driver('baidu');
    $response = $driver->getAccessToken();
    $info = $driver->getUserInfo($response);

    dd($info);
}
```

生成第三方登录引导链接:
```
$loginUrl = app('sociate')->driver('baidu')->getLoginUrl();
```
`driver()` 方法接收第三方平台参数,该值可以是 `qq`、`weibo`、`baidu`,不区分大小写。
`getLoginUrl($state)` 方法支持一个参数 `state` 作为返回参数，授权成功后该值会原样返回,可以作为重定向到登录前页面的地址,不传默认为空。

可以直接在视图的第三方登录按钮中,使用如下代码:
```
<a href="{{ app('sociate')->driver('baidu')->getLoginUrl(url()->full()) }}">百度登录</a>
```
在登录成功之后都可以使用 `request('state')` 获取到登录前的页面,再进行重定向跳转,增加用户体验。

# 其他方法
```
// 获取支持的所有平台
$support = app('sociate')->getSupport();
// 获取当前平台
$driver = app('sociate')->getDriver();
```

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
        $sociate = new Socialite();
        $driver = $sociate->driver('qq');
        $accessToken = $driver->getAccessToken();
        $user = $driver->getUserInfo($accessToken);
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
        $driver = app('sociate')->driver('qq');
        $accessToken = $driver->getAccessToken();
        $user = $driver->getUserInfo($accessToken);
        dd($user);
    }
}
```
