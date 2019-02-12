<?php

namespace Huoshaotuzi\Sociate;

use Illuminate\Support\ServiceProvider;

class SociateServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $configPath = realpath(__DIR__ . '/../config/sociate.php');

        $this->publishes($configPath, 'config');
    }

    public function register()
    {
        $this->app->singleton('sociate', function () {
            return new Sociate;
        });
    }
}
