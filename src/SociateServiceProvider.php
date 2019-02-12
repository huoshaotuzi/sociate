<?php

namespace Huoshaotuzi\Sociate;

use Illuminate\Support\ServiceProvider;

class SociateServiceProvider extends ServiceProvider
{
    public function boot()
    {
        //
    }

    public function register()
    {
        $this->app->singleton('sociate', function () {
            return new Sociate;
        });
    }
}
