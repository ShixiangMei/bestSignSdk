<?php

namespace Msx\BestSignSdk;

use Illuminate\Support\ServiceProvider;

class BestSignSdkServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
        $this->publishes([
            __DIR__.'/config/bestSignSdk.php' => config_path('bestSignSdk.php')
        ]);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
        // 单列绑定服务
        $this->app->singleton('miniapp', function ($app) {
            return new BestSignSdk($app['session'], $app['config']);
        });
    }
}
