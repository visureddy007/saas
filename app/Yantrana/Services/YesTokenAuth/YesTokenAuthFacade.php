<?php

namespace App\Yantrana\Services\YesTokenAuth;

/**
 * Facade for YesTokenAuth
 *-------------------------------------------------------- */

use Illuminate\Support\Facades\Facade;

/**
 * YesTokenAuth.
 *-------------------------------------------------------------------------- */
class YesTokenAuthFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'YesTokenAuth';
    }
}
