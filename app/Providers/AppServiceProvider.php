<?php

namespace App\Providers;

use App\Yantrana\Components\Vendor\Models\VendorModel;
use Illuminate\Support\ServiceProvider;
use Laravel\Cashier\Cashier;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        require app_path('Yantrana/__Laraware/Support/helpers.php');
        require app_path('Yantrana/Support/app-helpers.php');
        require app_path('Yantrana/Support/extended-validations.php');
        // config items requires gettext helper function to work
        require app_path('Yantrana/Support/custom-tech-config.php');
        require app_path('Yantrana/Support/extended-blade-directive.php');
        Cashier::useCustomerModel(VendorModel::class);
        if (getAppSettings('enable_stripe') and getAppSettings('stripe_enable_calculate_taxes')) {
            Cashier::calculateTaxes();
        }
    }
}
