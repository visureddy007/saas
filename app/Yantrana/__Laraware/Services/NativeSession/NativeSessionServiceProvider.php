<?php

namespace App\Yantrana\__Laraware\Services\NativeSession;

/**
 * Service Provider for NativeSession
 *-------------------------------------------------------- */

use Illuminate\Support\ServiceProvider;

class NativeSessionServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
        //
    }

    /**
     * Register any application services.
     */
    public function register()
    {
        // Register 'nativeSession' instance container to our NativeSession object

        $this->app->singleton('NativeSession', function ($app) {
            $storage = $this->app['session'];

            return new NativeSession($storage);
        });

        // Register Alias
        $this->app->booting(function () {
            $loader = \Illuminate\Foundation\AliasLoader::getInstance();
            $loader->alias('NativeSession', NativeSessionFacade::class);
        });
    }
}
