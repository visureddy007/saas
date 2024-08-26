<?php

namespace App\Yantrana\Services\PushBroadcast;

/**
 * Service Provider for PushBroadcast
 *-------------------------------------------------------- */

use Illuminate\Support\ServiceProvider;

class PushBroadcastServiceProvider extends ServiceProvider
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
        // Register 'pushbroadcast' instance container to our PushBroadcast object
        $this->app->singleton('pushbroadcast', function ($app) {
            return new \App\Yantrana\Services\PushBroadcast\PushBroadcast();
        });

        // Register Alias
        $this->app->booting(function () {
            $loader = \Illuminate\Foundation\AliasLoader::getInstance();
            $loader->alias(
                'PushBroadcast',
                \App\Yantrana\Services\PushBroadcast\PushBroadcastFacade::class
            );
        });
    }
}
