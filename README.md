# sociate for laravel
基于 Laravel 开发的第三方登录插件,
支持 QQ、新浪微博、百度、Github、微信公众号登录（即授权获取用户信息）。
演示地址：[火兔游戏](http://huotuyouxi.com/login)
# 更新记录
- 2019-05-06 支持微信公众号登录
- 2019-02-28 支持 Github 登录

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

GITHUB_KEY=
GITHUB_SECRET=
GITHUB_REDIRECT=

WECHAT_KEY=
WECHAT_SECRET=
WECHAT_REDIRECT=
```

`*_KEY` 即 `APP_KEY`,不同平台的叫法可能不同,统称应用 ID,微信公众号为 `APPID`;
`*_SECRET` 即 `SECRET`,一串随机的字符串,应用密匙,要注意该字段不能暴露给用户;
`*_REDIRECT` 即授权回调页地址,百度与微博、微信公众号可以在配置应用自行设置, QQ 貌似不支持。

以上,配置完成。

# 流程说明
第三方登录其实就是第三方平台给你一个跳转到他们页面的链接,用户点击授权之后,第三方平台会携带一个 `code` 参数和你自定义的 `state` 字段重定向到你在应用配置的授权回调页地址,通过 `code` 换取用户的 `access_token`,再用 `access_token` 换取用户资料,最后把资料保存下来。

# 微信公众号登录（非微信网页扫码登录）
微信公众号官方文档：https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1421140842

由于微信公众号比其他平台更复杂一点，因此在这里特别进行介绍，如果是刚入门的小白则可以快速入手。
首先需要一个公众号，个人申请的订阅号无法开通获取用户的权限，因此这个公众号必须是服务号或者是已认证的订阅号。

个人开发者或者本地测试时可以申请微信测试号，测试号可以测试公众号的各个接口，测试号申请地址：
http://mp.weixin.qq.com/debug/cgi-bin/sandbox?t=sandbox/login

开发过程中一般要使用测试号进行开发，测试完毕没有问题的时候，产品上线阶段再修改配置文件的 `APP_ID` 和 `SECRET`。
开发还需要下载微信 web 开发者工具，下载地址：https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1455784140

值得一提的一点，在线上环境需要配置 IP 白名单，只有这个名单里的 IP 才允许调用接口，如果你的服务器使用了负载均衡，则需要把所有可能调用接口的机子的 IP 全部添加进去，最多允许添加 200 个。

参照官方说明。
> 通过开发者ID及密码调用获取 access_token 接口时，需要设置访问来源IP为白名单。

以上准备完毕。

公众号登录授权获取用户信息有两种，在传入 scope 参数时进行区分。
参照官方说明。

```
关于网页授权的两种scope的区别说明
1、以snsapi_base为scope发起的网页授权，是用来获取进入页面的用户的openid的，并且是静默授权并自动跳转到回调页的。用户感知的就是直接进入了回调页（往往是业务页面）
以snsapi_userinfo为scope发起的网页授权，是用来获取用户的基本信息的。但这种授权需要用户手动同意，并且由于用户同意过，所以无须关注，就可在授权后获取该用户的基本信息。
```

本插件默认使用 snsapi_userinfo 参数。

首先需要在 routes/web.php 创建两个路由地址：

```
// 根据你实际的项目设置
Route::get('/', 'TestController@test');
Route::get('/auth', 'TestController@auth');
```
我这里使用的 /auth 即为授权回调地址，在 .env 将 WECHAT_REDIRECT 设置为 http://127.0.0.1:8000/auth

在公众号测试号页面拉到底部，体验接口权限表下找到“网页帐号-网页授权获取用户基本信息”右侧点击“修改”。

授权回调页面域名:127.0.0.1:8000

点击确认进行保存。

创建路由对应控制器及方法：

```
<?php

namespace App\Http\Controllers;

use Huoshaotuzi\Sociate\Sociate;

class TestController extends Controller
{
    public function test()
    {
        $class = new Sociate();
        echo '<a href="' . $class->driver('wechat')->getVerifyUrl() . '">点击跳转</a>';
    }

    public function auth()
    {
        $class = new Sociate();
        $driver = $class->driver('wechat');
        $token = $driver->getAccessToken(request('code'));

        $user = $driver->getUserInfo($token);
        dd($token, $user);
    }
}
```
以上就完成了公众号授权获取用户信息。

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

public function github()
{
    $driver = app('sociate')->driver('github');
    $response = $driver->getAccessToken();
    $info = $driver->getUserInfo($response);

    dd($response, $info);
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
        $sociate = new Sociate();
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
