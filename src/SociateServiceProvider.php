<?php

namespace Huoshaotuzi\Sociate;

use Illuminate\Support\ServiceProvider;

class SociateServiceProvider extends ServiceProvider
{
    /**
     * 延迟加载服务器提供者.
     *
     * @var bool
     */
    protected $defer = true;

    public function boot()
    {
        $configPath = $this->_getConfigPath();
        $this->publishes([$configPath => config_path('sociate.php')]);
    }

    public function register()
    {
        $configPath = $this->_getConfigPath();
        $this->mergeConfigFrom($configPath, 'sociate');

        $this->app->singleton('sociate', function () {
            return new Sociate();
        });
    }

    public function provides()
    {
        return ['sociate'];
    }

    private function _getConfigPath()
    {
        $configPath = __DIR__.'/config/sociate.php';

        return $configPath;
    }
}
