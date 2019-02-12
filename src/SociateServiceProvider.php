<?php

namespace Huoshaotuzi\Sociate;

use Illuminate\Support\ServiceProvider;

class SociateServiceProvider extends ServiceProvider
{
    /**
     * 延迟加载服务器提供者
     * @var boolean
     */
    protected $defer = true;

    public function boot()
    {
        $configPath = realpath(__DIR__ . '/../config/sociate.php');

        $this->publishes($configPath, 'config');
    }

    public function register()
    {
        $configPath = realpath(__DIR__ . '/../config/sociate.php');
        $this->mergeConfigFrom($configPath, 'sociate');

        $this->app->singleton('sociate', function () {
            return new Sociate;
        });
    }

    public function provides()
    {
        return ['sociate'];
    }
}
