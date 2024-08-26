<?php

namespace App\Yantrana\Services\PushBroadcast;

use Exception;
use Pusher\Pusher;

/**
 * PushBroadcast
 *
 *
 *--------------------------------------------------------------------------- */

/**
 * This PushBroadcast class.
 *---------------------------------------------------------------- */
class PushBroadcast
{
    /**
     * $pusher - pusher object
     *-----------------------------------------------------------------------*/
    private $pusher = null;

    /**
     * __construct
     *-----------------------------------------------------------------------*/
    public function __construct()
    {
        /**
         * pusher details
         */
        if (getAppSettings('allow_pusher')) {
            $pusherAppId = getAppSettings('pusher_app_id');
            $pusherKey = getAppSettings('pusher_app_key');
            $pusherSecret = getAppSettings('pusher_app_secret');
            // Pusher call
            $this->pusher = new Pusher(
                $pusherKey,
                $pusherSecret,
                $pusherAppId,
                [
                    'cluster' => getAppSettings('pusher_app_cluster_key'),
                    'useTLS' => true,
                ]
            );
        }
    }

    /**
     * trigger pusher services
     *-----------------------------------------------------------------------*/
    public function trigger($channels, $event, $data)
    {
        try {
            //trigger channel event to pusher instance
            if (getAppSettings('allow_pusher')) {
                $this->pusher->trigger($channels, $event, $data);
            }
        } catch (Exception $e) {
            //log error message
            __logDebug($e->getMessage());
        }
    }

    /**
     * account trigger
     *-----------------------------------------------------------------------*/
    public function accountTrigger($event, $data)
    {
        return $this->trigger('channel-'.$data['userUid'], $event, $data);
    }

    /**
     * push via notification request
     *-----------------------------------------------------------------------*/
    public function notifyViaPusher($eventId, $data)
    {
        return $this->accountTrigger($eventId, $data);
    }
}
