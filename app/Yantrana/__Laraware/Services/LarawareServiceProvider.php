<?php

namespace App\Yantrana\__Laraware\Services;

use Illuminate\Support\ServiceProvider;

class LarawareServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
        $this->mergeConfigFrom(
            __DIR__.'/../Config/laraware.php', 'laraware'
        );
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //Helpers & other additional resources from Angulara & __Laraware
        require app_path('Yantrana/__Laraware/Support/helpers.php');
    }
}
