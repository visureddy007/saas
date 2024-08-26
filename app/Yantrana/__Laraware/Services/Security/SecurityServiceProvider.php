<?php

namespace App\Yantrana\__Laraware\Services\Security;

/**
 * Service Provider for Security - 03 AUG 2015
 *-------------------------------------------------------- */

use Illuminate\Support\ServiceProvider;

class SecurityServiceProvider extends ServiceProvider
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
        // Register 'security' instance container to our Security object

        $this->app->singleton('YesSecurity', function ($app) {
            return new \App\Yantrana\__Laraware\Services\Security\Security();
        });

        // Register Alias
        $this->app->booting(function () {
            $loader = \Illuminate\Foundation\AliasLoader::getInstance();
            $loader->alias(
                'YesSecurity',
                \App\Yantrana\__Laraware\Services\Security\SecurityFacade::class
            );
        });
    }
}
