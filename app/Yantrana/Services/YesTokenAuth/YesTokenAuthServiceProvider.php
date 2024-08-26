<?php

namespace App\Yantrana\Services\YesTokenAuth;

/**
 * Service Provider for YesTokenAuth
 *-------------------------------------------------------- */

use Illuminate\Support\ServiceProvider;

class YesTokenAuthServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
    }

    /**
     * Register any application services.
     */
    public function register()
    {

        // Register 'YesTokenAuth' instance container to our YesTokenAuth object
        $this->app->singleton('YesTokenAuth', function ($app) {
            return new \App\Yantrana\Services\YesTokenAuth\YesTokenAuth();
        });

        // Register Alias
        $this->app->booting(function () {
            $loader = \Illuminate\Foundation\AliasLoader::getInstance();
            $loader->alias(
                'YesTokenAuth',
                \App\Yantrana\Services\YesTokenAuth\YesTokenAuthFacade::class
            );
        });
    }
}
