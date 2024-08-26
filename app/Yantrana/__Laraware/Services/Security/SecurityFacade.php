<?php

namespace App\Yantrana\__Laraware\Services\Security;

/**
 * Facade for Security - 10 JUL 2015
 *-------------------------------------------------------- */

use Illuminate\Support\Facades\Facade;

/**
 * Security related utilities.
 *-------------------------------------------------------------------------- */
class SecurityFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'YesSecurity';
    }
}
