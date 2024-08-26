<?php

namespace App\Yantrana\Services\PushBroadcast;

/**
 * Facade for PushBroadcast
 *-------------------------------------------------------- */

use Illuminate\Support\Facades\Facade;

/**
 * PushBroadcast.
 *-------------------------------------------------------------------------- */
class PushBroadcastFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'pushbroadcast';
    }
}
