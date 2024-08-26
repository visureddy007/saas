<?php

namespace App\Yantrana\Services\YesFileStorage;

/**
 * Facade for YesFileStorage
 *-------------------------------------------------------- */

use Illuminate\Support\Facades\Facade;

/**
 * YesFileStorage.
 *-------------------------------------------------------------------------- */
class YesFileStorageFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'yesfilestorage';
    }
}
