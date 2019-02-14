# sociate for laravel
基于 Laravel 开发的第三方登录插件,支持 QQ、新浪微博、百度登录。

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

发布配置文件到 `config` 文件夹,这一步也可以不操作:
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

`_KEY` 即 `APP_KEY`,不同平台的叫法可能不同;
`_SECRET` 即 `SECRET`,一串随机的字符串,要注意该字段不能暴露给用户;
`_REDIRECT` 即授权回调页地址,百度与微博可以在配置应用自行设置, QQ 貌似不支持。

以上,配置完成。

# 流程说明
第三方登录其实就是第三方平台给你一个跳转到他们页面的链接,用户点击授权之后,第三方平台会携带一个 `code` 参数和你自定义的 `state` 字段重定向到你在应用配置的授权回调页地址,通过 `code` 换取用户的 `access_token`,再用 `access_token` 换取用户资料,最后把资料保存下来。

# 基本方法

首先需要在登录页面添加第三方登录引导链接:
```
$loginUrl = app('sociate')->driver('baidu')->getLoginUrl();
```

`driver($type)` 方法接收第三方平台参数,该值可以是 `qq`、`weibo`、`baidu`,不区分大小写(后续更新会支持更多平台)。

`getLoginUrl($state)` 方法支持一个参数 `state` 作为返回参数，授权成功后该值会原样返回,不传默认为空。

可以在登录页视图的第三方登录按钮中,使用如下代码:
```
<a href="{{ app('sociate')->driver('baidu')->getLoginUrl(url()->full()) }}">百度登录</a>
```
登录成功后使用 `request('state')` 获取到登录前的页面,再进行重定向跳转,增加用户体验。

接下来在 `routes/web.php` 添加授权回调页路由,授权回调页的地址应该与你的应用配置一致,否则会提示 `redirect_uri` 错误:
```
// 参考我的回调页地址 http://xxx.com/auth/baidu
Route::namespace('Oauth')->prefix('auth')->group(function () {
    Route::get('/qq', 'OauthController@qq');
    Route::get('/weibo', 'OauthController@weibo');
    Route::get('/baidu', 'OauthController@baidu');
});
```

> 本地开发时,回调页填写 http://127.0.0.1:8000(默认端口)

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
    // 如果需要调用到其他接口,此处需要保存 access_token
    // dd($response);
    // 此处为逻辑处理:存储用户资料或根据 uid 判断用户是否已绑定账号
    $user = ...
    // 设置为登录状态
    Auth::login($user, true);
    // 重定向到登录前页面
    return redirect(request('state'));
}

public function weibo()
{
    $driver = app('sociate')->driver('qq');
    $response = $driver->getAccessToken();
    $info = $driver->getUserInfo($response);

    dd($response, $info);
}

public function baidu()
{
    $driver = app('sociate')->driver('baidu');
    $response = $driver->getAccessToken();
    $info = $driver->getUserInfo($response);

    dd($response, $info);
}
```

值得一提的是,我们只要用到授权登录功能,因此 `access_token` 与 `refresh_token` 对我们来说没有意义,可以不用保存下来,除非后续需要调用其他接口,像读取用户微博等, `token` 的值会在获取的时候刷新,如果要用到的话记得更新记录。

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

如果不想使用 `use` 引入,或在视图不方便使用命名空间,则可使用 `Laravel` 提供的辅助方法 `app()` 来调用,比较推荐用这种方法。
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
